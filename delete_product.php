<?php
require 'db_connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Delete a product with all its sizes
if (isset($_POST['delete_product_id'])) {
    $productID = intval($_POST['delete_product_id']);
    
    // Remove favorites associated with the product
    $deleteFavoritesQuery = "DELETE FROM favorites WHERE ProductID = ?";
    $stmtDeleteFavorites = $conn->prepare($deleteFavoritesQuery);
    $stmtDeleteFavorites->bind_param('i', $productID);
    $stmtDeleteFavorites->execute();
    
    // Remove the size associated with the product
    $deleteSizesQuery = "DELETE FROM productsizes WHERE ProductID = ?";
    $stmtDeleteSizes = $conn->prepare($deleteSizesQuery);
    $stmtDeleteSizes->bind_param('i', $productID);
    $stmtDeleteSizes->execute();
    
    // Delete the product itself
    $deleteProductQuery = "DELETE FROM products WHERE ProductID = ?";
    $stmtDeleteProduct = $conn->prepare($deleteProductQuery);
    $stmtDeleteProduct->bind_param('i', $productID);
    $stmtDeleteProduct->execute();
    
    echo "<script>alert('Product and its sizes deleted successfully!');</script>";
}

$sqlProducts = "SELECT ProductID, ProductName FROM products";
$stmtProducts = $conn->prepare($sqlProducts);
$stmtProducts->execute();
$resultProducts = $stmtProducts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
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
                <label for="" class="font-size-20px font-weight-700">Delete Product</label>
                <table class="table bg-color-e6e5e5 mb-0 font-size-14px border-none border-radius-top-10px mt-3">
                    <thead>
                        <tr>
                            <th class="width-80per">
                                <div class="px-3">Product Name</div>
                            </th>
                            <th class="width-20per">                                            
                                <div class="d-flex justify-content-center">Actions</div>
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="scroll-y-axis max-h-450px">
                    <table class="table table-bordered font-size-13px">
                        <tbody>
                            <?php if ($resultProducts->num_rows > 0): ?>
                                <?php while ($product = $resultProducts->fetch_assoc()): ?>
                                    <tr>
                                        <td class="width-80per">
                                            <div class="px-3"><?php echo htmlspecialchars($product['ProductName'] ?? 'Unknown'); ?></div>
                                        </td>
                                        <td class="width-20per">
                                            <div class="d-flex justify-content-center">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_product_id" value="<?= $product['ProductID'] ?>">
                                                    <button type="submit" class="btn pink-btn px-5">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">No products found.</td>
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
$stmtProducts->close();
$conn->close();
?>