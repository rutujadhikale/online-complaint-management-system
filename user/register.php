<?php

$conn = mysqli_connect("localhost", "root", "", "complaint_management");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $check = "SELECT * FROM users WHERE email='$email'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {

        $message = "Email already registered!";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users
                (full_name, email, phone, password)
                VALUES
                ('$full_name', '$email', '$phone', '$hashed_password')";

        if (mysqli_query($conn, $sql)) {

            $message = "Registration successful! You can now login.";

        } else {

            $message = "Registration failed: " . mysqli_error($conn);

        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>User Registration - Complaint Management System</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1f3c88, #4d7cff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .register-container {
            background: white;
            width: 450px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .register-container h2 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        input:focus {
            border-color: #1f3c88;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 13px;
            background-color: #1f3c88;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #162d68;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #1f3c88;
            font-weight: bold;
        }

        .login {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login a {
            color: #1f3c88;
            text-decoration: none;
            font-weight: bold;
        }

        .login a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="register-container">

    <h2>Create Account</h2>

    <p class="subtitle">
        Complaint Management System
    </p>

    <?php
    if ($message != "") {
        echo '<div class="message">' . htmlspecialchars($message) . '</div>';
    }
    ?>

    <form method="POST" action="">

        <label>Full Name</label>

        <input
            type="text"
            name="full_name"
            placeholder="Enter your full name"
            required
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label>Phone Number</label>

        <input
            type="tel"
            name="phone"
            placeholder="Enter your phone number"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Create a password"
            required
        >

        <input type="submit" value="Create Account">

    </form>

    <div class="login">

        Already have an account?

        <a href="login.php">
            Login here
        </a>

    </div>

</div>

</body>

</html>