<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLoginPage(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../frontend/login.php');
        exit;
    }
}

function AuthMiddleware(): void
{
    requireLoginPage();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: ../client/index.php');
        exit;
    }
}

function requireAuthAjax(): void
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Please log in to continue.']);
        exit;
    }
}

function requireAdminAjax(): void
{
    requireAuthAjax();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Administrator access is required.']);
        exit;
    }
}
