const productModal = new bootstrap.Modal(document.getElementById('productModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
let productToDelete = null;
let allProducts = [];

// One endpoint per file, matching api/products/.
const PRODUCT_API = {
    get: '../api/products/get.php',
    insert: '../api/products/insert.php',
    edit: '../api/products/edit.php',
    delete: '../api/products/delete.php'
};
const CATEGORY_GET_API = '../api/category/get.php';

function showAlert(message, type = 'success') {
    const stack = document.getElementById('toastStack');
    const toast = document.createElement('div');
    toast.className = `toast-item ${type === 'danger' ? 'toast-danger' : ''}`;
    toast.innerHTML = `<i class="bi ${type === 'danger' ? 'bi-x-circle-fill' : 'bi-check-circle-fill'}"></i><span>${message}</span><button class="toast-close" onclick="this.parentElement.remove()">&times;</button>`;
    stack.appendChild(toast);
    setTimeout(() => toast.remove(), 3200);
}

function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}

function loadCategoryOptions() {
    $.ajax({
        url: CATEGORY_GET_API,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            const options = (response.data || []).map(category => `<option value="${category.id}">${escapeHtml(category.name)}</option>`).join('');
            document.getElementById('productCategory').innerHTML = `<option value="">Select category</option>${options}`;
            document.getElementById('categoryFilter').innerHTML = `<option value="">All Categories</option>${options}`;
        },
        error: function () {
            showAlert('Could not load categories.', 'danger');
        }
    });
}

function openCreateModal() {
    document.getElementById('productModalLabel').textContent = 'Add Product';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function openEditModal(product) {
    document.getElementById('productModalLabel').textContent = 'Edit Product';
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productCategory').value = product.category_id;
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productStock').value = product.stock;
    document.getElementById('productStatus').value = product.status || 'active';
    document.getElementById('productDescription').value = product.description || '';
    const preview = document.getElementById('imagePreview');
    preview.style.display = product.image ? 'block' : 'none';
    preview.src = product.image || '';
    productModal.show();
}

function statusBadge(status) {
    return status === 'draft' ? '<span class="badge-status badge-draft">Draft</span>' : '<span class="badge-status badge-active">Active</span>';
}

function renderRows(products) {
    const body = document.getElementById('productTableBody');
    if (!products.length) {
        body.innerHTML = '<tr><td colspan="7" class="text-center py-4">No products yet.</td></tr>';
        return;
    }
    body.innerHTML = products.map((product, index) => `
        <tr data-name="${escapeHtml((product.name || '').toLowerCase())}" data-category="${product.category_id ?? ''}">
            <td><img src="${escapeHtml(product.image || 'https://via.placeholder.com/40')}" class="product-thumb" alt=""></td>
            <td class="fw-semibold">${escapeHtml(product.name)}</td>
            <td>${escapeHtml(product.category_name || '-')}</td>
            <td>$${parseFloat(product.price).toFixed(2)}</td><td>${product.stock}</td><td>${statusBadge(product.status)}</td>
            <td class="text-end"><button class="btn btn-sm btn-icon" onclick="openEditModal(allProducts[${index}])"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-icon btn-icon-danger" onclick="confirmDelete(allProducts[${index}].id, allProducts[${index}].name)"><i class="bi bi-trash3"></i></button></td>
        </tr>`).join('');
}

function loadProducts() {
    $.ajax({
        url: PRODUCT_API.get,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            allProducts = response.data || [];
            renderRows(allProducts);
        },
        error: function () {
            document.getElementById('productTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Could not load products.</td></tr>';
        }
    });
}

document.getElementById('productImage').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = event => {
        const preview = document.getElementById('imagePreview');
        preview.src = event.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
});

document.getElementById('productForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const isEdit = Boolean(document.getElementById('productId').value);
    $.ajax({
        url: isEdit ? PRODUCT_API.edit : PRODUCT_API.insert,
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (!response.success) return showAlert(response.message || 'Could not save product.', 'danger');
            productModal.hide();
            showAlert(response.message || 'Product saved.');
            loadProducts();
        },
        error: function () {
            showAlert('Could not save the product.', 'danger');
        }
    });
});

function confirmDelete(id, name) {
    productToDelete = id;
    document.getElementById('deleteProductName').textContent = name;
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    $.ajax({
        url: PRODUCT_API.delete,
        method: 'POST',
        data: { id: productToDelete },
        dataType: 'json',
        success: function (response) {
            deleteModal.hide();
            showAlert(response.message || 'Deleted.', response.success ? 'success' : 'danger');
            if (response.success) loadProducts();
        },
        error: function () {
            showAlert('Could not delete the product.', 'danger');
        }
    });
});

function filterTable() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    const categoryId = document.getElementById('categoryFilter').value;
    document.querySelectorAll('#productTableBody tr[data-name]').forEach(row => {
        row.style.display = (row.dataset.name.includes(term) && (!categoryId || row.dataset.category === categoryId)) ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('categoryFilter').addEventListener('change', filterTable);
loadCategoryOptions();
loadProducts();
