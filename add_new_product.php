<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'];
    $categoryID = $_POST['category_id'];
    $description = $_POST['description'];

    // Image processing
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageDestinationPath = 'uploads/' . basename($imageName);

        // Check if the folder exists
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true); // Create the folder if it does not exist
        }

        // Upload image to folder
        if (!move_uploaded_file($imageTmpPath, $imageDestinationPath)) {
            echo "<script>alert('Error moving uploaded file.');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error uploading image.');</script>";
        exit;
    }

    // Insert the product into the products table
    $productQuery = "INSERT INTO products (ProductName, CategoryID, Description, ImageURL) VALUES ('$productName', $categoryID, '$description', '$imageDestinationPath')";
    if (mysqli_query($conn, $productQuery)) {
        $productID = mysqli_insert_id($conn);

        // Enter sizes
        foreach ($_POST['sizes'] as $index => $size) {
            $price = $_POST['prices'][$index];
            $stock = $_POST['stocks'][$index];

            $sizeQuery = "INSERT INTO productsizes (ProductID, Size, Price, Stock) VALUES ($productID, '$size', $price, $stock)";
            mysqli_query($conn, $sizeQuery);
        }

        echo "<script>alert('Product added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding product.');</script>";
    }
}

$categoriesQuery = "SELECT * FROM categories";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <script>
        // Function to add new fields to size
        function addSizeField() {
            const container = document.getElementById('sizes-container');
            const sizeGroup = `
                <div class="row mb-3 size-group">
                    <div class="col-md-3">
                        <input type="text" name="sizes[]" class="form-control font-size-13px" placeholder="Size" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="prices[]" class="form-control font-size-13px" placeholder="Price" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="stocks[]" class="form-control font-size-13px" placeholder="Stock" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn black-btn px-3" onclick="removeSizeField(this)">Remove</button>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', sizeGroup);
        }

        // Function to remove size fields
        function removeSizeField(button) {
            button.parentElement.parentElement.remove();
        }

        // Drag and drop processing
        function handleFileDrop(event) {
            event.preventDefault();
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        }

        function handleFile(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('image-preview').style.display = 'block';
                    // Update the input field with the image file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('image-input').files = dataTransfer.files;
                };
                reader.readAsDataURL(file);
            } else {
                alert('Please select a valid image file.');
            }
        }

        // Allow clicking on the drag and drop area to open the file browser
        function selectFile() {
            document.getElementById('image-input').click();
        }

        // Process file selection
        function handleFileInput(event) {
            const file = event.target.files[0];
            handleFile(file);
        }
    </script>
    <style>
        
    </style>
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
        <div class="px-5 mt-4">
            <label for="" class="font-size-20px font-weight-700">Add New Product</label>
            <form method="POST" enctype="multipart/form-data" class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_name" class="form-label font-size-13px">Product Name</label>
                            <input type="text" id="product_name" name="product_name" class="form-control font-size-13px" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label font-size-13px">Category</label>
                            <select id="category_id" name="category_id" class="form-select font-size-13px" required>
                                <option value="">Select a category</option>
                                <?php while ($category = mysqli_fetch_assoc($categoriesResult)): ?>
                                    <option value="<?= $category['CategoryID'] ?>"><?= htmlspecialchars($category['CategoryName']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3">
                        <label for="description" class="form-label font-size-13px">Description</label>
                        <textarea id="description" name="description" class="form-control font-size-13px" rows="2" required></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 font-size-13px" id="drop-area" onclick="selectFile()" ondrop="handleFileDrop(event)" ondragover="event.preventDefault()">
                        <p>Drag & Drop an image here, or click to select one</p>
                        <input type="file" id="image-input" name="image" accept="image/*" class="d-none" required onchange="handleFileInput(event)">
                        <img id="image-preview" src="#" alt="Image Preview" style="display: none;">
                    </div>
                </div>
                <div id="sizes-container">
                    <label for="" class="font-size-17px font-weight-600">Sizes</label>
                    <div class="row mb-3 size-group mt-1">
                        <div class="col-md-3">
                            <input type="text" name="sizes[]" class="form-control font-size-13px" placeholder="Size" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="prices[]" class="form-control font-size-13px" placeholder="Price" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="stocks[]" class="form-control font-size-13px" placeholder="Stock" required>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn black-btn px-3" onclick="addSizeField()">Add More Sizes</button>
                <button type="submit" class="btn black-btn px-3">Add Product</button>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>