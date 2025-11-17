@extends('layouts.bootstrap')

@section('content')

<div class="container mt-4">
    <div class="row">
        
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-4 text-center">My Expenses</h2>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    
                    <form action="{{ route('expenses.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-3">
                            
                            <div class="col-md-3">
                                <input type="text" name="name" class="form-control" placeholder="Name" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="category" class="form-control" placeholder="Category" required>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="amount" class="form-control" placeholder="Amount" required step="0.01">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100">Add Expense</button>
                            </div>
                        </div>
                    </form>

                    <!-- Chart -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div id="expenseChart"></div>
                        </div>
                    </div>

                   <h4>All Expenses</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr> 
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $expense)
                                    <tr id="expense-row-{{ $expense->id }}">
                                       
                                        <td>
                                            <span class="view-mode">{{ $expense->name }}</span>
                                            <input type="name" class="edit-mode form-control" name="name" value="{{ $expense->name }}" style="display: none;">
                                        </td>
                                        <td>
                                            <span class="view-mode">{{ $expense->category }}</span>
                                            <input type="text" class="edit-mode form-control" name="category" value="{{ $expense->category }}" style="display: none;">
                                        </td>
                                        <td>
                                            <span class="view-mode">{{ $expense->date }}</span>
                                            <input type="date" class="edit-mode form-control" name="date" value="{{ $expense->date }}" style="display: none;">
                                        </td>
                                        
                                        <td>
                                            <span class="view-mode">{{ $expense->amount }}</span>
                                            <input type="number" step="0.01" class="edit-mode form-control" name="amount" value="{{ $expense->amount }}" style="display: none;">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-btn">Edit</button>
                                            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="edit-form d-inline" style="display: none;">
                                                @csrf
                                                @method('PUT') 
                                                <input type="hidden" name="name">
                                                <input type="hidden" name="date">
                                                <input type="hidden" name="category">
                                                <input type="hidden" name="amount">
                                                <button type="submit" class="btn btn-sm btn-success save-btn">Save</button>
                                            </form>

                                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this expense?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No expenses yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

      
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">👤 Your Profile</div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    const chartData = @json($expenses->groupBy('category')->map->sum('amount'));
    Highcharts.chart('expenseChart', {
        chart: { type: 'pie' },
        title: { text: 'Expenses by Category' },
        series: [{
            name: 'Total Spent',
            colorByPoint: true,
            data: Object.entries(chartData).map(([key, val]) => ({
                name: key,
                y: val
            }))
        }]
    });

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
            row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'inline-block');
            row.querySelector('.edit-form').style.display = 'inline-block';
            this.style.display = 'none';

            const form = row.querySelector('.edit-form');
            form.querySelector('input[name="name"]').value = row.querySelector('input[name="name"]').value;
            form.querySelector('input[name="date"]').value = row.querySelector('input[name="date"]').value;
            form.querySelector('input[name="category"]').value = row.querySelector('input[name="category"]').value;
            form.querySelector('input[name="amount"]').value = row.querySelector('input[name="amount"]').value;

            form.addEventListener('submit', function () {
                form.querySelector('input[name="name"]').value = row.querySelector('input[name="name"]').value;
                form.querySelector('input[name="date"]').value = row.querySelector('input[name="date"]').value;
                form.querySelector('input[name="category"]').value = row.querySelector('input[name="category"]').value;
                form.querySelector('input[name="amount"]').value = row.querySelector('input[name="amount"]').value;
            });
        });
    });
</script>
@endsection
