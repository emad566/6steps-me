<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>6steps OTP Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


</head>

<body>
    <div style="padding-left: 50px;">
        <br>
        Hello,
        <br>
        Your one-time password (OTP) for verification is:
        <br>
        <br>
        <div style="text-align:center; font-size:26px;"><strong><?php echo e($code); ?></strong></div>
        <br>
        It will expire after 6 minutes
        <br>
        If you did not request this OTP, please ignore this email.
        <br><br>
        Thanks,<br>
        6stepsa.com
    </div>

</body>

</html>
<?php /**PATH /home/theglow/api.6stepsa.com/resources/views/emails/send-verification-code.blade.php ENDPATH**/ ?>