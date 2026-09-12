```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html>

<head>

    <title>User Dashboard - Complaint Management System</title>

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
           MAIN CONTAINER
        ========================= */

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        /* =========================
           WELCOME BOX
        ========================= */

        .welcome {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .welcome h1 {
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #666;
            font-size: 16px;
        }

        /* =========================
           OPTIONS
        ========================= */

        .options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        /* =========================
           CARDS
        ========================= */

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .card h3 {
            color: #1f3c88;
            margin-bottom: 12px;
            font-size: 20px;
        }

        .card p {
            color: #777;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* =========================
           BUTTON
        ========================= */

        .button {
            display: inline-block;
            background-color: #1f3c88;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }

        .button:hover {
            background-color: #162d68;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 800px) {

            .navbar {
                padding: 15px 20px;
            }

            .logo {
                font-size: 17px;
            }

            .container {
                width: 95%;
            }

            .options {
                grid-template-columns: 1fr;
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

    <!-- Welcome -->

    <div class="welcome">

        <h1>
            Welcome, <?php echo htmlspecialchars($user_name); ?>!
        </h1>

        <p>
            Manage your complaints easily from your dashboard.
        </p>

    </div>


    <!-- Dashboard Options -->

    <div class="options">


        <!-- Register Complaint -->

        <div class="card">

            <h3>📝 Register Complaint</h3>

            <p>
                Submit a new complaint to the concerned department.
            </p>

            <a href="register_complaint.php" class="button">
                Register Complaint
            </a>

        </div>


        <!-- My Complaints -->

        <div class="card">

            <h3>📋 My Complaints</h3>

            <p>
                View all complaints submitted by you along with their status and admin remarks.
            </p>

            <a href="my_complaints.php" class="button">
                View Complaints
            </a>

        </div>


        <!-- Complaint Status -->

        <div class="card">

            <h3>📊 Complaint Status</h3>

            <p>
                Check the current status of your registered complaints.
            </p>

            <a href="complaint_status.php" class="button">
                Check Status
            </a>

        </div>


        <!-- Give Feedback -->

        <div class="card">

            <h3>⭐ Give Feedback</h3>

            <p>
                Give feedback about your complaint experience.
            </p>

            <a href="feedback.php" class="button">
                Give Feedback
            </a>

        </div>


    </div>

</div>

</body>

</html>
```
