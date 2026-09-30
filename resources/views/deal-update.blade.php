<!DOCTYPE html>
<html>
<head>
<title>Update Deal</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

<div class="container">
<h2>Update Deal</h2>
<hr>
@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
<form action="{{ url('/deals/'.$deal->id) }}" method="POST">
@csrf
@method('PUT')

<div class="form-group">
  <label>Status</label>
  @php $status = old('status', $deal->status ?? 'Negotiating'); @endphp
  <select name="status" class="form-control">
    <option value="Negotiating" {{ $status === 'Negotiating' ? 'selected' : '' }}>Negotiating</option>
    <option value="Offer Made" {{ $status === 'Offer Made' ? 'selected' : '' }}>Offer Made</option>
    <option value="Accepted" {{ $status === 'Accepted' ? 'selected' : '' }}>Accepted</option>
    <option value="Completed" {{ $status === 'Completed' ? 'selected' : '' }}>Completed</option>
  </select>
</div>

<div class="form-group">
  <label>Payment Status</label>
  @php $paymentStatus = old('payment_status', $deal->payment_status ?? 'Pending'); @endphp
  <select name="payment_status" class="form-control">
    <option value="Pending" {{ $paymentStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
    <option value="Paid" {{ $paymentStatus === 'Paid' ? 'selected' : '' }}>Paid</option>
    <option value="Partial" {{ $paymentStatus === 'Partial' ? 'selected' : '' }}>Partial</option>
  </select>
</div>

<div class="form-group">
  <label>Notes</label>
  <textarea name="notes" class="form-control" rows="3">{{ old('notes', $deal->notes ?? '') }}</textarea>
</div>

<div class="form-group">
  <label>Commission Percentage (%)</label>
  <input type="number" step="0.01" name="commission_percentage" class="form-control" value="{{ old('commission_percentage', $deal->commission_percentage ?? '') }}" min="0" max="100">
</div>

<button type="submit" class="btn btn-success">Update</button>

</form>

</div>

</body>
</html>