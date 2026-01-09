<?php
require 'db_connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Delete a specific size
if (isset($_POST['delete_size_id'])) {
    $sizeID = intval($_POST['delete_size_id']);
    
    // Remove size from database
    $deleteSizeQuery = "DELETE FROM productsizes WHERE ProductSizeID = ?";
    $stmtDeleteSize = $conn->prepare($deleteSizeQuery);
    $stmtDeleteSize->bind_param('i', $sizeID);
    
    if ($stmtDeleteSize->execute()) {
        echo "<script>alert('Size deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting size: " . $stmtDeleteSize->error . "');</script>";
    }
}

// Query to fetch all sizes with product information
$sqlSizes = "
    SELECT ps.ProductSizeID, ps.Size, ps.Price, ps.Stock, p.ProductName
    FROM productsizes ps
    JOIN products p ON ps.ProductID = p.ProductID
";

$stmtSizes = $conn->prepare($sqlSizes);
$stmtSizes->execute();
$resultSizes = $stmtSizes->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product Sizes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container-fluid px-0">
        <nav class="navbar navbar-expand-lg bg-body-tertiary px-4">
            <div class="container-fluid">
              <img src="img\logo.jpg" alt="" class="width-130px pe-4">
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
            <div class="mt-5 px-5">
                <label for="" class="font-size-20px font-weight-700">Delete Product Sizes</label>
                <table class="table bg-color-e6e5e5 mb-0 font-size-14px border-none border-radius-top-10px mt-3">
                    <thead>
                        <tr>
                            <th class="width-30per">Product Name</th>
                            <th class="width-20per">Size</th>
                            <th class="width-20per">Price</th>
                            <th class="width-10per">Stock</th>
                            <th class="width-20per">
                                <div class="d-flex justify-content-center">Actions</div>
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="scroll-y-axis max-h-450px">
                    <table class="table table-bordered font-size-13px">
                        <tbody>
                            <?php if ($resultSizes->num_rows > 0): ?>
                                <?php while ($size = $resultSizes->fetch_assoc()): ?>
                                    <tr>
                                        <td class="width-30per"><?php echo htmlspecialchars($size['ProductName']); ?></td>
                                        <td class="width-20per"><?php echo htmlspecialchars($size['Size']); ?></td>
                                        <td class="width-20per"><?php echo htmlspecialchars($size['Price']); ?> JD</td>
                                        <td class="width-10per"><?php echo htmlspecialchars($size['Stock']); ?></td>
                                        <td class="width-20per">
                                            <div class="d-flex justify-content-center">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_size_id" value="<?= $size['ProductSizeID'] ?>">
                                                    <button type="submit" class="btn pink-btn px-5">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No sizes found.</td>
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
$stmtSizes->close();
$conn->close();
?>