<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "aip_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users
$users = $conn->query("SELECT * FROM tbl_user");

// Check if a user ID is selected to show login records
$selectedUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$logins = [];
if ($selectedUserId) {
    $stmt = $conn->prepare("SELECT login_time FROM tbl_login_records WHERE user_id = ?");
    $stmt->bind_param("i", $selectedUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $logins[] = $row['login_time'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login Monitor</title>
    <style>
        table, th, td {
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 8px;
        }
        th { background-color: #f2f2f2; }
        .calendar { display: flex; flex-wrap: wrap; width: 280px; }
        .day { width: 40px; height: 40px; border: 1px solid #ccc; text-align: center; line-height: 40px; }
        .highlight { background-color: #a0e0a0; }
    </style>
</head>
<body>

<h2>User List</h2>
<table>
    <thead>
        <tr><th>ID</th><th>Name</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><a href="?user_id=<?= $user['id'] ?>">View Logins</a></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php if ($selectedUserId): ?>
    <h2>Login Records</h2>
    <h3><?= date("F Y") ?></h3>
    <div class="calendar">
        <?php
        $daysInMonth = date('t');
        $month = date('m');
        $year = date('Y');
        $loginDays = [];

        foreach ($logins as $login) {
            $day = date('j', strtotime($login));
            $loginDays[] = intval($day);
        }

        for ($day = 1; $day <= $daysInMonth; $day++): 
            $highlight = in_array($day, $loginDays) ? 'highlight' : '';
        ?>
            <div class="day <?= $highlight ?>"><?= $day ?></div>
        <?php endfor; ?>
    </div>
<?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
