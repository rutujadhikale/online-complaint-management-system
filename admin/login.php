```php
<?php
session_start();

// Database connection
$conn = mysqli_connect("127.0.0.1", "root", "", "complaint_management");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Secure SQL query
    $sql = "SELECT * FROM admins WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        // Check admin exists
        if (mysqli_num_rows($result) == 1) {

            $admin = mysqli_fetch_assoc($result);

            // Verify hashed password
            if (password_verify($password, $admin['password'])) {

                // Store admin information in session
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_role'] = $admin['role'];

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Invalid email or password!";

            }

        } else {

            $message = "Invalid email or password!";

        }

        mysqli_stmt_close($stmt);

    } else {

        $message = "Something went wrong. Please try again.";

    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Login - Complaint Management System</title>

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
        }

        .login-container {
            background: white;
            width: 400px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .icon {
            text-align: center;
            font-size: 45px;
            margin-bottom: 10px;
        }

        h2 {
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

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
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
            color: #e74c3c;
            font-weight: bold;
        }

        .back {
            text-align: center;
            margin-top: 20px;
        }

        .back a {
            color: #1f3c88;
            text-decoration: none;
            font-weight: bold;
        }

        .back a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="icon">
        🔐
    </div>

    <h2>Admin Login</h2>

    <p class="subtitle">
        Complaint Management System
    </p>

    <?php

    if ($message != "") {

        echo '<div class="message">' .
             htmlspecialchars($message) .
             '</div>';

    }

    ?>

    <form method="POST" action="">

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter admin email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter admin password"
            required
        >

        <input
            type="submit"
            value="Login"
        >

    </form>

    <div class="back">

        <a href="../index.php">
            ← Back to Home
        </a>

    </div>

</div>

</body>

</html>
```
