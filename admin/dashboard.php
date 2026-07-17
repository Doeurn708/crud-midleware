<?php
require_once __DIR__ . '/../middleware/auth.php';

AuthMiddleware();
$active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Work, Explore &amp; Repeat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>

<div class="app">
    <?php include __DIR__ . '/include/aside.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Here's what's happening in your store today.</p>
            </div>
            <div class="topbar-date"><?= date('l, F j, Y') ?></div>
        </header>

        <section class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon icon-indigo"><i class="bi bi-box-seam-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Products</span>
                    <span class="stat-value">128</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-amber"><i class="bi bi-tags-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Categories</span>
                    <span class="stat-value">14</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Registered Users</span>
                    <span class="stat-value">342</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-rose"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-info">
                    <span class="stat-label">This Month's Growth</span>
                    <span class="stat-value">+8.2%</span>
                </div>
            </div>
        </section>

        <section class="panel-row">
            <div class="panel panel-wide">
                <div class="panel-header">
                    <h2>Recent Products</h2>
                    <a href="products.php" class="panel-link">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Wireless Mouse</td>
                                <td>Electronics</td>
                                <td>$24.00</td>
                                <td><span class="badge-status badge-active">Active</span></td>
                            </tr>
                            <tr>
                                <td>Canvas Backpack</td>
                                <td>Bags</td>
                                <td>$59.00</td>
                                <td><span class="badge-status badge-active">Active</span></td>
                            </tr>
                            <tr>
                                <td>Desk Lamp</td>
                                <td>Home &amp; Office</td>
                                <td>$32.50</td>
                                <td><span class="badge-status badge-draft">Draft</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="quick-actions">
                    <a href="products.php" class="quick-action">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                    <a href="category.php" class="quick-action">
                        <i class="bi bi-plus-circle"></i> Add Category
                    </a>
                    <a href="#" class="quick-action">
                        <i class="bi bi-file-earmark-arrow-down"></i> Export Report
                    </a>
                </div>
            </div>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>