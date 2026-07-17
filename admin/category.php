<?php
require_once __DIR__ . '/../middleware/auth.php';

AuthMiddleware();
$active = 'category';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | Work, Explore &amp; Repeat</title>
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
                <h1 class="page-title">Categories</h1>
                <p class="page-subtitle">Organize your products into groups.</p>
            </div>
            <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg"></i> Add Category
            </button>
        </header>

        <section class="panel">
            <div class="panel-header">
                <h2>All Categories</h2>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Search categories...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <!-- Rows are rendered by JS from loadCategories() -->
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <tr class="skeleton-row">
                            <td style="width:30px;"><div class="skeleton-bar" style="width:16px;"></div></td>
                            <td><div class="skeleton-bar" style="width:60%;"></div></td>
                            <td><div class="skeleton-bar" style="width:40%;"></div></td>
                            <td><div class="skeleton-bar" style="width:20px;"></div></td>
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
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoryId">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="categoryName" class="form-control" placeholder="e.g. Electronics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-muted">(optional)</span></label>
                        <textarea name="description" id="categoryDescription" class="form-control" rows="3" placeholder="Short description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand">Save Category</button>
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
                <h5 class="modal-title modal-title-danger"><i class="bi bi-exclamation-triangle-fill"></i> Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCategoryName"></strong>? This can't be undone.</p>
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
const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
let categoryToDelete = null;
let allCategories = [];

// NOTE: adjust the endpoint/action names below to match your api/category.php
const API_URL = '../api/category.php';

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

function openCreateModal() {
    document.getElementById('categoryModalLabel').textContent = 'Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
}

function openEditModal(cat) {
    document.getElementById('categoryModalLabel').textContent = 'Edit Category';
    document.getElementById('categoryId').value = cat.id;
    document.getElementById('categoryName').value = cat.name;
    document.getElementById('categoryDescription').value = cat.description || '';
    categoryModal.show();
}

function renderRows(categories) {
    const body = document.getElementById('categoryTableBody');
    if (!categories.length) {
        body.innerHTML = `<tr><td colspan="5">
            <div class="empty-state">
                <i class="bi bi-tags"></i>
                <div class="empty-title">No categories yet</div>
                <div class="empty-sub">Create a category to start organizing your products.</div>
                <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openCreateModal()">
                    <i class="bi bi-plus-lg"></i> Add Category
                </button>
            </div>
        </td></tr>`;
        return;
    }
    body.innerHTML = categories.map((cat, i) => `
        <tr data-name="${escapeHtml((cat.name || '').toLowerCase())}">
            <td>${i + 1}</td>
            <td class="fw-semibold">${escapeHtml(cat.name)}</td>
            <td><code>${escapeHtml(cat.slug || '-')}</code></td>
            <td>${cat.product_count ?? 0}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-icon" onclick="openEditModal(allCategories[${i}])">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-icon-danger" onclick="confirmDelete(allCategories[${i}].id, allCategories[${i}].name)">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function loadCategories() {
    fetch(`${API_URL}?action=list`)
        .then(res => res.json())
        .then(res => {
            allCategories = res.data || [];
            renderRows(allCategories);
        })
        .catch(() => {
            document.getElementById('categoryTableBody').innerHTML =
                `<tr><td colspan="5" class="text-center text-danger py-4">Could not load categories.</td></tr>`;
        });
}

document.getElementById('categoryForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const formData = new FormData(this);
    formData.append('action', id ? 'update' : 'create');

    fetch(API_URL, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                categoryModal.hide();
                showAlert(res.message || 'Saved successfully.');
                loadCategories();
            } else {
                showAlert(res.message || 'Something went wrong.', 'danger');
            }
        })
        .catch(() => showAlert('Could not save the category.', 'danger'));
});

function confirmDelete(id, name) {
    categoryToDelete = id;
    document.getElementById('deleteCategoryName').textContent = name;
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&id=${categoryToDelete}`
    })
    .then(res => res.json())
    .then(res => {
        deleteModal.hide();
        showAlert(res.message || 'Deleted.', res.success ? 'success' : 'danger');
        if (res.success) loadCategories();
    })
    .catch(() => showAlert('Could not delete the category.', 'danger'));
});

document.getElementById('searchInput').addEventListener('input', function () {
    const term = this.value.toLowerCase();
    document.querySelectorAll('#categoryTableBody tr[data-name]').forEach(row => {
        row.style.display = row.dataset.name.includes(term) ? '' : 'none';
    });
});

loadCategories();
</script>
</body>
</html>
