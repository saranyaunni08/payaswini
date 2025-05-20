<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payaswini Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
            background-image: url('/images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .page-header {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.6);
        }

        .card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .card-header {
            background: #01375c;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            color: white;
        }

        .card-header h4 {
            margin: 0;
            font-size: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            text-align: left;
            margin-bottom: 15px;
        }

        .input-group label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-custom {
            background: #01375c;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background: #991717;
        }

        .text-sm {
            font-size: 14px;
            color: #666;
        }

        .text-custom {
            color: #c3b817;
            font-weight: bold;
            text-decoration: none;
        }

        .text-custom:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="page-header">
        <div class="card">
            <div class="card-header">
                <h4>Payaswini Admin Login</h4>
                <p>Welcome Back!</p>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('submit.login') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <label for="username">Username or Email</label>
                        <input type="text" id="username" name="login" value="{{ old('login') }}" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-custom">Login</button>
                </form>
                <p class="mt-4 text-sm">
                    Forgot your password?
                    <a href="{{ route('admin.password.request') }}" class="text-custom">Reset Password</a>
                </p>
            </div>
        </div>
    </main>
</body>
</html>