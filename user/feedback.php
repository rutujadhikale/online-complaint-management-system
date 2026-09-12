```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "complaint_management");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";


/* ============================
   SUBMIT FEEDBACK
   ============================ */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $complaint_id = intval($_POST['complaint_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);


    /* Check rating */

    if ($rating < 1 || $rating > 5) {

        $message = "Please select a valid rating.";
        $message_type = "error";

    } else {


        /* Check whether complaint belongs to user
           and is resolved */

        $check_sql = "
            SELECT complaint_id
            FROM complaints
            WHERE complaint_id = ?
            AND user_id = ?
            AND status = 'Resolved'
        ";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "ii",
            $complaint_id,
            $user_id
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);


        if (mysqli_num_rows($check_result) == 0) {

            $message = "You can give feedback only for your resolved complaints.";
            $message_type = "error";

        } else {


            /* Check whether feedback already exists */

            $duplicate_sql = "
                SELECT feedback_id
                FROM feedback
                WHERE complaint_id = ?
                AND user_id = ?
            ";

            $duplicate_stmt = mysqli_prepare(
                $conn,
                $duplicate_sql
            );

            mysqli_stmt_bind_param(
                $duplicate_stmt,
                "ii",
                $complaint_id,
                $user_id
            );

            mysqli_stmt_execute($duplicate_stmt);

            $duplicate_result =
                mysqli_stmt_get_result($duplicate_stmt);


            if (mysqli_num_rows($duplicate_result) > 0) {

                $message =
                    "You have already submitted feedback for this complaint.";

                $message_type = "error";

            } else {


                /* Insert feedback */

                $insert_sql = "
                    INSERT INTO feedback
                    (complaint_id, user_id, rating, comment)
                    VALUES (?, ?, ?, ?)
                ";

                $insert_stmt = mysqli_prepare(
                    $conn,
                    $insert_sql
                );

                mysqli_stmt_bind_param(
                    $insert_stmt,
                    "iiis",
                    $complaint_id,
                    $user_id,
                    $rating,
                    $comment
                );


                if (mysqli_stmt_execute($insert_stmt)) {

                    $message =
                        "Feedback submitted successfully!";

                    $message_type = "success";

                } else {

                    $message =
                        "Unable to submit feedback.";

                    $message_type = "error";
                }

                mysqli_stmt_close($insert_stmt);
            }

            mysqli_stmt_close($duplicate_stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}


/* ============================
   GET RESOLVED COMPLAINTS
   ============================ */

$complaint_sql = "
    SELECT
        complaints.complaint_id,
        complaints.subject
    FROM complaints

    LEFT JOIN feedback
        ON complaints.complaint_id = feedback.complaint_id
        AND feedback.user_id = ?

    WHERE complaints.user_id = ?
    AND complaints.status = 'Resolved'
    AND feedback.feedback_id IS NULL

    ORDER BY complaints.complaint_id DESC
";

$complaint_stmt = mysqli_prepare(
    $conn,
    $complaint_sql
);

mysqli_stmt_bind_param(
    $complaint_stmt,
    "ii",
    $user_id,
    $user_id
);

mysqli_stmt_execute($complaint_stmt);

$complaint_result =
    mysqli_stmt_get_result($complaint_stmt);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Give Feedback</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            min-height: 100vh;
        }

        /* Navbar */

        .navbar {
            background-color: #1f3c88;
            color: white;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .logout {
            color: white;
            text-decoration: none;
            background-color: #e74c3c;
            padding: 10px 18px;
            border-radius: 6px;
        }

        .logout:hover {
            background-color: #c0392b;
        }

        /* Container */

        .container {
            width: 90%;
            max-width: 650px;
            margin: 40px auto;
        }

        .form-box {
            background-color: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        h1 {
            color: #1f3c88;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 30px;
        }

        /* Messages */

        .success {
            background-color: #e8f8ee;
            color: #218838;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .info {
            background-color: #e7f1ff;
            color: #084298;
            padding: 15px;
            border-radius: 7px;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        /* Form */

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
            margin-bottom: 22px;
        }

        select:focus,
        textarea:focus {
            outline: none;
            border-color: #1f3c88;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Submit */

        .submit-btn {
            width: 100%;
            padding: 13px;
            background-color: #1f3c88;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #162d68;
        }

        /* Back */

        .back {
            display: inline-block;
            margin-top: 20px;
            color: #1f3c88;
            text-decoration: none;
            font-weight: bold;
        }

        .back:hover {
            text-decoration: underline;
        }

        /* Mobile */

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 20px;
            }

            .logo {
                font-size: 17px;
            }

            .container {
                width: 95%;
            }

            .form-box {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>


<!-- ============================
     NAVBAR
     ============================ -->

<div class="navbar">

    <div class="logo">
        Complaint Management System
    </div>

    <a href="logout.php" class="logout">
        Logout
    </a>

</div>


<!-- ============================
     MAIN CONTENT
     ============================ -->

<div class="container">

    <div class="form-box">

        <h1>
            Give Feedback
        </h1>

        <p class="subtitle">
            Share your feedback about your complaint experience.
        </p>


        <!-- Message -->

        <?php if ($message != "") { ?>

            <div class="<?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <?php if (mysqli_num_rows($complaint_result) > 0) { ?>


            <form method="POST" action="">


                <!-- Complaint -->

                <label>
                    Select Resolved Complaint
                </label>

                <select name="complaint_id" required>

                    <option value="">
                        -- Select Complaint --
                    </option>

                    <?php while (
                        $complaint =
                        mysqli_fetch_assoc($complaint_result)
                    ) { ?>

                        <option
                            value="<?php echo $complaint['complaint_id']; ?>"
                        >

                            Complaint
                            #<?php echo $complaint['complaint_id']; ?>

                            -
                            <?php echo htmlspecialchars(
                                $complaint['subject']
                            ); ?>

                        </option>

                    <?php } ?>

                </select>


                <!-- Rating -->

                <label>
                    Rating
                </label>

                <select name="rating" required>

                    <option value="">
                        -- Select Rating --
                    </option>

                    <option value="5">
                        ★★★★★ Excellent
                    </option>

                    <option value="4">
                        ★★★★ Very Good
                    </option>

                    <option value="3">
                        ★★★ Good
                    </option>

                    <option value="2">
                        ★★ Poor
                    </option>

                    <option value="1">
                        ★ Very Poor
                    </option>

                </select>


                <!-- Comment -->

                <label>
                    Comments
                </label>

                <textarea
                    name="comment"
                    placeholder="Write your feedback here..."
                    required
                ></textarea>


                <!-- Submit -->

                <button
                    type="submit"
                    class="submit-btn"
                >
                    Submit Feedback
                </button>

            </form>


        <?php } else { ?>


            <div class="info">

                <strong>No complaints available for feedback.</strong>

                <br><br>

                You can give feedback only after your complaint
                has been marked as <strong>Resolved</strong>.

            </div>


        <?php } ?>


        <a href="dashboard.php" class="back">
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>

</html>
```
