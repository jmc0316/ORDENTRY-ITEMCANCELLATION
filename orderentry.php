<?php
include '../config/db.php';

$total_amount = 0;
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $products = $_POST['products'];

    $stmt = $conn->prepare("INSERT INTO orders (total_amount) VALUES (?)");

    foreach ($products as $p) {
        $total_amount += $p['quantity'] * $p['unit_price'];
    }
    $stmt->bind_param("d", $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $item_stmt = $conn->prepare("INSERT INTO order_items 
        (order_id, product_code, name, quantity, unit_price, total_price) 
        VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($products as $p) {
        $total_price = $p['quantity'] * $p['unit_price'];
        $item_stmt->bind_param("issidd", $order_id, $p['product_code'], $p['name'], $p['quantity'], $p['unit_price'], $total_price);
        $item_stmt->execute();
    }

    $message = "Order saved successfully. Total: $" . number_format($total_amount, 2);
}
?>

<h2>Order Entry</h2>

<form method="POST">
    <table id="orderTable" border="1" cellpadding="8">
        <tr>
            <th>Product Code</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Unit Price</th>
        </tr>
        <tr>
            <td><input type="text" name="products[0][product_code]" required></td>
            <td><input type="text" name="products[0][name]" required></td>
            <td><input type="number" name="products[0][quantity]" required></td>
            <td><input type="number" step="0.01" name="products[0][unit_price]" required></td>
        </tr>
    </table>
    <br>
    <input type="submit" value="Submit Order">
</form>

<?php if ($message): ?>
    <p><strong><?php echo $message; ?></strong></p>
<?php endif; ?>