<?php
require_once __DIR__ . '/../middleware/auth.php';
requireLoginPage();
require_once __DIR__ . '/../config/database.php'; // must expose a PDO $pdo connection

// ----- Fetch categories -----
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// ----- Fetch products with their category name -----
try {
    $sql = "
        SELECT p.id, p.name, p.price, p.stock, p.image, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.name ASC
    ";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETEC Middleware — Shop</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            background-color: #f8f9fb;
        }
        .navbar-brand {
            letter-spacing: 0.5px;
        }
        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #fff;
        }
        .category-pill {
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #495057;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .category-pill:hover {
            background-color: #e9ecef;
        }
        .category-pill.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        .product-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.08) !important;
        }
        .product-img-wrap {
            height:250px;
            background-color: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: fill ;
        }
        .product-img-wrap i {
            font-size: 2.5rem;
            color: #ced4da;
        }
        .price-tag {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-shop me-2"></i>ETEC SHOP
        </a>

        <div class="ms-auto d-flex align-items-center">

            <div class="dropdown">
                <button class="btn btn-primary d-flex align-items-center border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar-circle me-2">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                    </span>
                    <span class="text-white d-none d-sm-inline">
                        <?= htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <span class="dropdown-item-text small text-muted">
                            <?= htmlspecialchars($_SESSION['user_email']); ?>
                        </span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button id="logout" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</nav>

<div class="container mt-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1">Our Products</h2>
        <p class="text-muted">Browse our full catalog below.</p>
    </div>

    <!-- Search + Category Pills -->
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 mb-4">

        <div class="d-flex gap-2 flex-wrap justify-content-center" id="categoryPills">
            <button class="category-pill active" data-category="">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="category-pill" data-category="<?= htmlspecialchars($cat['name']); ?>">
                    <?= htmlspecialchars($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="input-group" style="max-width: 280px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search products...">
        </div>

    </div>

    <!-- Product Grid -->
    <div class="row g-4" id="productGrid">

        <?php if (empty($products)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No products found.
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-4 col-lg-3 product-item"
                     data-category="<?= htmlspecialchars($product['category_name'] ?? ''); ?>"
                     data-name="<?= htmlspecialchars(strtolower($product['name'])); ?>">

                    <div class="card product-card shadow-sm h-100">

                        <div class="product-img-wrap">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <i class="bi bi-box-seam"></i>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <?php if ($product['category_name']): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">
                                    <?= htmlspecialchars($product['category_name']); ?>
                                </span>
                            <?php endif; ?>

                            <h6 class="card-title mb-1"><?= htmlspecialchars($product['name']); ?></h6>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="price-tag">$<?= number_format((float) $product['price'], 2); ?></span>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success-subtle text-success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 pt-0">
                            <button class="btn btn-primary w-100" <?= $product['stock'] > 0 ? '' : 'disabled'; ?>>
                                <i class="bi bi-cart-plus me-1"></i> Add to Cart
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <div class="text-center text-muted mt-4 d-none" id="noResults">
        <i class="bi bi-search fs-1 d-block mb-2"></i>
        No products match your search.
    </div>

</div>

<script>
$(document).ready(function(){

    function filterProducts(){
        const category = $(".category-pill.active").data("category");
        const search = $("#searchInput").val().toLowerCase().trim();
        let visibleCount = 0;

        $(".product-item").each(function(){
            const itemCategory = $(this).data("category");
            const itemName = $(this).data("name");

            const matchesCategory = (category === "" || itemCategory === category);
            const matchesSearch = (search === "" || itemName.includes(search));

            if (matchesCategory && matchesSearch) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $("#noResults").toggleClass("d-none", visibleCount > 0);
    }

    $(".category-pill").on("click", function(){
        $(".category-pill").removeClass("active");
        $(this).addClass("active");
        filterProducts();
    });

    $("#searchInput").on("keyup", function(){
        filterProducts();
    });

    $("#logout").click(function(){

        $.ajax({
            url: "../api/auth-handler.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "logout"
            },

            success: function(res){

                if(res.success){
                    window.location.href = '../frontend/login.php';
                }

            },

        });

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>