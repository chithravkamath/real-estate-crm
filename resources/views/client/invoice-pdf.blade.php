<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice: {{ $billing->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            padding: 30px;
            border-radius: 8px;
            background-color: #ffffff;
        }
        .header {
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header td {
            vertical-align: top;
        }
        .title h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 26px;
        }
        .title p {
            margin: 5px 0 0 0;
            color: #7f8c8d;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h3 {
            margin: 0;
            color: #3498db;
            font-size: 20px;
        }
        .invoice-details p {
            margin: 5px 0 0 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .details-table th, .details-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
        }
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
            width: 40%;
        }
        .details-table td {
            color: #333333;
        }
        .label {
            display: inline-block;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            color: #ffffff;
            text-transform: uppercase;
        }
        .label-paid { background-color: #2ecc71; }
        .label-pending { background-color: #f39c12; }
        .label-failed { background-color: #e74c3c; }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <table>
            <tr>
                <td class="title">
                    <h2>Real Estate CRM</h2>
                    <p>Premium CRM & Invoicing Services</p>
                </td>
                <td class="invoice-details">
                    <h3>INVOICE</h3>
                    <p><strong>Invoice #:</strong> {{ $billing->invoice_number }}</p>
                    <p><strong>Date:</strong> {{ optional($billing->payment_date)->format('Y-m-d') ?? 'N/A' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <tr>
            <th>Client Name</th>
            <td>{{ $billing->client_name }}</td>
        </tr>
        <tr>
            <th>Property Name</th>
            <td>{{ $billing->property_name }}</td>
        </tr>
        <tr>
            <th>Invoice Number</th>
            <td><strong>{{ $billing->invoice_number }}</strong></td>
        </tr>
        <tr>
            <th>Amount</th>
            <td><strong>Rs. {{ number_format(floatval($billing->payment_amount), 2) }}</strong></td>
        </tr>
        <tr>
            <th>Payment Status</th>
            <td>
                @php
                    $status = strtolower($billing->payment_status ?? 'pending');
                    $lblClass = 'label-pending';
                    if ($status === 'paid') $lblClass = 'label-paid';
                    elseif ($status === 'overdue' || $status === 'failed') $lblClass = 'label-failed';
                @endphp
                <span class="label {{ $lblClass }}">{{ $billing->payment_status }}</span>
            </td>
        </tr>
        <tr>
            <th>Payment Date</th>
            <td>{{ optional($billing->payment_date)->format('Y-m-d') ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for doing business with us!</p>
        <p>&copy; 2026 Real Estate CRM. All rights reserved.</p>
    </div>
</div>

</body>
</html>
