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

$message = "";

/* Submit Complaint */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $department_id = $_POST['department_id'];
    $subject = $_POST['subject'];
$description = $_POST['description'];
$priority = $_POST['priority'];

  $sql = "INSERT INTO complaints 
        (user_id, category_id, department_id, subject, description, priority) 
        VALUES 
        ('$user_id', '$category_id', '$department_id', '$subject', '$description', '$priority')";

    if (mysqli_query($conn, $sql)) {
        $message = "Complaint submitted successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Register Complaint</title>

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

        /* Main container */

        .container {
            width: 90%;
            max-width: 750px;
            margin: 40px auto;
        }

        .form-box {
            background-color: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .form-box h1 {
            color: #1f3c88;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 30px;
        }

        /* Success message */

        .success {
            background-color: #e8f8ee;
            color: #218838;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        /* Form */

        label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        select,
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
            margin-bottom: 22px;
        }

        select:focus,
        input[type="text"]:focus,
        textarea:focus {
            border-color: #1f3c88;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

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
                padding: 25px;
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


<!-- Main Content -->

<div class="container">

    <div class="form-box">

        <h1>Register Complaint</h1>

        <p class="subtitle">
            Submit your complaint to the concerned department.
        </p>


        <?php if ($message != "") { ?>

            <div class="success">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST" action="">

            <!-- Category -->

            <label>Complaint Category</label>

            <select name="category_id" required>

                <option value="">
                    -- Select Category --
                </option>

                <?php

                $category_query = "SELECT * FROM categories ORDER BY category_name ASC";
                $category_result = mysqli_query($conn, $category_query);

                while ($category = mysqli_fetch_assoc($category_result)) {

                ?>

                    <option value="<?php echo $category['category_id']; ?>">

                        <?php echo htmlspecialchars($category['category_name']); ?>

                    </option>

                <?php } ?>

            </select>


            <!-- Department -->

            <label>Department</label>

            <select name="department_id" required>

                <option value="">
                    -- Select Department --
                </option>

                <?php

                $department_query = "SELECT * FROM departments ORDER BY department_name ASC";
                $department_result = mysqli_query($conn, $department_query);

                while ($department = mysqli_fetch_assoc($department_result)) {

                ?>

                    <option value="<?php echo $department['department_id']; ?>">

                        <?php echo htmlspecialchars($department['department_name']); ?>

                    </option>

                <?php } ?>

            </select>


           <!-- Subject -->

<label>Complaint Subject</label>

<input
    type="text"
    name="subject"
    placeholder="Enter complaint subject"
    required
>


<!-- Priority -->

<label>Complaint Priority</label>

<select name="priority" required>

    <option value="">-- Select Priority --</option>

    <option value="Low">🟢 Low</option>

    <option value="Medium">🟡 Medium</option>

    <option value="High">🔴 High</option>

    <option value="Emergency">🚨 Emergency</option>

</select>


<!-- Description -->

<label>Complaint Description</label>

<textarea
    name="description"
    placeholder="Describe your complaint in detail..."
    required
></textarea>


            <!-- Submit -->

            <button type="submit" class="submit-btn">
                Submit Complaint
            </button>

        </form>


        <a href="dashboard.php" class="back">
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>

</html>