<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request Submitted</title>
</head>
<body>
    <h1>Leave Request Submitted Successfully</h1>

    <p>Dear {{ $user->name }},</p>

    <p>Your leave request has been submitted successfully. Here are the details:</p>

    <ul>
        <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
        <li><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('M d, Y') }}</li>
        <li><strong>End Date:</strong> {{ $leaveRequest->end_date->format('M d, Y') }}</li>
        <li><strong>Days Requested:</strong> {{ $leaveRequest->days_requested }}</li>
        <li><strong>Reason:</strong> {{ $leaveRequest->reason ?: 'Not specified' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($leaveRequest->status) }}</li>
    </ul>

    <p>You will receive another email once your request is reviewed by an administrator.</p>

    <p>Best regards,<br>
    Leave Management System</p>
</body>
</html>