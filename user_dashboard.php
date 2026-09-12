<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
</head>

<body>

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!</h1>

    <p>You are successfully logged in.</p>

    <p>Email: <?php echo htmlspecialchars($_SESSION["email"]); ?></p>

    <hr>

    <h2>User Dashboard</h2>

    <a href="register_complaint.php">Register Complaint</a>
    <br><br>

    <a href="my_complaints.php">My Complaints</a>
    <br><br>

    <a href="feedback.php">Give Feedback</a>
    <br><br>

    <a href="logout.php">Logout</a>

</body>

</html>