<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request Rejected</title>
</head>
<body>
    <h1>Leave Request Update</h1>

    <p>Dear {{ $user->name }},</p>

    <p>We regret to inform you that your leave request has been rejected. Here are the details:</p>

    <ul>
        <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
        <li><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('M d, Y') }}</li>
        <li><strong>End Date:</strong> {{ $leaveRequest->end_date->format('M d, Y') }}</li>
        <li><strong>Days Requested:</strong> {{ $leaveRequest->days_requested }}</li>
        <li><strong>Reason:</strong> {{ $leaveRequest->reason ?: 'Not specified' }}</li>
    </ul>

    <p>If you have any questions or would like to discuss alternative arrangements, please contact your supervisor or HR department.</p>

    <p>Best regards,<br>
    Leave Management System</p>
</body>
</html>