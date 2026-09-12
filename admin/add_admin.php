<?php
session_start();

$conn = mysqli_connect("127.0.0.1", "root", "", "complaint_management");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Only Super Admin can create new Admin
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'Super Admin') {
    die("Access Denied ❌ Only Super Admin can create new Admin.");
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password match
    if ($password !== $confirm_password) {

        $message = "Passwords do not match!";
        $message_type = "error";

    } else {

        // Check email already exists
        $check_sql = "SELECT admin_id FROM admins WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $message = "This email is already registered!";
            $message_type = "error";

        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin
            // New admin will automatically get role = Admin
            $sql = "INSERT INTO admins (full_name, email, password, role)
                    VALUES (?, ?, ?, 'Admin')";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $full_name,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "New admin added successfully!";
                $message_type = "success";

            } else {

                $message = "Something went wrong!";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>

    <title>Add New Admin</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 400px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #0D47A1;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 11px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 12px;
            background: #0D47A1;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #08306b;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #0D47A1;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Add New Admin</h2>

    <?php if ($message != "") { ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Add Admin</button>

    </form>

    <a class="back" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>