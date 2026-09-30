<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Deal Confirmation: {{ $type === 'booking' ? 'Booking' : 'Sale' }} Confirmed for {{ $deal->property_name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e4e8;
        }
        .header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 25px;
            line-height: 1.6;
        }
        .deal-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            border-left: 4px solid #2ecc71;
            padding-left: 10px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table th, .details-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
        }
        .details-table th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: #7f8c8d;
            width: 35%;
        }
        .details-table td {
            color: #2c3e50;
        }
        .label {
            display: inline-block;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            color: #ffffff;
        }
        .label-confirmed { background-color: #2ecc71; }
        .notes-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            font-style: italic;
            color: #555;
        }
        .footer {
            background-color: #f4f6f9;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #eeeeee;
        }
        .btn {
            display: inline-block;
            background-color: #2ecc71;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Real Estate CRM</h2>
        <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Sales & Bookings Department</p>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        <p>We are delighted to inform you that your deal status has been updated. Please find the confirmation overview below:</p>
        
        <div class="deal-title">
            {{ $type === 'booking' ? 'Booking Confirmed' : 'Sale Completed' }}
        </div>
        
        <table class="details-table">
            <tr>
                <th>Property</th>
                <td><strong>{{ $deal->property_name }}</strong></td>
            </tr>
            <tr>
                <th>Client Name</th>
                <td>{{ $deal->client_name }}</td>
            </tr>
            <tr>
                <th>Deal Amount</th>
                <td>Rs. {{ number_format(floatval($deal->deal_amount), 2) }}</td>
            </tr>
            <tr>
                <th>Assigned Agent</th>
                <td>{{ $deal->agent_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date of Transaction</th>
                <td>{{ optional($deal->booking_date)->format('F d, Y') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Confirmation Status</th>
                <td>
                    <span class="label label-confirmed">Confirmed</span>
                </td>
            </tr>
            <tr>
                <th>Confirmed At</th>
                <td>{{ $type === 'booking' ? optional($deal->booking_confirmed_at)->format('F d, Y h:i A') : optional($deal->sale_confirmed_at)->format('F d, Y h:i A') }}</td>
            </tr>
        </table>
        
        @if($deal->notes)
            <div style="font-weight: 600; margin-top: 20px; color: #2c3e50;">Transaction Notes:</div>
            <div class="notes-box">
                "{{ $deal->notes }}"
            </div>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ url('/client/bookings') }}" class="btn">View Bookings Portal</a>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2026 Real Estate CRM. All rights reserved.</p>
        <p>This is a system generated notification. Please do not reply directly to this email.</p>
    </div>
</div>

</body>
</html>
