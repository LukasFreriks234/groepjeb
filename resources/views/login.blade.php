<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis Login</title>

    <link rel="stylesheet" href="{{asset('css/login.css')}}">
</head>
<body>

<div class="login-container">

    <h1>Login</h1>

    <form method="POST" action="/">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Enter your email"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password"
                required
            >
        </div>
        
        @error('email')
        <p class="error-message">{{$message}}</p>
        @enderror
        
        <button type="submit" class="login-btn">
            Login
        </button>
    </form>
</div>

</body>
</html>