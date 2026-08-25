<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
</head>

<body style="margin:0;padding:0;background:#fff7ed;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background:#fff7ed;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:600px;background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #fed7aa;">
                    <tr>
                        <td style="padding:32px 32px 8px 32px;">
                            <div
                                style="font-size:12px;font-weight:700;letter-spacing:0.14em;color:#f97316;text-transform:uppercase;">
                                Restrotix
                            </div>
                            <h1 style="margin:12px 0 0 0;font-size:28px;line-height:1.2;color:#111827;">
                                Verify your email address
                            </h1>
                            <p style="margin:12px 0 0 0;font-size:15px;line-height:1.7;color:#4b5563;">
                                Use the verification code below to verify your restaurant signup email. This code will
                                expire in
                                {{ $expiresInMinutes }} minutes.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px 8px 32px;">
                            <div
                                style="background:#fff7ed;border:1px dashed #fdba74;border-radius:16px;padding:24px;text-align:center;">
                                <div
                                    style="font-size:12px;font-weight:700;letter-spacing:0.14em;color:#f97316;text-transform:uppercase;margin-bottom:10px;">
                                    VERIFICATION CODE
                                </div>
                                <div
                                    style="font-size:42px;font-weight:800;letter-spacing:0.35em;color:#111827;font-family:Courier New,monospace;">
                                    {{ implode(' ', str_split($otpCode)) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 8px 32px;text-align:center;">
                            <p style="margin:0;font-size:13px;line-height:1.7;color:#dc2626;font-weight:700;">
                                Please do not share this code with anyone.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px 32px 32px;">
                            <p style="margin:0;font-size:14px;line-height:1.7;color:#4b5563;">
                                If you did not request this code, you can safely ignore this email.
                            </p>
                            <p style="margin:16px 0 0 0;font-size:14px;line-height:1.7;color:#4b5563;">
                                Thanks,<br>
                                The RestoTix Team
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
