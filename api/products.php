<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

header('Content-Type: application/json; charset=utf-8');

function productImagePath(?string $image): ?string
{
    if (!$image || !str_starts_with($image, '../public/uploads/')) {
        return null;
    }

    return __DIR__ . '/../public/uploads/' . basename($image);
}

function uploadProductImage(?string $currentImage): ?string
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return $currentImage;
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK || $_FILES['image']['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Image upload failed. Images must be 2 MB or smaller.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Please upload a JPG, PNG, GIF, or WebP image.');
    }

    $directory = __DIR__ . '/../public/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('Unable to create the image upload directory.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . $filename)) {
        throw new RuntimeException('Unable to save the uploaded image.');
    }

    $oldPath = productImagePath($currentImage);
    if ($oldPath && is_file($oldPath)) {
        unlink($oldPath);
    }

    return '../public/uploads/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $statement = $pdo->query(
        'SELECT p.id, p.category_id, p.name, p.description, p.price, p.stock, p.status, p.image, c.name AS category_name ' .
        'FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC'
    );
    echo json_encode(['success' => true, 'data' => $statement->fetchAll()]);
    exit;
}

requireAuthAjax();
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

if ($action === 'create' || $action === 'update') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? 'active';
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || $categoryId <= 0 || $price === false || $price < 0 || $stock === false || $stock < 0 || !in_array($status, ['active', 'draft'], true)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please provide valid product details.']);
        exit;
    }

    $category = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
    $category->execute([$categoryId]);
    if (!$category->fetch()) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Selected category does not exist.']);
        exit;
    }

    if ($action === 'create') {
        try {
            $image = uploadProductImage(null);
        } catch (RuntimeException $error) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $error->getMessage()]);
            exit;
        }
        $statement = $pdo->prepare('INSERT INTO products (category_id, name, description, price, stock, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([$categoryId, $name, $description ?: null, $price, $stock, $status, $image]);
        echo json_encode(['success' => true, 'message' => 'Product created successfully.']);
        exit;
    }

    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid product is required.']);
        exit;
    }
    $existing = $pdo->prepare('SELECT image FROM products WHERE id = ?');
    $existing->execute([$id]);
    $product = $existing->fetch();
    if (!$product) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }
    try {
        $image = uploadProductImage($product['image']);
    } catch (RuntimeException $error) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => $error->getMessage()]);
        exit;
    }
    $statement = $pdo->prepare('UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, status = ?, image = ? WHERE id = ?');
    $statement->execute([$categoryId, $name, $description ?: null, $price, $stock, $status, $image, $id]);
    echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
    exit;
}

if ($action === 'delete' && $id > 0) {
    $existing = $pdo->prepare('SELECT image FROM products WHERE id = ?');
    $existing->execute([$id]);
    $product = $existing->fetch();
    $statement = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $statement->execute([$id]);
    $imagePath = productImagePath($product['image'] ?? null);
    if ($imagePath && is_file($imagePath)) {
        unlink($imagePath);
    }
    echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unknown action or invalid product.']);
