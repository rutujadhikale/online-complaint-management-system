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
$user_name = $_SESSION['full_name'];

$sql = "SELECT
            complaints.complaint_id,
            complaints.subject,
            complaints.priority,
            categories.category_name,
            departments.department_name,
            complaints.status,
            complaints.admin_remark,
            complaints.created_at,
            complaints.updated_at
        FROM complaints
        INNER JOIN categories
            ON complaints.category_id = categories.category_id
        INNER JOIN departments
            ON complaints.department_id = departments.department_id
        WHERE complaints.user_id = '$user_id'
        ORDER BY complaints.created_at DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Complaint Status - Complaint Management System</title>

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

        /* =========================
           NAVBAR
        ========================= */

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
            transition: 0.3s;
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
            margin: 40px auto;
        }

        /* =========================
           HEADING
        ========================= */

        .heading {
            background-color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .heading h1 {
            color: #1f3c88;
            margin-bottom: 8px;
        }

        .heading p {
            color: #777;
            line-height: 1.5;
        }

        /* =========================
           TABLE
        ========================= */

        .table-box {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        th {
            background-color: #1f3c88;
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
           PRIORITY BADGES
        ========================= */

        .priority {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .low {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .medium {
            background-color: #fff3cd;
            color: #856404;
        }

        .high {
            background-color: #f8d7da;
            color: #842029;
        }

        .emergency {
            background-color: #dc3545;
            color: white;
        }

        /* =========================
           STATUS BADGES
        ========================= */

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .submitted {
            background-color: #fff3cd;
            color: #856404;
        }

        .progress {
            background-color: #cfe2ff;
            color: #084298;
        }

        .resolved {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .rejected {
            background-color: #f8d7da;
            color: #842029;
        }

        /* =========================
           ADMIN REMARK
        ========================= */

        .remark {
            max-width: 280px;
            line-height: 1.5;
        }

        .no-remark {
            color: #999;
            font-style: italic;
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
            transition: 0.3s;
        }

        .back:hover {
            background-color: #162d68;
        }

        /* =========================
           NO COMPLAINTS
        ========================= */

        .no-complaints {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .no-complaints h3 {
            margin-bottom: 10px;
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

            .container {
                width: 95%;
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

    <a href="logout.php" class="logout">
        Logout
    </a>

</div>


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="container">

    <!-- Heading -->

    <div class="heading">

        <h1>
            Complaint Status
        </h1>

        <p>
            Welcome, <?php echo htmlspecialchars($user_name); ?>!
            Track the progress of your complaints below.
        </p>

    </div>


    <!-- Complaint Table -->

    <div class="table-box">

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <table>

                <tr>

                    <th>ID</th>

                    <th>Subject</th>

                    <th>Category</th>

                    <th>Department</th>

                    <th>Priority</th>

                    <th>Status</th>

                    <th>Admin Remark</th>

                    <th>Submitted On</th>

                    <th>Last Updated</th>

                </tr>


                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <!-- ID -->

                        <td>
                            <?php echo htmlspecialchars($row['complaint_id']); ?>
                        </td>


                        <!-- Subject -->

                        <td>
                            <?php echo htmlspecialchars($row['subject']); ?>
                        </td>


                        <!-- Category -->

                        <td>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </td>


                        <!-- Department -->

                        <td>
                            <?php echo htmlspecialchars($row['department_name']); ?>
                        </td>


                        <!-- Priority -->

                        <td>

                            <?php

                            $priority_class = "medium";

                            if ($row['priority'] == "Low") {

                                $priority_class = "low";

                            } elseif ($row['priority'] == "High") {

                                $priority_class = "high";

                            } elseif ($row['priority'] == "Emergency") {

                                $priority_class = "emergency";

                            }

                            ?>

                            <span class="priority <?php echo $priority_class; ?>">

                                <?php echo htmlspecialchars($row['priority']); ?>

                            </span>

                        </td>


                        <!-- Status -->

                        <td>

                            <?php

                            $status_class = "submitted";

                            if ($row['status'] == "In Progress") {

                                $status_class = "progress";

                            } elseif ($row['status'] == "Resolved") {

                                $status_class = "resolved";

                            } elseif ($row['status'] == "Rejected") {

                                $status_class = "rejected";

                            }

                            ?>

                            <span class="status <?php echo $status_class; ?>">

                                <?php echo htmlspecialchars($row['status']); ?>

                            </span>

                        </td>


                        <!-- Admin Remark -->

                        <td class="remark">

                            <?php if (!empty($row['admin_remark'])) { ?>

                                <?php echo htmlspecialchars($row['admin_remark']); ?>

                            <?php } else { ?>

                                <span class="no-remark">
                                    No remark yet
                                </span>

                            <?php } ?>

                        </td>


                        <!-- Submitted Date -->

                        <td>
                            <?php echo htmlspecialchars($row['created_at']); ?>
                        </td>


                        <!-- Updated Date -->

                        <td>
                            <?php echo htmlspecialchars($row['updated_at']); ?>
                        </td>

                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <div class="no-complaints">

                <h3>
                    No complaints found.
                </h3>

                <p>
                    You have not submitted any complaints yet.
                </p>

            </div>

        <?php } ?>

    </div>


    <!-- Back Button -->

    <a href="dashboard.php" class="back">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>
```
