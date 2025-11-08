<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .message {
            margin-bottom: 20px;
        }

        .button-container {
            text-align: center;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        @if(Auth::user()->email_verified_at == null)
        <div class="message">
            @if (session('status') == 'verification-link-sent')
                <p>Email verifikasi telah dikirim. Silakan cek email Anda.</p>
                <p>Tunggu admin melakukan approving. Kemudian lakukan login ulang.</p>
            @else
                <p>Email verifikasi telah dikirim. Silakan cek email Anda.</p>
                <p>Tunggu admin melakukan approving. Kemudian lakukan login ulang.</p>
            @endif
        </div>
        <div class="button-container">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn">Kirim Ulang Email Verifikasi</button>
            </form>
        </div>
        @elseif(Auth::user()->is_approved == 0 && Auth::user()->email_verified_at != null)
            <p>Email telah diverifikasi.</p>
            <p>Tunggu admin melakukan approving. Kemudian lakukan login ulang.</p>
        @endif
    </div>
</body>

</html>
