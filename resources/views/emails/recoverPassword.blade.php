<!DOCTYPE html>
<html>

<head>
    <title>Recover Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            color: #555555;
            font-size: 16px;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 4px;
            text-decoration: none;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #888888;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Password Recovery</h1>
        <p>We received a request to reset your password. Click the button below to reset it:</p>
        <a href="{{ $resetLink }}" class="button">Reset Password</a>
        <p>If you did not request a password reset, please ignore this email.</p>
    </div>
</body>

</html>
