<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h3 {
            color: #333;
        }

        p {
            color: #555;
        }

        hr {
            border: 0;
            height: 1px;
            background: #ddd;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Hello {{$mailData['name']}},</h3>
        <p>This is a password recovery request. Use the link below to reset your password.</p>
        <p>If you did not request this, please ignore this email.</p>
        <hr class="w-25" />
        <a href="{{url('reset-password/'.$mailData['token'])}}">Reset Password</a>
    </div>
</body>
</html>
