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
<script>
const productModal = new bootstrap.Modal(document.getElementById('productModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
let productToDelete = null;
let allProducts = [];

// NOTE: adjust these endpoints to match your api/products.php and api/category.php
const PRODUCT_API = '../api/products.php';
const CATEGORY_API = '../api/category.php';

function showAlert(message, type = 'success') {
    const stack = document.getElementById('toastStack');
    const toast = document.createElement('div');
    toast.className = `toast-item ${type === 'danger' ? 'toast-danger' : ''}`;
    toast.innerHTML = `
        <i class="bi ${type === 'danger' ? 'bi-x-circle-fill' : 'bi-check-circle-fill'}"></i>
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'toast-out 0.2s ease forwards';
        setTimeout(() => toast.remove(), 200);
    }, 3200);
}

function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}

function loadCategoryOptions() {
    fetch(`${CATEGORY_API}?action=list`)
        .then(res => res.json())
        .then(res => {
            const categories = res.data || [];
            const options = categories.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
            document.getElementById('productCategory').innerHTML = `<option value="">Select category</option>${options}`;
            document.getElementById('categoryFilter').innerHTML = `<option value="">All Categories</option>${options}`;
        });
}

function openCreateModal() {
    document.getElementById('productModalLabel').textContent = 'Add Product';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function openEditModal(p) {
    document.getElementById('productModalLabel').textContent = 'Edit Product';
    document.getElementById('productId').value = p.id;
    document.getElementById('productName').value = p.name;
    document.getElementById('productCategory').value = p.category_id;
    document.getElementById('productPrice').value = p.price;
    document.getElementById('productStock').value = p.stock;
    document.getElementById('productStatus').value = p.status || 'active';
    document.getElementById('productDescription').value = p.description || '';
    if (p.image) {
        document.getElementById('imagePreview').src = p.image;
        document.getElementById('imagePreview').style.display = 'block';
    }
    productModal.show();
}

function statusBadge(status) {
    return status === 'draft'
        ? '<span class="badge-status badge-draft">Draft</span>'
        : '<span class="badge-status badge-active">Active</span>';
}

function renderRows(products) {
    const body = document.getElementById('productTableBody');
    if (!products.length) {
        body.innerHTML = `<tr><td colspan="7">
            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                <div class="empty-title">No products yet</div>
                <div class="empty-sub">Add your first product to start building your catalog.</div>
                <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openCreateModal()">
                    <i class="bi bi-plus-lg"></i> Add Product
                </button>
            </div>
        </td></tr>`;
        return;
    }
    body.innerHTML = products.map((p, i) => `
        <tr data-name="${escapeHtml((p.name || '').toLowerCase())}" data-category="${p.category_id ?? ''}">
            <td><img src="${escapeHtml(p.image || 'https://via.placeholder.com/40')}" class="product-thumb" alt=""></td>
            <td class="fw-semibold">${escapeHtml(p.name)}</td>
            <td>${escapeHtml(p.category_name || '-')}</td>
            <td>$${parseFloat(p.price).toFixed(2)}</td>
            <td>${p.stock}</td>
            <td>${statusBadge(p.status)}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-icon" onclick="openEditModal(allProducts[${i}])">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-icon-danger" onclick="confirmDelete(allProducts[${i}].id, allProducts[${i}].name)">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function loadProducts() {
    fetch(`${PRODUCT_API}?action=list`)
        .then(res => res.json())
        .then(res => {
            allProducts = res.data || [];
            renderRows(allProducts);
        })
        .catch(() => {
            document.getElementById('productTableBody').innerHTML =
                `<tr><td colspan="7" class="text-center text-danger py-4">Could not load products.</td></tr>`;
        });
}

document.getElementById('productImage').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('imagePreview');
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(file);
});

document.getElementById('productForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('productId').value;
    const formData = new FormData(this);
    formData.append('action', id ? 'update' : 'create');

    fetch(PRODUCT_API, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                productModal.hide();
                showAlert(res.message || 'Saved successfully.');
                loadProducts();
            } else {
                showAlert(res.message || 'Something went wrong.', 'danger');
            }
        })
        .catch(() => showAlert('Could not save the product.', 'danger'));
});

function confirmDelete(id, name) {
    productToDelete = id;
    document.getElementById('deleteProductName').textContent = name;
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    fetch(PRODUCT_API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&id=${productToDelete}`
    })
    .then(res => res.json())
    .then(res => {
        deleteModal.hide();
        showAlert(res.message || 'Deleted.', res.success ? 'success' : 'danger');
        if (res.success) loadProducts();
    })
    .catch(() => showAlert('Could not delete the product.', 'danger'));
});

document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('categoryFilter').addEventListener('change', filterTable);

function filterTable() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    const cat = document.getElementById('categoryFilter').value;
    document.querySelectorAll('#productTableBody tr[data-name]').forEach(row => {
        const matchesName = row.dataset.name.includes(term);
        const matchesCat = !cat || row.dataset.category === cat;
        row.style.display = (matchesName && matchesCat) ? '' : 'none';
    });
}

loadCategoryOptions();
loadProducts();
</script>
</body>
</html>
