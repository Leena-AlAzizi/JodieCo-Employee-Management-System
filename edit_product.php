<?php
require 'db_connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Edit product information
if (isset($_POST['edit_product_id'])) {
    $productID = intval($_POST['edit_product_id']);
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $imageURL = $_POST['image_url'];

    // Update product information
    $updateQuery = "UPDATE products SET ProductName = ?, Description = ?, ImageURL = ? WHERE ProductID = ?";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bind_param('sssi', $productName, $description, $imageURL, $productID);
    
    if ($stmtUpdate->execute()) {
        echo "<script>alert('Product updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating product: " . $stmtUpdate->error . "');</script>";
    }
}

$sqlProducts = "SELECT ProductID, ProductName, Description, ImageURL FROM products";
$stmtProducts = $conn->prepare($sqlProducts);
$stmtProducts->execute();
$resultProducts = $stmtProducts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eidt Product</title>
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
            <div class="px-5 mt-5">
                <table class="table bg-color-e6e5e5 mb-0 font-size-14px border-none border-radius-top-10px">
                    <thead>
                        <tr>
                            <th class="width-20per">Product Name</th>
                            <th class="width-50per">Description</th>
                            <th class="width-20per">Image URL</th>
                            <th class="width-10per">
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
                                        <td class="width-20per"><?php echo htmlspecialchars($product['ProductName']); ?></td>
                                        <td class="width-50per"><?php echo htmlspecialchars($product['Description']); ?></td>
                                        <td class="width-20per"><?php echo htmlspecialchars($product['ImageURL']); ?></td>
                                        <td class="width-10per">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn pink-btn px-4" data-bs-toggle="modal" data-bs-target="#editModal<?= $product['ProductID'] ?>">Edit</button>
                                            </div>

                                            <!-- Product Edit Window -->
                                            <div class="modal fade" id="editModal<?= $product['ProductID'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-none">
                                                            <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body font-size-13px">
                                                            <form method="POST">
                                                                <input type="hidden" name="edit_product_id" value="<?= htmlspecialchars($product['ProductID']); ?>">
                                                                <div class="mb-3">
                                                                    <label for="product_name" class="form-label">Product Name</label>
                                                                    <input type="text" name="product_name" class="form-control font-size-13px" value="<?= htmlspecialchars($product['ProductName']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="description" class="form-label">Description</label>
                                                                    <textarea name="description" class="form-control font-size-13px" required><?= htmlspecialchars($product['Description']); ?></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="image_url" class="form-label">Image URL</label>
                                                                    <input type="text" name="image_url" class="form-control font-size-13px" value="<?= htmlspecialchars($product['ImageURL']); ?>" required>
                                                                </div>
                                                                <button type="submit" class="btn black-btn px-3">Save Changes</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No products found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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