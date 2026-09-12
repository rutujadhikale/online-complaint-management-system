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

/* Get user's complaints */
$sql = "SELECT
            complaints.complaint_id,
            categories.category_name,
            departments.department_name,
            complaints.subject,
            complaints.priority,
            complaints.description,
            complaints.status,
            complaints.admin_remark,
            complaints.created_at
        FROM complaints
        INNER JOIN categories
            ON complaints.category_id = categories.category_id
        INNER JOIN departments
            ON complaints.department_id = departments.department_id
        WHERE complaints.user_id = ?
        ORDER BY complaints.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Complaints - Complaint Management System</title>

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

        /* Main container */

        .container {
            width: 95%;
            max-width: 1500px;
            margin: 35px auto;
        }

        /* Heading */

        .heading {
            background-color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .heading h1 {
            color: #172b5f;
            margin-bottom: 8px;
        }

        .heading p {
            color: #777;
            line-height: 1.5;
        }

        /* Table */

        .table-box {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .table-title {
            color: #172b5f;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        th {
            background-color: #172b5f;
            color: white;
            padding: 13px;
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

        /* Priority badges */

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

        /* Status badges */

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
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

        /* Admin remark */

        .remark {
            max-width: 250px;
            line-height: 1.5;
        }

        .no-remark {
            color: #999;
            font-style: italic;
        }

        /* No complaints */

        .no-complaints {
            text-align: center;
            padding: 50px 20px;
            color: #777;
        }

        .no-complaints h3 {
            margin-bottom: 10px;
            color: #555;
        }

        /* Back button */

        .back {
            display: inline-block;
            margin-top: 25px;
            background-color: #172b5f;
            color: white;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 6px;
        }

        .back:hover {
            background-color: #1f3c88;
        }

        /* Mobile */

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

<!-- Navbar -->

<div class="navbar">

    <div class="logo">
        Complaint Management System
    </div>

    <a href="logout.php" class="logout">
        Logout
    </a>

</div>


<!-- Main -->

<div class="container">

    <!-- Heading -->

    <div class="heading">

        <h1>
            My Complaints
        </h1>

        <p>
            Welcome,
            <?php echo htmlspecialchars($user_name); ?>!
            Here you can view all complaints submitted by you.
        </p>

    </div>


    <!-- Complaint Table -->

    <div class="table-box">

        <div class="table-title">
            My Submitted Complaints
        </div>


        <?php if (mysqli_num_rows($result) > 0) { ?>

            <table>

                <tr>

                    <th>ID</th>

                    <th>Category</th>

                    <th>Department</th>

                    <th>Subject</th>

                    <th>Priority</th>

                    <th>Description</th>

                    <th>Status</th>

                    <th>Admin Remark</th>

                    <th>Date</th>

                </tr>


                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <!-- ID -->

                        <td>
                            <?php echo htmlspecialchars($row['complaint_id']); ?>
                        </td>


                        <!-- Category -->

                        <td>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </td>


                        <!-- Department -->

                        <td>
                            <?php echo htmlspecialchars($row['department_name']); ?>
                        </td>


                        <!-- Subject -->

                        <td>
                            <?php echo htmlspecialchars($row['subject']); ?>
                        </td>


                        <!-- Priority -->

                        <td>

                            <?php

                            $priority_class = "medium";

                            if ($row['priority'] == "Low") {
                                $priority_class = "low";
                            }

                            elseif ($row['priority'] == "High") {
                                $priority_class = "high";
                            }

                            elseif ($row['priority'] == "Emergency") {
                                $priority_class = "emergency";
                            }

                            ?>

                            <span class="priority <?php echo $priority_class; ?>">

                                <?php echo htmlspecialchars($row['priority']); ?>

                            </span>

                        </td>


                        <!-- Description -->

                        <td>
                            <?php echo htmlspecialchars($row['description']); ?>
                        </td>


                        <!-- Status -->

                        <td>

                            <?php

                            $status_class = "submitted";

                            if ($row['status'] == "In Progress") {
                                $status_class = "progress";
                            }

                            elseif ($row['status'] == "Resolved") {
                                $status_class = "resolved";
                            }

                            elseif ($row['status'] == "Rejected") {
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


                        <!-- Date -->

                        <td>
                            <?php echo htmlspecialchars($row['created_at']); ?>
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


    <!-- Back button -->

    <a href="dashboard.php" class="back">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>

<?php

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
```
