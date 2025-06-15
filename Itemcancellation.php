<?php
include '../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_code = $_POST['product_code'];
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];
    $cancel_date = $_POST['cancel_date'];

    $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE username = ?");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($admin_password, $admin['password_hash'])) {
            $insert = $conn->prepare("INSERT INTO cancellations (product_code, admin_username, cancel_date) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $product_code, $admin_username, $cancel_date);
            if ($insert->execute()) {
                $message = "Item cancellation recorded successfully.";
            } else {
                $message = "Error recording cancellation.";
            }
        } else {
            $message = "Invalid admin password.";
        }
    } else {
        $message = "Admin user not found.";
    }
}
?>

<h2>Item Cancellation</h2>

<form method="POST">
    <label>Product Code:</label><br>
    <input type="text" name="product_code" required><br><br>

    <label>Admin Username:</label><br>
    <input type="text" name="admin_username" required><br><br>

    <label>Admin Password:</label><br>
    <input type="password" name="admin_password" required><br><br>

    <label>Cancellation Date:</label><br>
    <input type="date" name="cancel_date" value="<?php echo date('Y-m-d'); ?>" required><br><br>

    <input type="submit" value="Cancel Item">
</form>

<?php if ($message): ?>
    <p><strong><?php echo $message; ?></strong></p>
<?php endif; ?>