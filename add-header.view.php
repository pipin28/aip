<?php
//FIle for Add-Header View
include 'src/config/database.php';

session_start(); 


if (!isset($_SESSION['username'])) {
    header("Location: router.php?page=login"); 
    exit;
}

$user_id = $_SESSION['user_id']; 
include 'src/controller/header.controller.php';

?>
<?php include 'public/components/header.php'; ?>
<div class="hero">
    <form action="" method="post">
        <div class="form-children">
            <div class="form-container-header">
                <h1>Annual Investment Program (AIP) Submission</h1>
                <p>Please fill out the details below for the Annual Program Plan. This information will be securely managed and not shared externally.</p>
            </div>

            <h4>AIP Header</h4>
            <div class="form-row">
                <div class="form-col">
                    <!-- Hidden input for department_id, which is user_id from session -->
                    <input type="hidden" name="department_id" value="<?php echo $user_id; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="signatory_one">Department Signatory</label>
                    <input type="text" name="signatory_one" placeholder="Enter department signatory" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label for="signatory_two">Budget Signatory</label>
                    <input type="text" name="signatory_two" placeholder="Enter budget signatory" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label for="signatory_three">Executive Signatory</label>
                    <input type="text" name="signatory_three" placeholder="Enter executive signatory" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <button type="submit" id="add-child">Submit</button><br><br>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>
