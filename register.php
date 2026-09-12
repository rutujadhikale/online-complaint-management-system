<?php
$conn = new mysqli("localhost", "root", "", "complaint_management");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "Email already registered!";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, email, phone, password)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $full_name, $email, $phone, $password);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Registration failed!";
        }

        $stmt->close();
    }

    $check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .container {
            width: 400px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>User Registration</h2>

    <?php
    if ($message != "") {
        echo "<div class='message'>$message</div>";
    }
    ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Register</button>

    </form>

</div>

</body>
</html>