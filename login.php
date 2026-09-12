<?php
session_start();

$conn = new mysqli("localhost", "root", "", "complaint_management");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT user_id, full_name, email, password FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];

            header("Location: user_dashboard.php");
            exit();

        } else {
            $message = "Incorrect password!";
        }

    } else {
        $message = "Email not registered!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>

    <title>User Login</title>

    <style>

        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .container {
            width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            color: red;
            margin-bottom: 15px;
        }

        .register {
            text-align: center;
            margin-top: 20px;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>User Login</h2>

    <?php
    if ($message != "") {
        echo "<div class='message'>$message</div>";
    }
    ?>

    <form method="POST">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>

    <div class="register">
        Don't have an account?
        <a href="register.php">Register</a>
    </div>

</div>

</body>

</html>