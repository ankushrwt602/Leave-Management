<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Leave Request Submitted</title>
</head>
<body>
    <h1>New Leave Request Requires Approval</h1>

    <p>A new leave request has been submitted and requires your approval.</p>

    <h2>Employee Details:</h2>
    <ul>
        <li><strong>Name:</strong> {{ $user->name }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
    </ul>

    <h2>Leave Request Details:</h2>
    <ul>
        <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
        <li><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('M d, Y') }}</li>
        <li><strong>End Date:</strong> {{ $leaveRequest->end_date->format('M d, Y') }}</li>
        <li><strong>Days Requested:</strong> {{ $leaveRequest->total_days }}</li>
        <li><strong>Reason:</strong> {{ $leaveRequest->reason ?: 'Not specified' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($leaveRequest->status) }}</li>
    </ul>

    @if($leaveRequest->emergency_contact)
    <h2>Emergency Contact:</h2>
    <p>{{ $leaveRequest->emergency_contact }}</p>
    @endif

    @if($leaveRequest->contact_during_leave)
    <h2>Contact During Leave:</h2>
    <p>{{ $leaveRequest->contact_during_leave }}</p>
    @endif

    @if($leaveRequest->work_coverage && count($leaveRequest->work_coverage) > 0)
    <h2>Work Coverage:</h2>
    <ul>
        @foreach($leaveRequest->work_coverage as $coverage)
        <li>{{ $coverage }}</li>
        @endforeach
    </ul>
    @endif

    <p>Please review this request and take appropriate action.</p>

    <p>You can view and manage leave requests by logging into the system.</p>

    <p>Best regards,<br>
    Leave Management System</p>
</body>
</html>