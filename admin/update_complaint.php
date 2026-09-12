```php
<?php
session_start();

/* =========================
   CHECK ADMIN LOGIN
   ========================= */

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


/* =========================
   ADMIN NAME
   ========================= */

$admin_name = $_SESSION['admin_name'] ?? "System Administrator";


/* =========================
   CHECK COMPLAINT ID
   ========================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Complaint ID.");
}

$complaint_id = intval($_GET['id']);


/* =========================
   UPDATE COMPLAINT
   ========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status = $_POST['status'] ?? "";
    $admin_remark = trim($_POST['admin_remark'] ?? "");


    /* Allowed status values */

    $allowed_statuses = [
        "Submitted",
        "In Progress",
        "Resolved",
        "Rejected"
    ];


    if (!in_array($status, $allowed_statuses, true)) {

        die("Invalid complaint status.");

    }


    /* Update complaint */

    $update_sql = "UPDATE complaints
                   SET status = ?,
                       admin_remark = ?,
                       updated_at = CURRENT_TIMESTAMP
                   WHERE complaint_id = ?";

    $stmt = mysqli_prepare($conn, $update_sql);

    if (!$stmt) {
        die("Update query failed: " . mysqli_error($conn));
    }


    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $status,
        $admin_remark,
        $complaint_id
    );


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        /* Go back to admin dashboard */

        header("Location: dashboard.php?updated=1");
        exit();

    } else {

        $error_message = "Unable to update complaint.";

        mysqli_stmt_close($stmt);
    }
}


/* =========================
   GET COMPLAINT DETAILS
   ========================= */

$sql = "SELECT
            complaints.complaint_id,
            complaints.subject,
            complaints.priority,
            complaints.description,
            complaints.status,
            complaints.admin_remark,
            complaints.created_at,
            complaints.updated_at,

            users.full_name,
            users.email,

            categories.category_name,

            departments.department_name

        FROM complaints

        INNER JOIN users
            ON complaints.user_id = users.user_id

        INNER JOIN categories
            ON complaints.category_id = categories.category_id

        INNER JOIN departments
            ON complaints.department_id = departments.department_id

        WHERE complaints.complaint_id = ?";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $complaint_id
);


mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) != 1) {

    mysqli_stmt_close($stmt);

    die("Complaint not found.");

}


$row = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>

<html>

<head>

    <title>Update Complaint - Complaint Management System</title>


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
           MAIN CONTAINER
           ========================= */

        .container {

            width: 90%;

            max-width: 900px;

            margin: 40px auto;
        }


        .card {

            background-color: white;

            padding: 35px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.10);
        }


        /* =========================
           HEADING
           ========================= */

        h1 {

            color: #172b5f;

            margin-bottom: 8px;
        }


        .subtitle {

            color: #777;

            margin-bottom: 25px;
        }


        /* =========================
           ERROR MESSAGE
           ========================= */

        .error-message {

            background-color: #f8d7da;

            color: #842029;

            padding: 12px 15px;

            border-radius: 7px;

            margin-bottom: 20px;

            font-weight: bold;
        }


        /* =========================
           COMPLAINT INFORMATION
           ========================= */

        .info {

            background-color: #f8f9fc;

            padding: 20px;

            border-radius: 8px;

            margin-bottom: 25px;
        }


        .info-row {

            display: flex;

            margin-bottom: 14px;
        }


        .info-row:last-child {

            margin-bottom: 0;
        }


        .label {

            width: 160px;

            font-weight: bold;

            color: #333;
        }


        .value {

            flex: 1;

            color: #555;

            line-height: 1.5;
        }


        /* =========================
           PRIORITY
           ========================= */

        .priority {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
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
           FORM
           ========================= */

        label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

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

            min-height: 140px;

            resize: vertical;
        }


        /* =========================
           UPDATE BUTTON
           ========================= */

        .update-btn {

            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 7px;

            background-color: #1f3c88;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }


        .update-btn:hover {

            background-color: #162d68;
        }


        /* =========================
           BACK BUTTON
           ========================= */

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


        /* =========================
           MOBILE
           ========================= */

        @media (max-width: 600px) {

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


            .card {

                padding: 25px 20px;
            }


            .info-row {

                display: block;
            }


            .label {

                width: auto;

                margin-bottom: 5px;
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

            👤 <?php echo htmlspecialchars($admin_name); ?>

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
     MAIN
     ========================= -->

<div class="container">


    <div class="card">


        <h1>

            Update Complaint

        </h1>


        <p class="subtitle">

            Review the complaint and update its status.

        </p>



        <!-- Error Message -->

        <?php if (isset($error_message)) { ?>

            <div class="error-message">

                <?php echo htmlspecialchars($error_message); ?>

            </div>

        <?php } ?>



        <!-- =========================
             COMPLAINT DETAILS
             ========================= -->

        <div class="info">


            <!-- ID -->

            <div class="info-row">

                <div class="label">
                    Complaint ID:
                </div>

                <div class="value">

                    #<?php
                    echo htmlspecialchars(
                        $row['complaint_id']
                    );
                    ?>

                </div>

            </div>



            <!-- User -->

            <div class="info-row">

                <div class="label">
                    User:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['full_name']
                    );
                    ?>

                </div>

            </div>



            <!-- Email -->

            <div class="info-row">

                <div class="label">
                    Email:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['email']
                    );
                    ?>

                </div>

            </div>



            <!-- Category -->

            <div class="info-row">

                <div class="label">
                    Category:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['category_name']
                    );
                    ?>

                </div>

            </div>



            <!-- Department -->

            <div class="info-row">

                <div class="label">
                    Department:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['department_name']
                    );
                    ?>

                </div>

            </div>



            <!-- Subject -->

            <div class="info-row">

                <div class="label">
                    Subject:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['subject']
                    );
                    ?>

                </div>

            </div>



            <!-- Priority -->

            <div class="info-row">

                <div class="label">
                    Priority:
                </div>

                <div class="value">


                    <?php

                    $priority = $row['priority'] ?? "Medium";

                    $priority_class = "medium";


                    if ($priority == "Low") {

                        $priority_class = "low";

                    }

                    elseif ($priority == "High") {

                        $priority_class = "high";

                    }

                    elseif ($priority == "Emergency") {

                        $priority_class = "emergency";

                    }

                    ?>


                    <span
                        class="priority <?php echo $priority_class; ?>"
                    >

                        <?php
                        echo htmlspecialchars($priority);
                        ?>

                    </span>


                </div>

            </div>



            <!-- Description -->

            <div class="info-row">

                <div class="label">
                    Description:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['description']
                    );
                    ?>

                </div>

            </div>



            <!-- Current Status -->

            <div class="info-row">

                <div class="label">
                    Current Status:
                </div>

                <div class="value">

                    <?php
                    echo htmlspecialchars(
                        $row['status']
                    );
                    ?>

                </div>

            </div>


        </div>



        <!-- =========================
             UPDATE FORM
             ========================= -->

        <form
            method="POST"
            action=""
        >


            <!-- STATUS -->

            <label>
                Complaint Status
            </label>


            <select
                name="status"
                required
            >


                <option
                    value="Submitted"

                    <?php

                    if ($row['status'] == "Submitted") {

                        echo "selected";

                    }

                    ?>
                >

                    Submitted

                </option>



                <option
                    value="In Progress"

                    <?php

                    if ($row['status'] == "In Progress") {

                        echo "selected";

                    }

                    ?>
                >

                    In Progress

                </option>



                <option
                    value="Resolved"

                    <?php

                    if ($row['status'] == "Resolved") {

                        echo "selected";

                    }

                    ?>
                >

                    Resolved

                </option>



                <option
                    value="Rejected"

                    <?php

                    if ($row['status'] == "Rejected") {

                        echo "selected";

                    }

                    ?>
                >

                    Rejected

                </option>


            </select>



            <!-- ADMIN REMARK -->

            <label>
                Admin Remark
            </label>


            <textarea
                name="admin_remark"
                placeholder="Enter your remark for the user..."
            ><?php

            echo htmlspecialchars(
                $row['admin_remark'] ?? ""
            );

            ?></textarea>



            <!-- BUTTON -->

            <button
                type="submit"
                class="update-btn"
            >

                Update Complaint

            </button>


        </form>



        <!-- BACK -->

        <a
            href="dashboard.php"
            class="back"
        >

            ← Back to Admin Dashboard

        </a>


    </div>

</div>


</body>

</html>
```
