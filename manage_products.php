<?php
session_start();
require 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
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
        <div class="row mt-5 pt-5">
            <div class="col-md-6 px-5">
                <a href="delete_product.php" class="btn w-100 black-btn">Delete Product</a>
            </div>
            <div class="col-md-6 px-5">
                <a href="delete_product_size.php" class="btn w-100 black-btn">Delete Product Size</a>
            </div>
        </div>
        <div class="row mt-5 pt-5">
            <div class="col-md-6 px-5">
                <a href="edit_product.php" class="btn w-100 black-btn">Update Product Information</a>
            </div>
            <div class="col-md-6 px-5">
                <a href="add_product_size.php" class="btn w-100 black-btn">Add New Size For The Product</a>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
