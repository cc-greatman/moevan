<!DOCTYPE html>
<html>
<head>
    <title>API Request Data</title>
</head>
<body>
    <h2>API Request Sent</h2>
    <p><strong>Network ID:</strong> {{ $networkId }}</p>
    <p><strong>Data Type:</strong> {{ $dataType }}</p>
    <p><strong>Plan ID:</strong> {{ $planId }}</p>
    <p><strong>Phone:</strong> {{ $phone }}</p>
    <p><strong>Reference:</strong> {{ $reference }}</p>
    
    <h3>Headers:</h3>
    <ul>
        <li><strong>Content-Type:</strong> application/json</li>
        <li><strong>Accept:</strong> application/json</li>
        <li><strong>Authorization:</strong> Bearer {{ $autopilotApiKey }}</li>
    </ul>

    <p>Regards,</p>
    <p>BitSYS</p>
</body>
</html>
