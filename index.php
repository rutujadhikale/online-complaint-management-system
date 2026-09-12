```php
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Online Complaint Management System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            color: #333;
        }

        /* Navbar */

        .navbar {
            background: #1f3c88;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .nav-buttons a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            padding: 9px 16px;
            border-radius: 6px;
            border: 1px solid white;
        }

        .nav-buttons a:hover {
            background: white;
            color: #1f3c88;
        }

        /* Hero Section */

        .hero {
            min-height: 430px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 50px 20px;
            background: linear-gradient(135deg, #1f3c88, #4d7cff);
            color: white;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .hero-buttons a {
            display: inline-block;
            text-decoration: none;
            padding: 13px 25px;
            margin: 5px;
            border-radius: 7px;
            font-weight: bold;
        }

        .user-btn {
            background: white;
            color: #1f3c88;
        }

        .admin-btn {
            border: 2px solid white;
            color: white;
        }

        .hero-buttons a:hover {
            opacity: 0.85;
        }

        /* Features */

        .features {
            padding: 50px 7%;
            text-align: center;
        }

        .features h2 {
            color: #1f3c88;
            margin-bottom: 35px;
            font-size: 30px;
        }

        .feature-container {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .feature-card {
            background: white;
            width: 260px;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .feature-card .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #1f3c88;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.5;
        }

        /* Footer */

        footer {
            background: #172b5f;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 20px;
        }

        /* Mobile */

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .nav-buttons a {
                margin: 3px;
            }

            .hero h1 {
                font-size: 30px;
            }

            .hero p {
                font-size: 16px;
            }

            .feature-card {
                width: 90%;
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

    <div class="nav-buttons">

        <a href="user/login.php">
            User Login
        </a>

        <a href="admin/login.php">
            Admin Login
        </a>

    </div>

</div>


<!-- Hero Section -->

<section class="hero">

    <div class="hero-content">

        <h1>
            Online Complaint Registration and Management System
        </h1>

        <p>
            A simple and efficient platform for registering,
            tracking and managing complaints online.
        </p>

        <div class="hero-buttons">

            <a href="user/register.php" class="user-btn">
                Register as User
            </a>

            <a href="user/login.php" class="user-btn">
                User Login
            </a>

            <a href="admin/login.php" class="admin-btn">
                Admin Login
            </a>

        </div>

    </div>

</section>


<!-- Features -->

<section class="features">

    <h2>
        System Features
    </h2>

    <div class="feature-container">

        <div class="feature-card">

            <div class="icon">📝</div>

            <h3>
                Register Complaint
            </h3>

            <p>
                Users can easily register their complaints
                and submit them to the concerned department.
            </p>

        </div>


        <div class="feature-card">

            <div class="icon">📊</div>

            <h3>
                Track Complaint
            </h3>

            <p>
                Users can check the current status and
                progress of their submitted complaints.
            </p>

        </div>


        <div class="feature-card">

            <div class="icon">🛡️</div>

            <h3>
                Admin Management
            </h3>

            <p>
                Administrators can view complaints and
                update their status and remarks.
            </p>

        </div>


        <div class="feature-card">

            <div class="icon">⭐</div>

            <h3>
                Feedback
            </h3>

            <p>
                Users can provide feedback about their
                complaint resolution experience.
            </p>

        </div>

    </div>

</section>


<!-- Footer -->

<footer>

    <p>
        © 2026 Online Complaint Management System
    </p>

    <p>
        Final Year Computer Engineering Project
    </p>

</footer>


</body>

</html>
```
