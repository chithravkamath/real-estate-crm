<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Deal Details - CRM</title>

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

<!-- TITLE -->
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h2>Deal Profile: {{ $property ?? 'Skyline Towers' }}</h2>
    </div>
    <div class="col-md-4 text-right" style="margin-top: 20px;">
      <a href="{{ url('/deals') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back to Deals</a>
      @if(isset($deal))
        <a href="{{ url('/deal-update/'.$deal->id) }}" class="btn btn-info"><span class="glyphicon glyphicon-pencil"></span> Edit Deal</a>
      @endif
    </div>
  </div>
  <hr>
</div>

<!-- SUCCESS/WARNING MESSAGES -->
<div class="container">
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
</div>

<div class="container">
  <div class="row">
    
    <!-- LEFT COLUMN: DEAL INFORMATION -->
    <div class="col-md-5">
      
      <div class="panel panel-primary">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-briefcase"></span> <strong>Deal Information</strong>
        </div>
        <div class="panel-body">
          <table class="table table-striped" style="margin-bottom: 0;">
            <tr>
              <th style="border-top: 0; width: 40%;">Status</th>
              <td style="border-top: 0;">
                <span class="label {{ strtolower($status) === 'completed' ? 'label-success' : (strtolower($status) === 'negotiating' ? 'label-warning' : 'label-info') }}">
                  {{ $status ?? 'Negotiating' }}
                </span>
              </td>
            </tr>
            <tr>
              <th>Client Name</th>
              <td>{{ $client ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th>Property Name</th>
              <td>{{ $property ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th>Deal Amount</th>
              <td><strong>₹{{ number_format(floatval(preg_replace('/[^0-9\.]/', '', $amount)), 0) }}</strong></td>
            </tr>
            <tr>
              <th>Payment Status</th>
              <td>
                <span class="label {{ strtolower($payment_status) === 'paid' ? 'label-success' : 'label-danger' }}">
                  {{ $payment_status ?? 'Pending' }}
                </span>
              </td>
            </tr>
            @if(isset($deal))
              @if($deal->agent_name)
                <tr>
                  <th>Assigned Agent</th>
                  <td>{{ $deal->agent_name }}</td>
                </tr>
              @endif
              @if($deal->booking_date)
                <tr>
                  <th>Booking Date</th>
                  <td>{{ $deal->booking_date ? $deal->booking_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
              @endif
              @if($deal->commission_amount)
                <tr>
                  <th>Commission Amount</th>
                  <td>₹{{ number_format($deal->commission_amount, 0) }} ({{ $deal->commission_percentage }}%)</td>
                </tr>
              @endif
            @endif
          </table>
          
          @if($notes)
            <div style="margin-top: 15px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #337ab7;">
              <strong>Deal Notes:</strong>
              <p style="margin: 5px 0 0 0; font-style: italic; color: #555;">{{ $notes }}</p>
            </div>
          @endif
        </div>
      </div>

      <!-- BOOKING & SALE INFORMATION -->
      @if(isset($deal))
        <div class="panel panel-default" style="margin-top: 20px;">
          <div class="panel-heading">
            <span class="glyphicon glyphicon-ok-sign"></span> <strong>Booking & Sale Confirmation</strong>
          </div>
          <div class="panel-body">
            
            <!-- BOOKING STATUS -->
            <div style="margin-bottom: 20px; padding: 12px; background-color: #fafafa; border: 1px solid #e3e3e3; border-radius: 4px;">
              <h5 style="margin-top: 0; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                Booking Confirmation
              </h5>
              <div class="row">
                <div class="col-xs-6">
                  <strong>Status:</strong>
                  @php
                    $bStatus = $deal->booking_status ?? 'pending';
                    $bLabelClass = 'label-warning';
                    if ($bStatus === 'confirmed') $bLabelClass = 'label-success';
                    elseif ($bStatus === 'cancelled') $bLabelClass = 'label-danger';
                  @endphp
                  <span class="label {{ $bLabelClass }}">{{ ucfirst($bStatus) }}</span>
                </div>
                <div class="col-xs-6 text-right" style="font-size: 11px; color: #777;">
                  @if($deal->booking_confirmed_at)
                    <span class="glyphicon glyphicon-calendar"></span> {{ $deal->booking_confirmed_at->format('Y-m-d H:i') }}
                  @else
                    Not confirmed yet
                  @endif
                </div>
              </div>
              <div class="text-right" style="margin-top: 10px;">
                @if($bStatus !== 'confirmed')
                  <form action="{{ url('/deals/'.$deal->id.'/confirm-booking') }}" method="POST" style="display:inline-block; margin-right: 5px;">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                      <span class="glyphicon glyphicon-ok"></span> Confirm Booking
                    </button>
                  </form>
                @endif
                @if($bStatus !== 'cancelled')
                  <form action="{{ url('/deals/'.$deal->id.'/cancel-booking') }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this booking?');">
                      <span class="glyphicon glyphicon-remove"></span> Cancel Booking
                    </button>
                  </form>
                @endif
              </div>
            </div>

            <!-- SALE STATUS -->
            <div style="padding: 12px; background-color: #fafafa; border: 1px solid #e3e3e3; border-radius: 4px;">
              <h5 style="margin-top: 0; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                Sale Confirmation
              </h5>
              <div class="row">
                <div class="col-xs-6">
                  <strong>Status:</strong>
                  @php
                    $sStatus = $deal->sale_status ?? 'pending';
                    $sLabelClass = 'label-warning';
                    if ($sStatus === 'completed') $sLabelClass = 'label-success';
                    elseif ($sStatus === 'cancelled') $sLabelClass = 'label-danger';
                  @endphp
                  <span class="label {{ $sLabelClass }}">{{ ucfirst($sStatus === 'completed' ? 'Completed' : $sStatus) }}</span>
                </div>
                <div class="col-xs-6 text-right" style="font-size: 11px; color: #777;">
                  @if($deal->sale_confirmed_at)
                    <span class="glyphicon glyphicon-calendar"></span> {{ $deal->sale_confirmed_at->format('Y-m-d H:i') }}
                  @else
                    Not completed yet
                  @endif
                </div>
              </div>
              <div class="text-right" style="margin-top: 10px;">
                @if($sStatus !== 'completed')
                  <form action="{{ url('/deals/'.$deal->id.'/confirm-sale') }}" method="POST" style="display:inline-block; margin-right: 5px;">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                      <span class="glyphicon glyphicon-usd"></span> Confirm Sale
                    </button>
                  </form>
                @endif
                @if($sStatus !== 'cancelled')
                  <form action="{{ url('/deals/'.$deal->id.'/cancel-sale') }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this sale?');">
                      <span class="glyphicon glyphicon-remove"></span> Cancel Sale
                    </button>
                  </form>
                @endif
              </div>
            </div>

          </div>
        </div>
      @endif

      <!-- PAYMENT & BILLING SUMMARY -->
      <div class="panel panel-default" style="margin-top: 20px;">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-usd"></span> <strong>Payment Summary & Tracking</strong>
        </div>
        <div class="panel-body">
          @php
            $dealAmountVal = floatval(preg_replace('/[^0-9\.]/', '', $amount));
            $advAmt = isset($billing) ? floatval($billing->advance_amount ?? 0) : 0;
            $finAmt = isset($billing) ? floatval($billing->final_amount ?? 0) : 0;
            $dueAmt = isset($billing) ? floatval($billing->due_amount ?? $dealAmountVal) : $dealAmountVal;
            
            $payStatus = isset($billing) ? strtolower($billing->payment_status ?? 'pending') : 'pending';
            $payLabelClass = 'label-warning';
            if ($payStatus === 'paid') $payLabelClass = 'label-success';
            elseif ($payStatus === 'partial') $payLabelClass = 'label-info';
          @endphp

          <table class="table table-striped" style="margin-bottom: 0; font-size: 13px;">
            <tr>
              <th style="border-top: 0; width: 50%;">Total Deal Amount</th>
              <td style="border-top: 0;"><strong>₹{{ number_format($dealAmountVal, 0) }}</strong></td>
            </tr>
            <tr>
              <th>Payment Status</th>
              <td><span class="label {{ $payLabelClass }}">{{ ucfirst($payStatus) }}</span></td>
            </tr>
            <tr>
              <th>Advance Paid</th>
              <td>
                ₹{{ number_format($advAmt, 0) }}
                @if(isset($billing) && $billing->advance_paid_at)
                  <br><span style="font-size: 10px; color: #777;"><span class="glyphicon glyphicon-calendar"></span> {{ \Carbon\Carbon::parse($billing->advance_paid_at)->format('Y-m-d') }}</span>
                @endif
              </td>
            </tr>
            <tr>
              <th>Final Paid</th>
              <td>
                ₹{{ number_format($finAmt, 0) }}
                @if(isset($billing) && $billing->final_paid_at)
                  <br><span style="font-size: 10px; color: #777;"><span class="glyphicon glyphicon-calendar"></span> {{ \Carbon\Carbon::parse($billing->final_paid_at)->format('Y-m-d') }}</span>
                @endif
              </td>
            </tr>
            <tr style="background-color: #fcf8e3;">
              <th>Due Amount</th>
              <td><strong class="text-danger">₹{{ number_format($dueAmt, 0) }}</strong></td>
            </tr>
          </table>

          <div style="margin-top: 15px;" class="text-right">
            @if($payStatus !== 'paid')
              <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#advancePaymentModal">
                <span class="glyphicon glyphicon-plus"></span> Record Advance
              </button>
              <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#finalPaymentModal">
                <span class="glyphicon glyphicon-plus"></span> Record Final
              </button>
            @else
              <span class="text-success"><span class="glyphicon glyphicon-ok-sign"></span> Fully Paid</span>
            @endif
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: OFFER & NEGOTIATION RECORDS -->
    <div class="col-md-7">
      
      <div class="panel panel-warning">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-6" style="line-height: 30px;">
              <span class="glyphicon glyphicon-transfer"></span> <strong>Negotiation & Offers</strong>
            </div>
            <div class="col-xs-6 text-right">
              @if(isset($deal))
                <a href="{{ url('/deals/'.$deal->id.'/negotiations/create') }}" class="btn btn-warning btn-sm">
                  <span class="glyphicon glyphicon-plus"></span> Add Offer Record
                </a>
              @endif
            </div>
          </div>
        </div>
        
        <div class="panel-body table-responsive">
          <!-- Inline search & filter form -->
          <form method="GET" action="{{ url('/deal-details/'.$deal->id) }}" style="margin-bottom: 15px;">
            @if(request('doc_search')) <input type="hidden" name="doc_search" value="{{ request('doc_search') }}"> @endif
            @if(request('doc_type')) <input type="hidden" name="doc_type" value="{{ request('doc_type') }}"> @endif

            <div class="row">
              <div class="col-md-8">
                <input type="text" name="neg_search" class="form-control input-sm" placeholder="Search price or notes..." value="{{ request('neg_search') }}">
              </div>
              <div class="col-md-4">
                <select name="neg_status" class="form-control input-sm" onchange="this.form.submit()">
                  <option value="">Filter Status</option>
                  <option value="Pending" {{ request('neg_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                  <option value="Approved" {{ request('neg_status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                  <option value="Countered" {{ request('neg_status') === 'Countered' ? 'selected' : '' }}>Countered</option>
                  <option value="Rejected" {{ request('neg_status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
              </div>
            </div>
          </form>

          <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
            <thead>
              <tr style="background-color: #f9f9f9;">
                <th>Offer Details</th>
                <th>Counter Offer</th>
                <th>Date</th>
                <th>Status</th>
                <th style="width: 90px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($negotiations as $negotiation)
                <tr>
                  <td>
                    <strong>₹{{ number_format(floatval($negotiation->offered_price), 0) }}</strong>
                    @if($negotiation->negotiation_note)
                      <p style="margin: 3px 0 0 0; font-size:11px; color:#666; font-style:italic;">"{{ $negotiation->negotiation_note }}"</p>
                    @endif
                  </td>
                  <td>
                    @if($negotiation->counter_offer)
                      <strong>₹{{ number_format(floatval($negotiation->counter_offer), 0) }}</strong>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>{{ $negotiation->negotiation_date ? $negotiation->negotiation_date->format('Y-m-d') : 'N/A' }}</td>
                  <td>
                    @php
                      $statusClass = 'label-default';
                      if ($negotiation->status === 'Approved') $statusClass = 'label-success';
                      elseif ($negotiation->status === 'Pending') $statusClass = 'label-warning';
                      elseif ($negotiation->status === 'Countered') $statusClass = 'label-primary';
                      elseif ($negotiation->status === 'Rejected') $statusClass = 'label-danger';
                    @endphp
                    <span class="label {{ $statusClass }}">
                      {{ $negotiation->status }}
                    </span>
                  </td>
                  <td>
                    <!-- Actions -->
                    <a href="{{ url('/negotiations/'.$negotiation->id.'/edit') }}" class="btn btn-info btn-sm" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="{{ url('/negotiations/'.$negotiation->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Delete this negotiation record?');"><span class="glyphicon glyphicon-trash"></span></a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">No offer or negotiation history found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- AGREEMENT & DOCUMENT MANAGEMENT -->
      <div class="panel panel-info" style="margin-top: 20px;">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-6" style="line-height: 30px;">
              <span class="glyphicon glyphicon-folder-open"></span> <strong>Agreements & Documents</strong>
            </div>
            <div class="col-xs-6 text-right">
              @if(isset($deal))
                <a href="{{ url('/documents/upload?deal_id='.$deal->id) }}" class="btn btn-info btn-sm">
                  <span class="glyphicon glyphicon-upload"></span> Upload Document
                </a>
              @endif
            </div>
          </div>
        </div>
        <div class="panel-body table-responsive">
          <!-- Inline search & filter form -->
          <form method="GET" action="{{ url('/deal-details/'.$deal->id) }}" style="margin-bottom: 15px;">
            @if(request('neg_search')) <input type="hidden" name="neg_search" value="{{ request('neg_search') }}"> @endif
            @if(request('neg_status')) <input type="hidden" name="neg_status" value="{{ request('neg_status') }}"> @endif

            <div class="row">
              <div class="col-md-8">
                <input type="text" name="doc_search" class="form-control input-sm" placeholder="Search documents..." value="{{ request('doc_search') }}">
              </div>
              <div class="col-md-4">
                <select name="doc_type" class="form-control input-sm" onchange="this.form.submit()">
                  <option value="">Filter by Type</option>
                  <option value="agreement document" {{ request('doc_type') === 'agreement document' ? 'selected' : '' }}>Agreement Document</option>
                  <option value="booking confirmation" {{ request('doc_type') === 'booking confirmation' ? 'selected' : '' }}>Booking Confirmation</option>
                  <option value="sale document" {{ request('doc_type') === 'sale document' ? 'selected' : '' }}>Sale Document</option>
                  <option value="invoice" {{ request('doc_type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
                  <option value="ID proof" {{ request('doc_type') === 'ID proof' ? 'selected' : '' }}>ID Proof</option>
                </select>
              </div>
            </div>
          </form>

          <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
            <thead>
              <tr style="background-color: #f9f9f9;">
                <th>Document Name</th>
                <th>Type</th>
                <th>Uploaded By</th>
                <th>Upload Date</th>
                <th style="width: 90px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($documents as $doc)
                <tr>
                  <td><strong>{{ $doc->document_name }}</strong></td>
                  <td>
                    <span class="label label-default">{{ ucwords($doc->document_type) }}</span>
                  </td>
                  <td>{{ $doc->uploaded_by }}</td>
                  <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                  <td>
                    <!-- Download -->
                    <a href="{{ url('/documents/'.$doc->id.'/download') }}" class="btn btn-default btn-sm" title="Download"><span class="glyphicon glyphicon-download-alt"></span></a>
                    <!-- Delete -->
                    <a href="{{ url('/documents/'.$doc->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Delete this document?');"><span class="glyphicon glyphicon-trash"></span></a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">No agreements or documents uploaded yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </div>
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

<!-- ADVANCE PAYMENT MODAL -->
@if(isset($deal))
<div class="modal fade" id="advancePaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ url('/deals/'.$deal->id.'/record-advance') }}" method="POST">
        @csrf
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Record Advance Payment</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Advance Amount (₹) <span class="text-danger">*</span></label>
            <input type="number" name="advance_amount" class="form-control" placeholder="e.g. 500000" min="0" required>
          </div>
          <div class="form-group">
            <label>Date Paid <span class="text-danger">*</span></label>
            <input type="date" name="advance_paid_at" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- FINAL PAYMENT MODAL -->
<div class="modal fade" id="finalPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ url('/deals/'.$deal->id.'/record-final') }}" method="POST">
        @csrf
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Record Final Payment</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Final Amount (₹) <span class="text-danger">*</span></label>
            <input type="number" name="final_amount" class="form-control" placeholder="e.g. 4500000" min="0" required>
          </div>
          <div class="form-group">
            <label>Date Paid <span class="text-danger">*</span></label>
            <input type="date" name="final_paid_at" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

</body>
</html>