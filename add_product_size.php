<?php
require 'db_connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add new size
if (isset($_POST['add_size'])) {
    $productID = intval($_POST['product_id']);
    $size = $_POST['size'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // Insert size into database
    $insertSizeQuery = "INSERT INTO productsizes (ProductID, Size, Price, Stock) VALUES (?, ?, ?, ?)";
    $stmtInsertSize = $conn->prepare($insertSizeQuery);
    $stmtInsertSize->bind_param('isdi', $productID, $size, $price, $stock);
    
    if ($stmtInsertSize->execute()) {
        echo "<script>alert('Size added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding size: " . $stmtInsertSize->error . "');</script>";
    }
}

// Query to fetch all products
$sqlProducts = "SELECT ProductID, ProductName FROM products";
$stmtProducts = $conn->prepare($sqlProducts);
$stmtProducts->execute();
$resultProducts = $stmtProducts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product Size</title>
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
            <div class="mt-5 px-5">
                <label for="" class="font-size-20px font-weight-700">Add New Size</label>
                <form method="POST" class="mt-3 font-size-13px">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Select Product</label>
                                <select name="product_id" class="form-select font-size-13px" required>
                                    <option value="" disabled selected>Select a product</option>
                                    <?php while ($product = $resultProducts->fetch_assoc()): ?>
                                        <option value="<?= $product['ProductID']; ?>"><?= htmlspecialchars($product['ProductName']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="size" class="form-label font-size-13px">Size</label>
                                <input type="text" name="size" class="form-control font-size-13px" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (JD)</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_size" class="btn pink-btn px-4 me-2">Add Size</button>
                    <a href="manage_products.php" class="btn black-btn px-4">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmtProducts->close();
$conn->close();
?>