<?php
include 'src/config/database.php';

// Get user_id and status from session
$user_id = $_SESSION['user_id'];  
$role = $_SESSION['role'];  

// Mark notification as read (optional, if you're marking comments as read)
if (isset($_GET['mark_as_read'])) {
    $comment_id = $_GET['mark_as_read'];

    // Sanitize the comment ID to prevent SQL injection
    $comment_id = mysqli_real_escape_string($conn, $comment_id);

    // Update the comment status to 'read' or mark as 'viewed'
    $update_query = "UPDATE tbl_comment SET status = 'read' WHERE id = '$comment_id'";
    mysqli_query($conn, $update_query);
    
    // // Optionally, redirect to avoid form resubmission
    // header("Location: " . $_SERVER['PHP_SELF']);
    // exit;
}

$comment_query = "
    SELECT c.*, name AS name 
    FROM tbl_comment c
    JOIN tbl_user u ON c.sender_id = u.id
    WHERE c.user_id = '$user_id' AND c.status = 'unread' ORDER BY id DESC"; 

    $result = mysqli_query($conn, $comment_query);
// Check if the query was successful and has results
if ($result) {
    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);  // Convert to an array
} else {
    $comments = [];  // Default to an empty array if no results
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Default Title'; ?></title>
    <link rel="icon" type="image/png" href="public/images/aip_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="public/css/style.css">
</head>
<style>
    /* Modal Background */
.notif-modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.4); /* Black background with transparency */
    padding-top: 60px; /* Top margin to center modal */
}

/* Modal Content */
.notif-content {
    background-color: #fff;
    margin: 5% auto; /* Center the modal */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Adjust width as necessary */
    max-width: 800px; /* Optional: restrict modal width */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
}



/* Close Button (X) */
.close-btn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close-btn:hover,
.close-btn:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Comment Item */
.comment-item {
    margin-bottom: 15px;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.comment-item p {
    margin: 5px 0;
}

.comment-item a {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
}

.comment-item a:hover {
    text-decoration: underline;
}

/* Notification Count (Red Circle) */
.notification-count {
    position: absolute;
    bottom:2px;
    right: 10px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 5px 9px;
    font-size: 12px;
    cursor: pointer;
}

.bell{
    position: relative;
    right: 50px;
}
.notification-bell i{
    cursor: pointer;
}

/* For modal responsiveness (ensure it fits well on mobile) */
@media screen and (max-width: 600px) {
    .modal-content {
        width: 90%;
    }

    .comment-item p {
        font-size: 14px;
    }
}

</style>

<body>
    <div class="container">
        <?php include 'public/components/side-bar.php' ?>
        <div class="main">
            <div class="header">
                <div class="header-left-side">
                 <h1><?php echo isset($title) ? $title : 'Default Title'; ?></h1>
                </div>
                <div class="header-right-side">
                   <div class="bell">
                    <div class="notification-bell">
                    <i class="fa-solid fa-bell" onclick="openCommentModal()"></i>
                    <?php if (count($comments) > 0): ?>
                        <span class="notification-count"><?php echo count($comments); ?></span>
                    <?php endif; ?>
                </div>
                    </div>
                    <div class="user"> 
                    <li class="nav-link has-dropdown">
                            <a href="#" class="has-dropdown logged">
                                <!-- Display the department email from session -->
                                <?php 
                                    echo $_SESSION['username'];  // Display the email stored in session
                                ?>
                            </a>
                        </li>
                    </div>
                </div>
            </div>
        
            <div id="commentModal" class="notif-modal" style="display:none;">
            <div class="notif-content">
                <span class="close-btn" onclick="closeCommentModal()">×</span>
                <h2>Feedback</h2>

        <!-- Loop through comments and display them -->
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <p><strong>From: <?php echo htmlspecialchars($comment['name']); ?></strong></p>
                            <p><strong>AIP Ref: <?php echo htmlspecialchars($comment['aip_ref_code'])  ?></strong></p>
                            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                            <a href="?mark_as_read=<?php echo $comment['id']; ?>" class="mark-read">Mark as read</a>
                        </div>      
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No new comments.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add JavaScript for modal functionality -->
        <script>
            function openCommentModal() {
                document.getElementById("commentModal").style.display = "block";
                
                // Reset the notification count on opening modal
                document.querySelector(".notification-count").style.display = "none";

                // Optionally, you can make an AJAX call to update the notification count in the backend (mark all comments as read)
                fetch("mark_all_as_read.php"); // You'll need to create this PHP file to update status to 'read' for all unread comments
            }

            function closeCommentModal() {
                document.getElementById("commentModal").style.display = "none";
            }

            window.onclick = function(event) {
                var modal = document.getElementById('commentModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };
        </script>