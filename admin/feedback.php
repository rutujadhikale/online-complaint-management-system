<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


/* =========================
   DATABASE CONNECTION
   ========================= */

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "complaint_management"
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


$admin_name = $_SESSION['admin_name'];


/* =========================
   GET ALL FEEDBACK
   ========================= */

$sql = "
    SELECT
        feedback.feedback_id,
        feedback.rating,
        feedback.comment,
        feedback.created_at,

        users.full_name,
        users.email,

        complaints.complaint_id,
        complaints.subject

    FROM feedback

    INNER JOIN users
        ON feedback.user_id = users.user_id

    INNER JOIN complaints
        ON feedback.complaint_id = complaints.complaint_id

    ORDER BY feedback.created_at DESC
";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Feedback - Complaint Management System</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f6f9;
        }


        /* =========================
           NAVBAR
           ========================= */

        .navbar {
            background-color: #172b5f;
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

        .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-name {
            font-size: 15px;
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


        /* =========================
           CONTAINER
           ========================= */

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 35px auto;
        }


        /* =========================
           HEADING
           ========================= */

        .heading {
            background-color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;

            box-shadow:
                0 3px 10px rgba(0,0,0,0.08);
        }

        .heading h1 {
            color: #172b5f;
            margin-bottom: 8px;
        }

        .heading p {
            color: #777;
        }


        /* =========================
           FEEDBACK BOX
           ========================= */

        .table-box {
            background-color: white;
            padding: 20px;
            border-radius: 12px;

            box-shadow:
                0 3px 10px rgba(0,0,0,0.08);

            overflow-x: auto;
        }

        .table-title {
            color: #172b5f;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }


        /* =========================
           TABLE
           ========================= */

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th {
            background-color: #172b5f;
            color: white;
            padding: 14px;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            color: #444;
            vertical-align: top;
        }

        tr:hover {
            background-color: #f8f9fc;
        }


        /* =========================
           RATING
           ========================= */

        .rating {
            color: #f0a500;
            font-size: 18px;
            white-space: nowrap;
        }

        .rating-number {
            color: #555;
            font-size: 13px;
            margin-left: 5px;
        }


        /* =========================
           COMMENT
           ========================= */

        .comment {
            max-width: 300px;
            line-height: 1.5;
        }


        /* =========================
           COMPLAINT ID
           ========================= */

        .complaint-id {
            background-color: #e8eefc;
            color: #1f3c88;
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: bold;
            white-space: nowrap;
        }


        /* =========================
           DATE
           ========================= */

        .date {
            white-space: nowrap;
            color: #666;
        }


        /* =========================
           BACK BUTTON
           ========================= */

        .back {
            display: inline-block;

            margin-top: 25px;

            background-color: #1f3c88;
            color: white;

            text-decoration: none;

            padding: 11px 20px;

            border-radius: 6px;
        }

        .back:hover {
            background-color: #162d68;
        }


        /* =========================
           NO FEEDBACK
           ========================= */

        .no-feedback {
            text-align: center;
            padding: 50px;
            color: #777;
        }

        .no-feedback h3 {
            margin-bottom: 8px;
            color: #555;
        }


        /* =========================
           MOBILE
           ========================= */

        @media (max-width: 700px) {

            .navbar {
                padding: 15px 20px;
            }

            .logo {
                font-size: 17px;
            }

            .admin-name {
                display: none;
            }

            .container {
                width: 95%;
            }

            .heading {
                padding: 20px;
            }

            .table-box {
                padding: 15px;
            }

            th,
            td {
                font-size: 13px;
                padding: 9px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
     ========================= -->

<div class="navbar">

    <div class="logo">
        Complaint Management System
    </div>


    <div class="admin-info">

        <span class="admin-name">

            👤
            <?php
            echo htmlspecialchars($admin_name);
            ?>

        </span>


        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</div>



<!-- =========================
     MAIN CONTAINER
     ========================= -->

<div class="container">


    <!-- =========================
         HEADING
         ========================= -->

    <div class="heading">

        <h1>
            User Feedback
        </h1>

        <p>

            Welcome,
            <?php
            echo htmlspecialchars($admin_name);
            ?>!

            View feedback submitted by users about their complaints.

        </p>

    </div>



    <!-- =========================
         FEEDBACK TABLE
         ========================= -->

    <div class="table-box">

        <div class="table-title">

            ⭐ All User Feedback

        </div>


        <?php if (mysqli_num_rows($result) > 0) { ?>


            <table>

                <tr>

                    <th>
                        Feedback ID
                    </th>

                    <th>
                        User
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Complaint ID
                    </th>

                    <th>
                        Complaint Subject
                    </th>

                    <th>
                        Rating
                    </th>

                    <th>
                        Comment
                    </th>

                    <th>
                        Date
                    </th>

                </tr>


                <?php while ($row = mysqli_fetch_assoc($result)) { ?>


                    <tr>


                        <!-- Feedback ID -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['feedback_id']
                            );
                            ?>

                        </td>



                        <!-- User -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['full_name']
                            );
                            ?>

                        </td>



                        <!-- Email -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['email']
                            );
                            ?>

                        </td>



                        <!-- Complaint ID -->

                        <td>

                            <span class="complaint-id">

                                #<?php
                                echo htmlspecialchars(
                                    $row['complaint_id']
                                );
                                ?>

                            </span>

                        </td>



                        <!-- Complaint Subject -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['subject']
                            );
                            ?>

                        </td>



                        <!-- Rating -->

                        <td>

                            <span class="rating">

                                <?php

                                $rating = (int)$row['rating'];

                                for ($i = 1; $i <= 5; $i++) {

                                    if ($i <= $rating) {

                                        echo "★";

                                    } else {

                                        echo "☆";

                                    }

                                }

                                ?>

                            </span>


                            <span class="rating-number">

                                (<?php
                                echo $rating;
                                ?>/5)

                            </span>

                        </td>



                        <!-- Comment -->

                        <td class="comment">

                            <?php

                            echo htmlspecialchars(
                                $row['comment']
                            );

                            ?>

                        </td>



                        <!-- Date -->

                        <td class="date">

                            <?php

                            echo htmlspecialchars(
                                $row['created_at']
                            );

                            ?>

                        </td>


                    </tr>


                <?php } ?>


            </table>


        <?php } else { ?>


            <div class="no-feedback">

                <h3>
                    No feedback found.
                </h3>

                <p>
                    Users have not submitted any feedback yet.
                </p>

            </div>


        <?php } ?>


    </div>



    <!-- =========================
         BACK BUTTON
         ========================= -->

    <a
        href="dashboard.php"
        class="back"
    >

        ← Back to Admin Dashboard

    </a>


</div>


</body>

</html>