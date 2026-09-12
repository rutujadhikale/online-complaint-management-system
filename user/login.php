```php
<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "complaint_management");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];

            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Invalid email or password!";
        }

    } else {
        $message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Login - Complaint Management System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #162d68, #4d7cff);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 420px;
            max-width: 100%;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 18px;
            background: #1f3c88;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
        }

        h2 {
            text-align: center;
            color: #1f3c88;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            color: #333;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d5d9e2;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #1f3c88;
            outline: none;
            box-shadow: 0 0 0 3px rgba(31, 60, 136, 0.10);
        }

        input[type="submit"] {
            width: 100%;
            padding: 14px;
            background: #1f3c88;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background: #162d68;
            transform: translateY(-1px);
        }

        .message {
            background: #fdeaea;
            color: #d63031;
            padding: 11px;
            border-radius: 7px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .register {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .register a {
            color: #1f3c88;
            text-decoration: none;
            font-weight: bold;
        }

        .register a:hover {
            text-decoration: underline;
        }

        .admin-link {
            text-align: center;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #eeeeee;
        }

        .admin-link a {
            color: #555;
            text-decoration: none;
            font-size: 13px;
        }

        .admin-link a:hover {
            color: #1f3c88;
        }

        @media (max-width: 500px) {

            .login-container {
                padding: 30px 25px;
            }

            h2 {
                font-size: 24px;
            }

        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="logo">
        👤
    </div>

    <h2>User Login</h2>

    <p class="subtitle">
        Online Complaint Management System
    </p>

    <?php

    if ($message != "") {

        echo '<div class="message">' .
             htmlspecialchars($message) .
             '</div>';

    }

    ?>

    <form method="POST" action="">

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <input
            type="submit"
            value="Login"
        >

    </form>

    <div class="register">

        Don't have an account?

        <a href="register.php">
            Register here
        </a>

    </div>

    <div class="admin-link">

        <a href="../admin/login.php">
            Admin Login
        </a>

    </div>

</div>

</body>

</html>
```
