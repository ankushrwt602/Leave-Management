<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request Approved</title>
</head>
<body>
    <h1>Leave Request Approved</h1>

    <p>Dear {{ $user->name }},</p>

    <p>Great news! Your leave request has been approved. Here are the details:</p>

    <ul>
        <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
        <li><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('M d, Y') }}</li>
        <li><strong>End Date:</strong> {{ $leaveRequest->end_date->format('M d, Y') }}</li>
        <li><strong>Days Approved:</strong> {{ $leaveRequest->days_requested }}</li>
        <li><strong>Reason:</strong> {{ $leaveRequest->reason ?: 'Not specified' }}</li>
    </ul>

    <p>Please ensure you follow any company policies regarding leave procedures.</p>

    <p>Best regards,<br>
    Leave Management System</p>
</body>
</html>