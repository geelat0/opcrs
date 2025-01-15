<!DOCTYPE html>
<html>
<head>
    <title>Temporary Password</title>
</head>
<body>
    <p>Dear {{ $emailData['first_name'] }},</p>
    <p>Your temporary password is: <strong>{{ $emailData['password'] }}</strong></p>
    <p>Please use this password to log in and change it as soon as possible.</p>
    <p>Thank you,</p>
    <p>Your Application Team</p>
</body>
</html>
