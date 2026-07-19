<?php
require_once __DIR__ . '/../middleware/auth.php';

AuthMiddleware();
$active = 'products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Work, Explore &amp; Repeat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/style2.css">
</head>
<body>

<div class="app">
    <?php include __DIR__ . '/include/aside.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1 class="page-title">Products</h1>
                <p class="page-subtitle">Manage your product catalog.</p>
            </div>
            <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg"></i> Add Product
            </button>
        </header>

        <section class="panel">
            <div class="panel-header">
                <h2>All Products</h2>
                <div class="d-flex gap-2">
                    <select id="categoryFilter" class="form-select form-select-sm" style="width:auto;">
                        <option value="">All Categories</option>
                    </select>
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" placeholder="Search products...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <tr class="skeleton-row">
                            <td style="width:56px;"><div class="skeleton-bar" style="width:36px; height:36px; border-radius:8px;"></div></td>
                            <td><div class="skeleton-bar" style="width:70%;"></div></td>
                            <td><div class="skeleton-bar" style="width:50%;"></div></td>
                            <td><div class="skeleton-bar" style="width:40px;"></div></td>
                            <td><div class="skeleton-bar" style="width:30px;"></div></td>
                            <td><div class="skeleton-bar" style="width:60px;"></div></td>
                            <td><div class="skeleton-bar" style="width:50px; margin-left:auto;"></div></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="productId">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" id="productName" class="form-control" placeholder="e.g. Wireless Mouse" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="productCategory" class="form-select" required>
                                <option value="">Select category</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price" id="productPrice" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" min="0" name="stock" id="productStock" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" id="productStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="productDescription" class="form-control" rows="3" placeholder="Short product description..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" id="productImage" class="form-control" accept="image/*">
                            <img id="imagePreview" class="mt-2 rounded" style="max-height:90px; display:none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-danger"><i class="bi bi-exclamation-triangle-fill"></i> Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteProductName"></strong>? This can't be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-stack" id="toastStack"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../ajax/products/product.js"></script>
</body>
</html>
