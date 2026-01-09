<?php
session_start();
require 'db_connection.php';

// Check the category selection
$categoryID = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get all categories
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

// Fetch products based on the specified category or display all products if no category is specified
if ($categoryID > 0) {
    $products_query = "
        SELECT p.ProductID, p.ProductName, p.Description, p.ImageURL, ps.Size, ps.Price, ps.Stock 
        FROM products p
        LEFT JOIN productsizes ps ON p.ProductID = ps.ProductID
        WHERE p.CategoryID = $categoryID
    ";
} else {
    $products_query = "
        SELECT p.ProductID, p.ProductName, p.Description, p.ImageURL, ps.Size, ps.Price, ps.Stock 
        FROM products p
        LEFT JOIN productsizes ps ON p.ProductID = ps.ProductID
    ";
}
$products_result = mysqli_query($conn, $products_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <style>

    </style>
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
        <div class="product-table px-5 mt-5">
            <form method="GET" class="mb-4">
                <label for="category" class="font-size-13px mb-3">Choose a Category:</label>
                <select name="category" id="category" class="form-select font-size-12px w-50" onchange="this.form.submit()">
                    <option value="" class="color-b5b5b5">Select a category</option>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?= $category['CategoryID'] ?>" <?= $categoryID == $category['CategoryID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['CategoryName']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
            <?php if ($products_result && mysqli_num_rows($products_result) > 0): ?>
                <table class="table bg-color-e6e5e5 mb-0 font-size-14px border-none border-radius-top-10px">
                    <thead>
                        <tr >
                            <th class="width-15per border-none">Product Name</th>
                            <th class="width-45per border-none">Description</th>
                            <th class="width-10per border-none">Size</th>
                            <th class="width-10per border-none">Price</th>
                            <th class="width-10per border-none">Stock</th>
                            <th class="width-10per border-none">Image</th>
                        </tr>
                    </thead>
                </table>
                <div class="scroll-y-axis max-h-450px">
                <table class="table table-bordered font-size-13px ">
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                            <tr>
                                <td class="width-15per"><?= htmlspecialchars($product['ProductName']) ?></td>
                                <td class="width-45per"><?= htmlspecialchars($product['Description']) ?></td>
                                <td class="width-10per"><?= htmlspecialchars($product['Size']) ?></td>
                                <td class="width-10per"><?= htmlspecialchars($product['Price']) ?></td>
                                <td class="width-10per"><?= htmlspecialchars($product['Stock']) ?></td>
                                <td class="width-10per ">
                                    <div class="d-flex justify-content-center">
                                        <?php if (!empty($product['ImageURL'])): ?>
                                            <img src="<?= htmlspecialchars($product['ImageURL']) ?>" class="border-radius-5px" alt="Product Image" style="width: 100px; height: auto;">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No products found for the selected category.</p>
            <?php endif; ?>
        </div>        
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
