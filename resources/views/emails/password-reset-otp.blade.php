<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Password Reset OTP</title>
</head>

<body style="
    margin:0;
    padding:0;
    background:#fdf2f2;
    font-family:Arial,Helvetica,sans-serif;
    color:#1f2937;
">

<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="background:#fdf2f2;padding:32px 16px;"
>
    <tr>
        <td align="center">

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    max-width:600px;
                    background:#ffffff;
                    border-radius:18px;
                    overflow:hidden;
                    border:1px solid #fecaca;
                "
            >

                <tr>
                    <td style="padding:32px 32px 8px 32px;">

                        <div style="
                            font-size:12px;
                            font-weight:700;
                            letter-spacing:0.14em;
                            color:#DC0812;
                            text-transform:uppercase;
                        ">
                            Restrotix
                        </div>

                        <h1 style="
                            margin:12px 0 0 0;
                            font-size:28px;
                            line-height:1.2;
                            color:#111827;
                        ">
                            Reset your password
                        </h1>

                        <p style="
                            margin:12px 0 0 0;
                            font-size:15px;
                            line-height:1.7;
                            color:#4b5563;
                        ">
                            We received a request to reset your Restrotix account password.
                            Use the OTP below to continue.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 32px 8px 32px;">

                        <div style="
                            background:#fff1f2;
                            border:1px dashed #fca5a5;
                            border-radius:16px;
                            padding:24px;
                            text-align:center;
                        ">
                            <div style="
                                font-size:12px;
                                font-weight:700;
                                letter-spacing:0.14em;
                                color:#DC0812;
                                text-transform:uppercase;
                                margin-bottom:10px;
                            ">
                                PASSWORD RESET CODE
                            </div>

                            <div style="
                                font-size:20px;
                                font-weight:800;
                                letter-spacing:0.10em;
                                color:#111827;
                                font-family:'Courier New',monospace;
                            ">
                                {{ implode(' ', str_split($otpCode)) }}
                            </div>
                        </div>

                    </td>
                </tr>

                <tr>
                    <td style="
                        padding:8px 32px 8px 32px;
                        text-align:center;
                    ">

                        <p style="
                            margin:0;
                            font-size:13px;
                            line-height:1.7;
                            color:#DC0812;
                            font-weight:700;
                        ">
                            This OTP will expire in {{ $expiresInMinutes }} minutes.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 32px 32px 32px;">

                        <p style="
                            margin:0;
                            font-size:14px;
                            line-height:1.7;
                            color:#4b5563;
                        ">
                            Do not share this OTP with anyone.
                        </p>

                        <p style="
                            margin:10px 0 0 0;
                            font-size:14px;
                            line-height:1.7;
                            color:#4b5563;
                        ">
                            If you did not request a password reset, you can safely ignore this email.
                        </p>

                        <p style="
                            margin:16px 0 0 0;
                            font-size:14px;
                            line-height:1.7;
                            color:#4b5563;
                        ">
                            Thanks,<br>
                            The Restrotix Team
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>