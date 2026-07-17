<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $statement = $pdo->query(
        'SELECT c.id, c.name, c.description, ' .
        'LOWER(REPLACE(TRIM(c.name), " ", "-")) AS slug, ' .
        'COUNT(p.id) AS product_count ' .
        'FROM categories c LEFT JOIN products p ON p.category_id = c.id ' .
        'GROUP BY c.id, c.name, c.description ORDER BY c.id DESC'
    );
    echo json_encode(['success' => true, 'data' => $statement->fetchAll()]);
    exit;
}

requireAuthAjax();
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($action === 'create' || $action === 'update') {
    if ($name === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Category name is required.']);
        exit;
    }

    if ($action === 'create') {
        $statement = $pdo->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
        $statement->execute([$name, $description ?: null]);
        echo json_encode(['success' => true, 'message' => 'Category created successfully.']);
        exit;
    }

    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid category is required.']);
        exit;
    }
    $statement = $pdo->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
    $statement->execute([$name, $description ?: null, $id]);
    echo json_encode(['success' => true, 'message' => 'Category updated successfully.']);
    exit;
}

if ($action === 'delete') {
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid category is required.']);
        exit;
    }
    $statement = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $statement->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Category deleted successfully.']);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unknown action.']);
