<?php
require 'db_connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Request Fetch Query
$sqlOrders = "
    SELECT o.OrderID, o.OrderDate, o.Status, o.TotalPrice, 
           GROUP_CONCAT(CONCAT(od.Quantity, ' x ', p.ProductName) ORDER BY od.ProductID SEPARATOR ', ') AS Products,
           CONCAT(c.FirstName, ' ', c.LastName) AS CustomerName,
           c.Email AS CustomerEmail,
           c.Phone AS CustomerPhone,
           c.Address AS CustomerAddress
    FROM orders o
    LEFT JOIN orderdetails od ON o.OrderID = od.OrderID
    LEFT JOIN Products p ON od.ProductID = p.ProductID
    LEFT JOIN customers c ON o.CustomerID = c.CustomerID
    WHERE o.Status != 'Before confirmation'
    GROUP BY o.OrderID, o.OrderDate, o.Status, c.FirstName, c.LastName, c.Email, c.Phone, c.Address
    ORDER BY o.OrderDate DESC
";

$stmtOrders = $conn->prepare($sqlOrders);
if (!$stmtOrders) {
    die("Prepare failed: " . $conn->error);
}

$stmtOrders->execute();
$resultOrders = $stmtOrders->get_result();

if ($resultOrders === false) {
    echo "Error retrieving orders: " . $conn->error;
    exit();
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderID = intval($_POST['order_id']);
    $newStatus = $_POST['new_status'];

    $updateQuery = "UPDATE orders SET Status = ? WHERE OrderID = ?";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bind_param('si', $newStatus, $orderID);
    
    if ($stmtUpdate->execute()) {
        echo "<script>alert('Order status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating order status: " . $stmtUpdate->error . "');</script>";
    }
    
    $stmtUpdate->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="manage-orders-page container-fluid px-0">
        <nav class="navbar navbar-expand-lg bg-body-tertiary px-4">
            <div class="container-fluid">
              <img src="img/logo.jpg" alt="" class="width-130px pe-4">
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link active color-000 px-3 font-size-15px" aria-current="page" href="dashboard.php">Dashboard</a>
                  </li>
                </ul>
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="me-3 font-size-15px">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <?php endif; ?>
                <a href="logout.php" class="nav-link color-000 px-1 font-size-15px d-flex align-items-center" title="Logout">
                    <i class="bi bi-box-arrow-right font-size-20px me-2"></i> log out
                </a>
              </div>
            </div>
        </nav>
        <div class="row">
            <div class=" px-5 mt-5">
                <label for="" class="font-size-20px font-weight-700">Manage Orders</label>
                <table class="table bg-color-e6e5e5 mb-0 font-size-14px border-none border-radius-top-10px mt-3">
                    <thead>
                        <tr>
                            <th class="width-7per">Order Date</th>
                            <th class="width-13per">Customer Name</th>
                            <th class="width-30per">Products</th>
                            <th class="width-10per">Total Price</th>
                            <th class="width-10per">Status</th>
                            <th class="width-24per">Actions</th>
                            <th class="width-6per">User Info</th>
                        </tr>
                    </thead>
                </table>
                <div class="scroll-y-axis max-h-450px">
                    <table class="table table-bordered font-size-13px">
                        <tbody>
                            <?php if ($resultOrders->num_rows > 0): ?>
                                <?php while ($order = $resultOrders->fetch_assoc()): ?>
                                    <tr>
                                        <td class="width-7per"><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['OrderDate']))); ?></td>
                                        <td class="width-13per"><?php echo htmlspecialchars($order['CustomerName']); ?></td>
                                        <td class="width-30per"><?php echo htmlspecialchars($order['Products']); ?></td>
                                        <td class="width-10per"><?php echo htmlspecialchars($order['TotalPrice']); ?> JD</td>
                                        <td class="width-10per"><?php echo htmlspecialchars($order['Status']); ?></td>
                                        <td class="width-24per">
                                            <form method="POST" class="d-inline">
                                                <div class="d-flex">
                                                    <input type="hidden" name="order_id" value="<?= $order['OrderID'] ?>" class="">
                                                    <select name="new_status" class="form-select font-size-13px" required>
                                                        <option value="Before confirmation" <?= $order['Status'] == 'Before confirmation' ? 'selected' : '' ?>>Before confirmation</option>
                                                        <option value="in progress" <?= $order['Status'] == 'in progress' ? 'selected' : '' ?>>in progress</option>
                                                        <option value="completed" <?= $order['Status'] == 'completed' ? 'selected' : '' ?>>completed</option>
                                                        <option value="cancelled" <?= $order['Status'] == 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                                                    </select>
                                                    <button type="submit" class="btn black-btn ms-2">Update</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="width-6per">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <button class="btn black-btn" data-bs-toggle="modal" data-bs-target="#userInfoModal<?= $order['OrderID'] ?>">User Info</button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal for User Info -->
                                    <div class="modal fade" id="userInfoModal<?= $order['OrderID'] ?>" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="userInfoModalLabel">User Information</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['CustomerName']); ?></p>
                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['CustomerEmail']); ?></p>
                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['CustomerPhone']); ?></p>
                                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['CustomerAddress']); ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>  
            </div>
        </div>
        
    </div>
    
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>

<?php
$stmtOrders->close();
$conn->close();
?>