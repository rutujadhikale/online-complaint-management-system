<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "complaint_management"
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$complaint_id = intval($_GET['id']);

/*
   First delete feedback connected to this complaint.
   This prevents foreign key errors.
*/

$feedback_sql = "
    DELETE FROM feedback
    WHERE complaint_id = ?
";

$feedback_stmt = mysqli_prepare($conn, $feedback_sql);
mysqli_stmt_bind_param(
    $feedback_stmt,
    "i",
    $complaint_id
);
mysqli_stmt_execute($feedback_stmt);
mysqli_stmt_close($feedback_stmt);


/*
   Now delete the complaint.
*/

$complaint_sql = "
    DELETE FROM complaints
    WHERE complaint_id = ?
";

$complaint_stmt = mysqli_prepare($conn, $complaint_sql);

mysqli_stmt_bind_param(
    $complaint_stmt,
    "i",
    $complaint_id
);

mysqli_stmt_execute($complaint_stmt);

mysqli_stmt_close($complaint_stmt);

mysqli_close($conn);


/*
   Return to admin dashboard.
*/

header("Location: dashboard.php");
exit();

?>