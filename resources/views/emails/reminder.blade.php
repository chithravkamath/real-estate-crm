<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRM Reminder: {{ $reminder->title }}</title>
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
        .reminder-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
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
            width: 30%;
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
        .label-lead { background-color: #f39c12; }
        .label-client { background-color: #2ecc71; }
        .label-visit { background-color: #3498db; }
        .label-deal { background-color: #9b59b6; }
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
            background-color: #3498db;
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
        <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Automated Notification System</p>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        <p>This is a future-ready automated notification regarding a scheduled reminder in your Real Estate CRM portal.</p>
        
        <div class="reminder-title">
            {{ $reminder->title }}
        </div>
        
        <table class="details-table">
            <tr>
                <th>Date</th>
                <td>{{ \Carbon\Carbon::parse($reminder->reminder_date)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <th>Time</th>
                <td>{{ \Carbon\Carbon::parse($reminder->reminder_time)->format('h:i A') }}</td>
            </tr>
            <tr>
                <th>Module</th>
                <td>
                    @php
                        $labelClass = 'label-deal';
                        if ($reminder->related_type === 'Lead') $labelClass = 'label-lead';
                        elseif ($reminder->related_type === 'Client') $labelClass = 'label-client';
                        elseif ($reminder->related_type === 'Site Visit') $labelClass = 'label-visit';
                    @endphp
                    <span class="label {{ $labelClass }}">{{ $reminder->related_type }}</span>
                </td>
            </tr>
            <tr>
                <th>Related Item</th>
                <td>{{ $reminder->related_name }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><strong style="color: {{ $reminder->status === 'Completed' ? '#2ecc71' : '#e74c3c' }};">{{ $reminder->status }}</strong></td>
            </tr>
        </table>
        
        @if($reminder->notes)
            <div style="font-weight: 600; margin-top: 20px; color: #2c3e50;">Notes:</div>
            <div class="notes-box">
                "{{ $reminder->notes }}"
            </div>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ url('/reminders') }}" class="btn">View in CRM Portal</a>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2026 Real Estate CRM. All rights reserved.</p>
        <p>This is a system generated notification. Please do not reply directly to this email.</p>
    </div>
</div>

</body>
</html>
