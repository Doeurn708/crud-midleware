<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-mark">W</span>
        <span class="brand-text">Work<span class="brand-accent">.</span>Explore</span>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-label">Overview</span>
        <a href="dashboard.php" class="nav-link <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <span class="nav-label">Catalog</span>
        <a href="category.php" class="nav-link <?= ($active ?? '') === 'category' ? 'active' : '' ?>">
            <i class="bi bi-tags-fill"></i> Categories
        </a>
        <a href="products.php" class="nav-link <?= ($active ?? '') === 'products' ? 'active' : '' ?>">
            <i class="bi bi-box-seam-fill"></i> Products
        </a>

        <span class="nav-label">Account</span>
        <a href="#" id="logoutBtn" class="nav-link text-danger-link">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
            <div class="user-meta">
                <div class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
                <div class="user-role"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></div>
            </div>
        </div>
    </div>
</aside>

<script>
document.getElementById('logoutBtn')?.addEventListener('click', function(e){
    e.preventDefault();
    if(!confirm('Log out of your account?')) return;
    fetch('../api/auth-handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=logout'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            window.location.href = '../frontend/login.php';
        }
    });
});
</script>