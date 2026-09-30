<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billing</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

</head>

<body>

<!--  TOP HEADER -->
<nav class="navbar navbar-default">
  <div class="container">

    <div class="navbar-header">
      <p class="navbar-brand" style="color:black;"><strong>Real Estate CRM</strong></p>
    </div>

    <ul class="nav navbar-nav navbar-right">
      <li><a href="#">Welcome, {{ Auth::user()->name }}</a></li>
      <li><a href="{{ url('/') }}">Logout</a></li>
    </ul>

  </div>
</nav>

<!-- 🔹 MAIN NAV -->
<nav class="navbar navbar-default">
  <div class="container">

    <div class="navbar-header">
      <button class="navbar-toggle" data-toggle="collapse" data-target="#mainNav">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <div class="collapse navbar-collapse" id="mainNav">
      @include('partials.main-nav')
    </div>

  </div>
</nav>

<div class="container">

<h2>Billing & Commission</h2>
<hr>

@if(session('success'))
<div class="alert alert-success">
    <span class="glyphicon glyphicon-ok-sign"></span> {{ session('success') }}
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning">
    <span class="glyphicon glyphicon-info-sign"></span> {{ session('warning') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <span class="glyphicon glyphicon-remove-sign"></span> {{ session('error') }}
</div>
@endif

@if(isset($editingBilling))
<div class="panel panel-default">
  <div class="panel-heading">
    <h4>Edit Billing Record</h4>
  </div>
  <div class="panel-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php
      $totalAmount = floatval($editingBilling->payment_amount);
      $alreadyPaid = floatval($editingBilling->advance_amount ?? 0) + floatval($editingBilling->final_amount ?? 0);
      $remainingDue = floatval($editingBilling->due_amount ?? $editingBilling->payment_amount);
    @endphp
    
    <div class="well well-sm" style="margin-bottom: 20px;">
      <div class="row">
        <div class="col-sm-4 text-center">
          <strong>Total Amount:</strong> <span class="text-primary" style="font-size: 16px;">₹{{ number_format($totalAmount, 2) }}</span>
        </div>
        <div class="col-sm-4 text-center">
          <strong>Already Paid:</strong> <span class="text-success" style="font-size: 16px;">₹{{ number_format($alreadyPaid, 2) }}</span>
        </div>
        <div class="col-sm-4 text-center">
          <strong>Remaining Due:</strong> <span class="text-danger" style="font-size: 16px;">₹{{ number_format($remainingDue, 2) }}</span>
        </div>
      </div>
    </div>

    <form action="{{ url('/billings/'.$editingBilling->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="row">
        <div class="col-md-6 form-group">
          <label>Payment Status</label>
          <select name="payment_status" class="form-control">
            @php $status = old('payment_status', $editingBilling->payment_status); @endphp
            <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Partial" {{ $status === 'Partial' ? 'selected' : '' }}>Partial</option>
            <option value="Paid" {{ $status === 'Paid' ? 'selected' : '' }}>Paid</option>
            <option value="Overdue" {{ $status === 'Overdue' ? 'selected' : '' }}>Overdue</option>
          </select>
        </div>
        <div class="col-md-6 form-group">
          <label>Payment Method</label>
          <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', $editingBilling->payment_method) }}">
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6 form-group">
          <label>Invoice Number</label>
          <input type="text" class="form-control" value="{{ $editingBilling->invoice_number }}" disabled>
        </div>
        <div class="col-md-6 form-group">
          <label>Additional Payment Amount</label>
          <input type="number" name="additional_payment" class="form-control" step="0.01" min="0" max="{{ $remainingDue }}" value="{{ old('additional_payment') }}" placeholder="Enter amount to pay (Max: ₹{{ number_format($remainingDue, 2) }})">
        </div>
      </div>

      <div class="form-group">
        <label>Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $editingBilling->notes) }}</textarea>
      </div>
      <div class="text-right">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ url('/billing') }}" class="btn btn-default">Cancel</a>
      </div>
    </form>
  </div>
</div>
<hr>
@endif

<form method="GET" action="{{ url('/billing') }}">
  @if(request('exp_search')) <input type="hidden" name="exp_search" value="{{ request('exp_search') }}"> @endif
  @if(request('exp_category')) <input type="hidden" name="exp_category" value="{{ request('exp_category') }}"> @endif

  <div class="row" style="margin-bottom:20px;">
    <div class="col-md-6">
      <input type="text" name="search" class="form-control" placeholder="Search invoices..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-control" onchange="this.form.submit()">
        <option value="">Filter by Payment Status</option>
        <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
        <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
      </select>
    </div>
    <div class="col-md-3">
      <a href="{{ url('/billing/create') }}" class="btn btn-primary btn-block">+ Add New Invoice</a>
    </div>
  </div>
</form>

<!-- STATS -->
<div class="row text-center">

  <div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <h2>₹{{ number_format($totalRevenue, 0) }}</h2>
        <p>Total Revenue</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <h2>₹{{ number_format($amountPaid, 0) }}</h2>
        <p>Amount Paid</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <h2>₹{{ number_format($pendingAmount, 0) }}</h2>
        <p>Pending Amount</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <h2>₹{{ number_format($totalCommission, 0) }}</h2>
        <p>Total Commission</p>
      </div>
    </div>
  </div>

</div>

<div class="row text-center" style="margin-top: 10px; margin-bottom: 20px;">
  <div class="col-md-6">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #d9534f;">
        <h2>₹{{ number_format($totalExpenses, 0) }}</h2>
        <p><strong>Total Expenses</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #5cb85c;">
        <h2>₹{{ number_format($profit, 0) }}</h2>
        <p><strong>Net Profit (Revenue - Expenses)</strong></p>
      </div>
    </div>
  </div>
</div>

<!-- INVOICES -->
<h3>Recent Invoices</h3>

<div class="row">
@forelse($recentBillings as $billing)

    @php
        $statusClass = 'label-default';

        if (!empty($billing->payment_status)) {

            if ($billing->payment_status == 'Paid') {
                $statusClass = 'label-success';
            }

            elseif ($billing->payment_status == 'Pending') {
                $statusClass = 'label-warning';
            }

            elseif ($billing->payment_status == 'Overdue') {
                $statusClass = 'label-danger';
            }
        }
    @endphp
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-body">

          <h4><b>{{ $billing->invoice_number }}</b> <span class="pull-right text-success">{{ $billing->payment_amount }}</span></h4>

          <p><b>Property:</b> {{ $billing->property_name }}</p>
          <p><b>Client:</b> {{ $billing->client_name }}</p>
          <p><b>Agent:</b> {{ $billing->agent_name ?? 'N/A' }}</p>
          <p><b>Invoice:</b> {{ $billing->invoice_number }}</p>
          <p><b>Issue Date:</b> {{ optional($billing->payment_date)->format('Y-m-d') }}</p>
          <p><b>Due Date:</b> {{ optional($billing->due_date)->format('Y-m-d') }}</p>

          <p><b>Status:</b> <span class="label {{ $statusClass }}">{{ $billing->payment_status }}</span></p>

        </div>
      </div>
    </div>
  @empty
    <div class="col-md-12">
      <div class="alert alert-info text-center">No billing records found.</div>
    </div>
  @endforelse
</div>

<!-- COMMISSION TABLE -->
<h3>Commission Breakdown by Agent</h3>

<div class="table-responsive">
<table class="table table-bordered table-striped">

<thead>
<tr>
<th>Agent</th>
<th>Property</th>
<th>Sale Amount</th>
<th>Commission</th>
<th>Status</th>
<th>Due Date</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
@forelse($billings as $billing)
  @php
    $statusClass = 'label-default';
    if ($billing->payment_status === 'Paid') $statusClass = 'label-success';
    if ($billing->payment_status === 'Pending') $statusClass = 'label-warning';
    if ($billing->payment_status === 'Partial') $statusClass = 'label-info';
    if ($billing->payment_status === 'Overdue') $statusClass = 'label-danger';
  @endphp
  <tr>
    <td>{{ $billing->agent_name ?? 'N/A' }}</td>
    <td>{{ $billing->property_name }}</td>
    <td>₹{{ number_format(floatval($billing->payment_amount), 0) }}</td>
    <td>₹{{ number_format(floatval($billing->commission), 0) }}</td>
    <td><span class="label {{ $statusClass }}">{{ $billing->payment_status }}</span></td>
    <td>{{ optional($billing->due_date)->format('Y-m-d') }}</td>
    <td>
      <a href="{{ url('/billing/'.$billing->id.'/send-email') }}" class="btn btn-primary btn-sm" title="Email Invoice"><span class="glyphicon glyphicon-envelope"></span> Email</a>
      <a href="{{ url('/billings/'.$billing->id.'/edit') }}" class="btn btn-info btn-sm">Edit</a>
      <form action="{{ url('/billings/'.$billing->id) }}" method="POST" style="display:inline; margin:0; padding:0;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this invoice?')">Delete</button>
      </form>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="text-center">No billing records found.</td>
  </tr>
@endforelse
</tbody>
</table>
</div>

<!-- PAYMENT TRACKING TABLE -->
<h3>Advance & Final Payment Tracking</h3>

<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead>
<tr style="background-color: #f9f9f9;">
<th>Client</th>
<th>Property</th>
<th>Total Deal Amount</th>
<th>Advance Amount</th>
<th>Final Amount</th>
<th>Due Amount</th>
<th>Payment Status</th>
</tr>
</thead>
<tbody>
@forelse($billings as $billTrack)
  @php
    $tStatus = strtolower($billTrack->payment_status ?? 'pending');
    $tLabelClass = 'label-warning';
    if ($tStatus === 'paid') $tLabelClass = 'label-success';
    elseif ($tStatus === 'partial') $tLabelClass = 'label-info';
    elseif ($tStatus === 'overdue') $tLabelClass = 'label-danger';
  @endphp
  <tr>
    <td>{{ $billTrack->client_name }}</td>
    <td>{{ $billTrack->property_name }}</td>
    <td>₹{{ number_format(floatval($billTrack->payment_amount), 0) }}</td>
    <td>₹{{ number_format(floatval($billTrack->advance_amount ?? 0), 0) }}</td>
    <td>₹{{ number_format(floatval($billTrack->final_amount ?? 0), 0) }}</td>
    <td><strong class="{{ $tStatus !== 'paid' ? 'text-danger' : 'text-success' }}">₹{{ number_format(floatval($billTrack->due_amount ?? $billTrack->payment_amount), 0) }}</strong></td>
    <td><span class="label {{ $tLabelClass }}">{{ ucfirst($tStatus) }}</span></td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="text-center">No payment tracking records found.</td>
  </tr>
@endforelse
</tbody>
</table>
</div>

<div class="text-center">
  {{ $billings->appends(request()->query())->links() }}
</div>

<hr>

<!-- 🔹 PAYMENT RECONCILIATION -->
<h3>Payment Reconciliation</h3>
<div class="table-responsive">
<table class="table table-bordered table-striped table-hover">
<thead>
<tr style="background-color: #f9f9f9;">
<th>Invoice Number</th>
<th>Client</th>
<th>Property</th>
<th>Total Amount</th>
<th>Total Paid</th>
<th>Due Amount</th>
<th>Payment Status</th>
<th>Reconciliation Status</th>
</tr>
</thead>
<tbody>
@forelse($billings as $billingItem)
  @php
    $adv = floatval($billingItem->advance_amount ?? 0);
    $fin = floatval($billingItem->final_amount ?? 0);
    $tPaid = $adv + $fin;
    $dueAmt = floatval($billingItem->due_amount ?? $billingItem->payment_amount);
    
    if ($dueAmt <= 0) {
        $reconStatus = 'Reconciled';
        $reconClass = 'label-success';
    } elseif ($tPaid > 0) {
        $reconStatus = 'Partial';
        $reconClass = 'label-info';
    } else {
        $reconStatus = 'Pending';
        $reconClass = 'label-warning';
    }

    $pStatus = strtolower($billingItem->payment_status ?? 'pending');
    $pLabelClass = 'label-warning';
    if ($pStatus === 'paid') $pLabelClass = 'label-success';
    elseif ($pStatus === 'partial') $pLabelClass = 'label-info';
    elseif ($pStatus === 'overdue') $pLabelClass = 'label-danger';
  @endphp
  <tr>
    <td><strong>{{ $billingItem->invoice_number }}</strong></td>
    <td>{{ $billingItem->client_name }}</td>
    <td>{{ $billingItem->property_name }}</td>
    <td>₹{{ number_format(floatval($billingItem->payment_amount), 0) }}</td>
    <td>₹{{ number_format($tPaid, 0) }}</td>
    <td><strong class="{{ $dueAmt > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($dueAmt, 0) }}</strong></td>
    <td><span class="label {{ $pLabelClass }}">{{ ucfirst($pStatus) }}</span></td>
    <td><span class="label {{ $reconClass }}">{{ $reconStatus }}</span></td>
  </tr>
@empty
  <tr>
    <td colspan="8" class="text-center">No payment reconciliation records found.</td>
  </tr>
@endforelse
</tbody>
</table>
</div>

<hr>

<!-- 🔹 EXPENSE TRACKING -->
<div class="row" style="margin-top: 20px; margin-bottom: 10px;">
  <div class="col-md-12">
    <h3>Expense Tracking</h3>
  </div>
</div>

<form method="GET" action="{{ url('/billing') }}" style="margin-bottom: 15px;">
  @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
  @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

  <div class="row">
    <div class="col-md-6">
      <input type="text" name="exp_search" class="form-control" placeholder="Search title or category..." value="{{ request('exp_search') }}">
    </div>
    <div class="col-md-3">
      <select name="exp_category" class="form-control" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <option value="Marketing" {{ request('exp_category') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
        <option value="Travel" {{ request('exp_category') === 'Travel' ? 'selected' : '' }}>Travel</option>
        <option value="Maintenance" {{ request('exp_category') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
        <option value="Office" {{ request('exp_category') === 'Office' ? 'selected' : '' }}>Office</option>
      </select>
    </div>
    <div class="col-md-3">
      <a href="{{ url('/billing/expenses/create') }}" class="btn btn-warning btn-block"><strong>+ Record Expense</strong></a>
    </div>
  </div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-striped table-hover">
<thead>
<tr style="background-color: #f9f9f9;">
<th>Expense Title</th>
<th>Category</th>
<th>Amount</th>
<th>Expense Date</th>
<th>Created By</th>
<th>Notes</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
@forelse($expenses as $expense)
  @php
    $catClass = 'label-default';
    if ($expense->expense_category === 'Marketing') $catClass = 'label-primary';
    elseif ($expense->expense_category === 'Travel') $catClass = 'label-info';
    elseif ($expense->expense_category === 'Maintenance') $catClass = 'label-warning';
    elseif ($expense->expense_category === 'Office') $catClass = 'label-success';
    elseif ($expense->expense_category === 'Office Supplies') $catClass = 'label-success';
    elseif ($expense->expense_category === 'Legal') $catClass = 'label-warning';
    elseif ($expense->expense_category === 'Brokerage') $catClass = 'label-danger';
    elseif ($expense->expense_category === 'Salaries') $catClass = 'label-primary';
  @endphp
  <tr>
    <td><strong>{{ $expense->expense_title }}</strong></td>
    <td><span class="label {{ $catClass }}">{{ $expense->expense_category }}</span></td>
    <td><strong class="text-danger">₹{{ number_format($expense->amount, 2) }}</strong></td>
    <td>{{ optional($expense->expense_date)->format('Y-m-d') }}</td>
    <td>{{ $expense->created_by }}</td>
    <td><span class="text-muted">{{ \Illuminate\Support\Str::limit($expense->notes, 60) }}</span></td>
    <td>
      <a href="{{ url('/billing/expenses/edit/'.$expense->id) }}" class="btn btn-info btn-sm">Edit</a>
      <form action="{{ url('/billing/expenses/delete/'.$expense->id) }}" method="POST" style="display:inline; margin:0; padding:0;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this expense?')">Delete</button>
      </form>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="text-center">No expense records found.</td>
  </tr>
@endforelse
</tbody>
</table>
</div>

<div class="text-center" style="margin-bottom: 20px;">
  {{ $expenses->appends(request()->query())->links() }}
</div>

<!-- 🔹 FOOTER -->
<footer class="container">
  <hr>

  <div class="row">

    <div class="col-md-4">
      <h4>Company Information</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/about') }}">About Us</a></li>
        <li><a href="{{ url('/contact') }}">Contact Us</a></li>
        <li><a href="{{ url('/privacy') }}">Privacy Policy</a></li>
      </ul>
    </div>

    <div class="col-md-4">
      <h4>CRM Modules</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/properties') }}">Properties</a></li>
        <li><a href="{{ url('/clients') }}">Clients</a></li>
        <li><a href="{{ url('/leads') }}">Leads</a></li>
      </ul>
    </div>

    <div class="col-md-4">
      <h4>Quick Links</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
        <li><a href="{{ url('/support') }}">Support</a></li>
      </ul>
    </div>

  </div>

  <p class="text-center">&copy; 2026 Real Estate CRM</p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>
