```php
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
 
$admin_name = $_SESSION['admin_name']; 
 
$message = ""; 
$message_type = ""; 
 
 
/* ===================================================== 
   DELETE COMPLAINT
   ===================================================== */ 
 
if ( 
    $_SERVER["REQUEST_METHOD"] == "POST" && 
    isset($_POST['delete_complaint']) 
) { 
 
    $complaint_id = intval($_POST['complaint_id']); 
 
    if ($complaint_id > 0) { 
 
        /* Delete feedback related to complaint */ 
 
        $feedback_delete = " 
            DELETE FROM feedback 
            WHERE complaint_id = ? 
        "; 
 
        $feedback_stmt = mysqli_prepare( 
            $conn, 
            $feedback_delete 
        ); 
 
        if ($feedback_stmt) { 
 
            mysqli_stmt_bind_param( 
                $feedback_stmt, 
                "i", 
                $complaint_id 
            ); 
 
            mysqli_stmt_execute($feedback_stmt); 
 
            mysqli_stmt_close($feedback_stmt); 
        } 
 
 
        /* Delete complaint */ 
 
        $complaint_delete = " 
            DELETE FROM complaints 
            WHERE complaint_id = ? 
        "; 
 
        $complaint_stmt = mysqli_prepare( 
            $conn, 
            $complaint_delete 
        ); 
 
        if ($complaint_stmt) { 
 
            mysqli_stmt_bind_param( 
                $complaint_stmt, 
                "i", 
                $complaint_id 
            ); 
 
            if (mysqli_stmt_execute($complaint_stmt)) { 
 
                if (mysqli_stmt_affected_rows($complaint_stmt) > 0) { 
 
                    $message = 
                        "Complaint #" . 
                        $complaint_id . 
                        " deleted successfully."; 
 
                    $message_type = "success"; 
 
                } else { 
 
                    $message = "Complaint not found."; 
 
                    $message_type = "error"; 
                } 
 
            } else { 
 
                $message = "Unable to delete complaint."; 
 
                $message_type = "error"; 
            } 
 
            mysqli_stmt_close($complaint_stmt); 
 
        } else { 
 
            $message = "Delete query failed."; 
 
            $message_type = "error"; 
        } 
 
    } else { 
 
        $message = "Invalid complaint ID."; 
 
        $message_type = "error"; 
    } 
} 
 
 
/* ===================================================== 
   DASHBOARD STATISTICS 
   ===================================================== */ 
 
 
/* Total Complaints */ 
 
$total_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
"; 
 
$total_result = mysqli_query($conn, $total_query); 
 
if (!$total_result) { 
    die("Total complaints query failed: " . mysqli_error($conn)); 
} 
 
$total = mysqli_fetch_assoc($total_result)['total']; 
 
 
/* Submitted */ 
 
$submitted_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
    WHERE status = 'Submitted' 
"; 
 
$submitted_result = mysqli_query($conn, $submitted_query); 
 
if (!$submitted_result) { 
    die("Submitted query failed: " . mysqli_error($conn)); 
} 
 
$submitted = mysqli_fetch_assoc($submitted_result)['total']; 
 
 
/* In Progress */ 
 
$progress_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
    WHERE status = 'In Progress' 
"; 
 
$progress_result = mysqli_query($conn, $progress_query); 
 
if (!$progress_result) { 
    die("Progress query failed: " . mysqli_error($conn)); 
} 
 
$progress = mysqli_fetch_assoc($progress_result)['total']; 
 
 
/* Resolved */ 
 
$resolved_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
    WHERE status = 'Resolved' 
"; 
 
$resolved_result = mysqli_query($conn, $resolved_query); 
 
if (!$resolved_result) { 
    die("Resolved query failed: " . mysqli_error($conn)); 
} 
 
$resolved = mysqli_fetch_assoc($resolved_result)['total']; 
 
 
/* Rejected */ 
 
$rejected_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
    WHERE status = 'Rejected' 
"; 
 
$rejected_result = mysqli_query($conn, $rejected_query); 
 
if (!$rejected_result) { 
    die("Rejected query failed: " . mysqli_error($conn)); 
} 
 
$rejected = mysqli_fetch_assoc($rejected_result)['total']; 
 
 
/* High Priority */ 
 
$high_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
    WHERE priority = 'High' 
"; 
 
$high_result = mysqli_query($conn, $high_query); 
 
if (!$high_result) { 
    die("High priority query failed: " . mysqli_error($conn)); 
} 
 
$high = mysqli_fetch_assoc($high_result)['total']; 
 
 
/* Emergency Priority */ 
 
$emergency_query = " 
    SELECT COUNT(*) AS total 
    FROM complaints 
    WHERE priority = 'Emergency' 
"; 
 
$emergency_result = mysqli_query($conn, $emergency_query); 
 
if (!$emergency_result) { 
    die("Emergency priority query failed: " . mysqli_error($conn)); 
} 
 
$emergency = mysqli_fetch_assoc($emergency_result)['total']; 
 
 
/* ===================================================== 
   FEEDBACK COUNT 
   ===================================================== */ 
 
$feedback_query = " 
    SELECT COUNT(*) AS total 
    FROM feedback 
"; 
 
$feedback_result = mysqli_query($conn, $feedback_query); 
 
if (!$feedback_result) { 
    die("Feedback query failed: " . mysqli_error($conn)); 
} 
 
$total_feedback = mysqli_fetch_assoc($feedback_result)['total']; 
 
 
/* ===================================================== 
   ALL COMPLAINTS 
   ===================================================== */ 
 
$sql = " 
    SELECT 
        complaints.complaint_id, 
        users.full_name, 
        users.email, 
        categories.category_name, 
        departments.department_name, 
        complaints.subject, 
        complaints.priority, 
        complaints.description, 
        complaints.status, 
        complaints.admin_remark, 
        complaints.created_at 
 
    FROM complaints 
 
    INNER JOIN users 
        ON complaints.user_id = users.user_id 
 
    INNER JOIN categories 
        ON complaints.category_id = categories.category_id 
 
    INNER JOIN departments 
        ON complaints.department_id = departments.department_id 
 
    ORDER BY complaints.created_at DESC 
"; 
 
$result = mysqli_query($conn, $sql); 
 
if (!$result) { 
    die("Complaint query failed: " . mysqli_error($conn)); 
} 
 
?> 
 
<!DOCTYPE html> 
 
<html> 
 
<head> 
 
    <title> 
        Admin Dashboard - Complaint Management System 
    </title> 
 
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
 
 
        /* ===================================================== 
           NAVBAR 
           ===================================================== */ 
 
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
            gap: 10px; 
        } 
 
        .admin-name { 
            font-size: 15px; 
            margin-right: 10px; 
        } 
 
        /* ADD NEW ADMIN BUTTON */ 
 
        .add-admin { 
            color: white; 
            text-decoration: none; 
 
            background-color: #198754; 
 
            padding: 10px 16px; 
 
            border-radius: 6px; 
 
            font-weight: bold; 
        } 
 
        .add-admin:hover { 
            background-color: #146c43; 
        } 
 
        /* LOGOUT BUTTON */ 
 
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
 
 
        /* ===================================================== 
           MAIN CONTAINER 
           ===================================================== */ 
 
        .container { 
            width: 95%; 
            max-width: 1500px; 
 
            margin: 35px auto; 
        } 
 
 
        /* ===================================================== 
           MESSAGE 
           ===================================================== */ 
 
        .message { 
            padding: 14px 18px; 
 
            border-radius: 8px; 
 
            margin-bottom: 25px; 
 
            font-weight: bold; 
        } 
 
        .success-message { 
            background-color: #d1e7dd; 
            color: #0f5132; 
            border-left: 5px solid #198754; 
        } 
 
        .error-message { 
            background-color: #f8d7da; 
            color: #842029; 
            border-left: 5px solid #dc3545; 
        } 
 
 
        /* ===================================================== 
           WELCOME 
           ===================================================== */ 
 
        .welcome { 
            background-color: white; 
 
            padding: 25px 30px; 
 
            border-radius: 12px; 
 
            margin-bottom: 25px; 
 
            box-shadow: 0 3px 10px rgba(0,0,0,0.08); 
        } 
 
        .welcome h1 { 
            color: #172b5f; 
 
            margin-bottom: 8px; 
        } 
 
        .welcome p { 
            color: #777; 
        } 
 
 
        /* ===================================================== 
           STATISTICS 
           ===================================================== */ 
 
        .stats-grid { 
            display: grid; 
 
            grid-template-columns: repeat(8, 1fr); 
 
            gap: 12px; 
 
            margin-bottom: 25px; 
        } 
 
        .stat-card { 
            background-color: white; 
 
            padding: 18px 10px; 
 
            border-radius: 12px; 
 
            box-shadow: 0 3px 10px rgba(0,0,0,0.08); 
 
            text-align: center; 
 
            border-top: 5px solid #172b5f; 
        } 
 
        .stat-icon { 
            font-size: 26px; 
 
            margin-bottom: 8px; 
        } 
 
        .stat-number { 
            font-size: 26px; 
 
            font-weight: bold; 
 
            color: #172b5f; 
 
            margin-bottom: 5px; 
        } 
 
        .stat-title { 
            color: #777; 
 
            font-size: 13px; 
 
            font-weight: bold; 
        } 
 
        .submitted-card { 
            border-top-color: #f0ad00; 
        } 
 
        .progress-card { 
            border-top-color: #0d6efd; 
        } 
 
        .resolved-card { 
            border-top-color: #198754; 
        } 
 
        .rejected-card { 
            border-top-color: #dc3545; 
        } 
 
        .high-card { 
            border-top-color: #dc3545; 
        } 
 
        .emergency-card { 
            border-top-color: #b00020; 
        } 
 
 
        /* ===================================================== 
           FEEDBACK SECTION 
           ===================================================== */ 
 
        .feedback-section { 
            background-color: white; 
 
            padding: 22px 25px; 
 
            border-radius: 12px; 
 
            margin-bottom: 25px; 
 
            box-shadow: 0 3px 10px rgba(0,0,0,0.08); 
 
            display: flex; 
 
            justify-content: space-between; 
 
            align-items: center; 
 
            gap: 20px; 
 
            border-left: 5px solid #f0a500; 
        } 
 
        .feedback-text h2 { 
            color: #172b5f; 
 
            font-size: 20px; 
 
            margin-bottom: 6px; 
        } 
 
        .feedback-text p { 
            color: #777; 
 
            line-height: 1.5; 
        } 
 
        .feedback-count { 
            color: #172b5f; 
 
            font-weight: bold; 
        } 
 
        .feedback-btn { 
            display: inline-block; 
 
            background-color: #f0a500; 
 
            color: white; 
 
            text-decoration: none; 
 
            padding: 12px 20px; 
 
            border-radius: 7px; 
 
            font-weight: bold; 
 
            white-space: nowrap; 
        } 
 
        .feedback-btn:hover { 
            background-color: #d89000; 
        } 
 
 
        /* ===================================================== 
           TABLE 
           ===================================================== */ 
 
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
 
            min-width: 1400px; 
        } 
 
        th { 
            background-color: #172b5f; 
 
            color: white; 
 
            padding: 13px; 
 
            text-align: left; 
 
            white-space: nowrap; 
        } 
 
        td { 
            padding: 12px; 
 
            border-bottom: 1px solid #eee; 
 
            color: #444; 
 
            vertical-align: top; 
        } 
 
        tr:hover { 
            background-color: #f8f9fc; 
        } 
 
 
        /* ===================================================== 
           STATUS BADGES 
           ===================================================== */ 
 
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
 
 
        /* ===================================================== 
           PRIORITY BADGES 
           ===================================================== */ 
 
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
 
 
        /* ===================================================== 
           REMARK 
           ===================================================== */ 
 
        .remark { 
            max-width: 220px; 
 
            line-height: 1.4; 
        } 
 
        .no-remark { 
            color: #999; 
 
            font-style: italic; 
        } 
 
 
        /* ===================================================== 
           ACTION BUTTONS 
           ===================================================== */ 
 
        .action-buttons { 
            display: flex; 
 
            gap: 6px; 
 
            align-items: center; 
        } 
 
        .update-btn { 
            display: inline-block; 
 
            background-color: #1f3c88; 
 
            color: white; 
 
            padding: 8px 14px; 
 
            border-radius: 6px; 
 
            text-decoration: none; 
 
            font-size: 13px; 
 
            white-space: nowrap; 
 
            border: none; 
 
            cursor: pointer; 
        } 
 
        .update-btn:hover { 
            background-color: #162d68; 
        } 
 
        .delete-btn { 
            display: inline-block; 
 
            background-color: #dc3545; 
 
            color: white; 
 
            padding: 8px 14px; 
 
            border-radius: 6px; 
 
            text-decoration: none; 
 
            font-size: 13px; 
 
            white-space: nowrap; 
 
            border: none; 
 
            cursor: pointer; 
        } 
 
        .delete-btn:hover { 
            background-color: #b02a37; 
        } 
 
 
        /* ===================================================== 
           NO COMPLAINTS 
           ===================================================== */ 
 
        .no-complaints { 
            text-align: center; 
 
            padding: 40px; 
 
            color: #777; 
        } 
 
 
        /* ===================================================== 
           MOBILE 
           ===================================================== */ 
 
        @media (max-width: 1300px) { 
 
            .stats-grid { 
                grid-template-columns: repeat(4, 1fr); 
            } 
 
        } 
 
 
        @media (max-width: 900px) { 
 
            .stats-grid { 
                grid-template-columns: repeat(2, 1fr); 
            } 
 
        } 
 
 
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
 
            .add-admin { 
                padding: 8px 10px; 
                font-size: 12px; 
            } 
 
            .logout { 
                padding: 8px 10px; 
                font-size: 12px; 
            } 
 
            .container { 
                width: 95%; 
            } 
 
            .stats-grid { 
                grid-template-columns: repeat(2, 1fr); 
            } 
 
            .feedback-section { 
                flex-direction: column; 
 
                align-items: flex-start; 
            } 
 
            .feedback-btn { 
                width: 100%; 
 
                text-align: center; 
            } 
 
        } 
 
 
        @media (max-width: 450px) { 
 
            .stats-grid { 
                grid-template-columns: 1fr; 
            } 
 
            .admin-info { 
                gap: 5px; 
            } 
 
        } 
 
    </style> 
 
</head> 
 
 
<body> 
 
 
<!-- ===================================================== 
     NAVBAR 
     ===================================================== --> 
 
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
 
        <!-- ADD NEW ADMIN --> 
 
        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'Super Admin') { ?>

    <a href="add_admin.php" class="add-admin">
        ➕ Add New Admin
    </a>

<?php } ?>
 
        <!-- LOGOUT --> 
 
        <a 
            href="logout.php" 
            class="logout" 
        > 
            Logout 
        </a> 
 
    </div> 
 
</div> 
 
 
<!-- ===================================================== 
     MAIN CONTAINER 
     ===================================================== --> 
 
<div class="container"> 
 
 
    <!-- ================================================= 
         MESSAGE 
         ================================================= --> 
 
    <?php if ($message != "") { ?> 
 
        <div 
            class="message 
            <?php 
            echo ( 
                $message_type == "success" 
                ? "success-message" 
                : "error-message" 
            ); 
            ?>" 
        > 
 
            <?php 
            echo htmlspecialchars($message); 
            ?> 
 
        </div> 
 
    <?php } ?> 
 
 
    <!-- ================================================= 
         WELCOME 
         ================================================= --> 
 
    <div class="welcome"> 
 
        <h1> 
            Admin Dashboard 
        </h1> 
 
        <p> 
 
            Welcome, 
            <?php 
            echo htmlspecialchars($admin_name); 
            ?>! 
 
            Manage and monitor all registered complaints. 
 
        </p> 
 
    </div> 
 
 
    <!-- ================================================= 
         STATISTICS 
         ================================================= --> 
 
    <div class="stats-grid"> 
 
        <!-- Total --> 
 
        <div class="stat-card"> 
 
            <div class="stat-icon"> 
                📋 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $total; ?> 
            </div> 
 
            <div class="stat-title"> 
                Total Complaints 
            </div> 
 
        </div> 
 
 
        <!-- Submitted --> 
 
        <div class="stat-card submitted-card"> 
 
            <div class="stat-icon"> 
                🟡 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $submitted; ?> 
            </div> 
 
            <div class="stat-title"> 
                Submitted 
            </div> 
 
        </div> 
 
 
        <!-- In Progress --> 
 
        <div class="stat-card progress-card"> 
 
            <div class="stat-icon"> 
                🔵 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $progress; ?> 
            </div> 
 
            <div class="stat-title"> 
                In Progress 
            </div> 
 
        </div> 
 
 
        <!-- Resolved --> 
 
        <div class="stat-card resolved-card"> 
 
            <div class="stat-icon"> 
                🟢 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $resolved; ?> 
            </div> 
 
            <div class="stat-title"> 
                Resolved 
            </div> 
 
        </div> 
 
 
        <!-- Rejected --> 
 
        <div class="stat-card rejected-card"> 
 
            <div class="stat-icon"> 
                ❌ 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $rejected; ?> 
            </div> 
 
            <div class="stat-title"> 
                Rejected 
            </div> 
 
        </div> 
 
 
        <!-- High --> 
 
        <div class="stat-card high-card"> 
 
            <div class="stat-icon"> 
                🔴 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $high; ?> 
            </div> 
 
            <div class="stat-title"> 
                High Priority 
            </div> 
 
        </div> 
 
 
        <!-- Emergency --> 
 
        <div class="stat-card emergency-card"> 
 
            <div class="stat-icon"> 
                🚨 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $emergency; ?> 
            </div> 
 
            <div class="stat-title"> 
                Emergency 
            </div> 
 
        </div> 
 
 
        <!-- Feedback --> 
 
        <div class="stat-card"> 
 
            <div class="stat-icon"> 
                ⭐ 
            </div> 
 
            <div class="stat-number"> 
                <?php echo $total_feedback; ?> 
            </div> 
 
            <div class="stat-title"> 
                User Feedback 
            </div> 
 
        </div> 
 
    </div> 
 
 
    <!-- ================================================= 
         USER FEEDBACK 
         ================================================= --> 
 
    <div class="feedback-section"> 
 
        <div class="feedback-text"> 
 
            <h2> 
                ⭐ User Feedback 
            </h2> 
 
            <p> 
 
                View feedback and ratings submitted by users. 
 
                Total Feedback: 
 
                <span class="feedback-count"> 
                    <?php echo $total_feedback; ?> 
                </span> 
 
            </p> 
 
        </div> 
 
 
        <a 
            href="feedback.php" 
            class="feedback-btn" 
        > 
            ⭐ View Feedback 
        </a> 
 
    </div> 
 
 
    <!-- ================================================= 
         ALL COMPLAINTS 
         ================================================= --> 
 
    <div class="table-box"> 
 
        <div class="table-title"> 
            All Complaints 
        </div> 
 
 
        <?php if (mysqli_num_rows($result) > 0) { ?> 
 
            <table> 
 
                <tr> 
 
                    <th>ID</th> 
 
                    <th>User</th> 
 
                    <th>Email</th> 
 
                    <th>Category</th> 
 
                    <th>Department</th> 
 
                    <th>Subject</th> 
 
                    <th>Priority</th> 
 
                    <th>Description</th> 
 
                    <th>Status</th> 
 
                    <th>Admin Remark</th> 
 
                    <th>Date</th> 
 
                    <th>Action</th> 
 
                </tr> 
 
 
                <?php while ($row = mysqli_fetch_assoc($result)) { ?> 
 
                    <tr> 
 
                        <!-- ID --> 
 
                        <td> 
 
                            <?php 
                            echo htmlspecialchars( 
                                $row['complaint_id'] 
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
 
                        <!-- Category --> 
 
                        <td> 
 
                            <?php 
                            echo htmlspecialchars( 
                                $row['category_name'] 
                            ); 
                            ?> 
 
                        </td> 
 
                        <!-- Department --> 
 
                        <td> 
 
                            <?php 
                            echo htmlspecialchars( 
                                $row['department_name'] 
                            ); 
                            ?> 
 
                        </td> 
 
                        <!-- Subject --> 
 
                        <td> 
 
                            <?php 
                            echo htmlspecialchars( 
                                $row['subject'] 
                            ); 
                            ?> 
 
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
 
                            <span 
                                class="priority 
                                <?php echo $priority_class; ?>" 
                            > 
 
                                <?php 
                                echo htmlspecialchars( 
                                    $row['priority'] 
                                ); 
                                ?> 
 
                            </span> 
 
                        </td> 
 
                        <!-- Description --> 
 
                        <td> 
 
                            <?php 
                            echo htmlspecialchars( 
                                $row['description'] 
                            ); 
                            ?> 
 
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
 
                            <span 
                                class="status 
                                <?php echo $status_class; ?>" 
                            > 
 
                                <?php 
                                echo htmlspecialchars( 
                                    $row['status'] 
                                ); 
                                ?> 
 
                            </span> 
 
                        </td> 
 
                        <!-- Admin Remark --> 
 
                        <td class="remark"> 
 
                            <?php 
 
                            if (!empty($row['admin_remark'])) { 
 
                                echo htmlspecialchars( 
                                    $row['admin_remark'] 
                                ); 
 
                            } else { 
 
                            ?> 
 
                                <span class="no-remark"> 
                                    No remark 
                                </span> 
 
                            <?php 
                            } 
                            ?> 
 
                        </td> 
 
                        <!-- Date --> 
 
                        <td> 
 
                            <?php 
                            echo htmlspecialchars( 
                                $row['created_at'] 
                            ); 
                            ?> 
 
                        </td> 
 
                        <!-- ACTION --> 
 
                        <td> 
 
                            <div class="action-buttons"> 
 
                                <!-- UPDATE --> 
 
                                <a 
                                    href="update_complaint.php?id=<?php echo $row['complaint_id']; ?>" 
                                    class="update-btn" 
                                > 
                                    Update 
                                </a> 
 
                                <!-- DELETE --> 
 
                                <form 
                                    method="POST" 
                                    action="" 
                                    style="display:inline;" 
                                    onsubmit="return confirm('Are you sure you want to delete Complaint #<?php echo $row['complaint_id']; ?>? This will also delete its feedback.');" 
                                > 
 
                                    <input 
                                        type="hidden" 
                                        name="complaint_id" 
                                        value="<?php echo $row['complaint_id']; ?>" 
                                    > 
 
                                    <button 
                                        type="submit" 
                                        name="delete_complaint" 
                                        class="delete-btn" 
                                    > 
                                        Delete 
                                    </button> 
 
                                </form> 
 
                            </div> 
 
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
                    There are currently no registered complaints. 
                </p> 
 
            </div> 
 
        <?php } ?> 
 
    </div> 
 
</div> 
 
 
</body> 
 
</html>
```
