<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Sign Up</title>
    <style>
        #signup-form {
            display: none;
        }
    </style>

    <link rel="stylesheet" href="sign_in_out.css">
</head>

<body>
    <div class="container">
        <p id="tits">Your Pastry Favorites Are Waiting!
            Log In to Order!</p>
        <div id="signin-form">
            <p id="title">LOGIN FIRST</p><br>
            <form action="signin.php" method="post">
                <input type="text" name="username" id="username" placeholder="Username" required autofocus /><br><br>
                <input type="password" name="password" id="password" placeholder="Password" required /><br><br>
                <button type="submit" class="btnsign-in">Sign In</button>
            </form>
            <br>
            <p class="sp">
                Don't have an account? <a href="sign-up.html">Sign Up</a>
            </p>
        </div>
    </div>
</body>

</html>