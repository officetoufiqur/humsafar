<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #333; text-align: center;">Your OTP Code</h2>
        <p>Hello,</p>
        <p>Thank you for choosing our Company. Use the following OTP to complete registration.</p>

        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #4A90E2; padding: 10px 20px; background: #f4f4f4; border-radius: 5px;">
                {{ $otp }}
            </span>
        </div>

        <p>OTP is valid for 30 minutes. Do not share this code with others, including our employees.</p>
        <hr style="border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #888; text-align: center;">Sent from {{ config('app.name') }}</p>
    </div>
</body>
</html>