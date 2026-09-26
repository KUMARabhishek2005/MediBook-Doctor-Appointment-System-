<?php
// Database connection, session and helper functions used by every page.
declare(strict_types=1);
session_start();

$host = 'localhost'; $db = 'doctor_appointment'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    exit('Database connection failed. Check includes/config.php and that MySQL is running.');
}

function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

function require_login(): void {
    if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
}
function require_role(string $role): void {
    require_login();
    if ($_SESSION['user']['role'] !== $role) { http_response_code(403); exit('Access denied.'); }
}

// Flash message: set on one page, shown once on the next page.
function flash(string $msg, string $type = 'success'): void { $_SESSION['flash'] = [$msg, $type]; }
function redirect(string $to): void { header("Location: $to"); exit; }

// Coloured status pill, e.g. status_badge('Approved') -> green pill.
function status_badge(string $s): string {
    return '<span class="badge b-' . strtolower(e($s)) . '">' . e($s) . '</span>';
}
// Two-letter initials for the round avatar.
function initials(string $name): string {
    $name = preg_replace('/^Dr\.?\s*/i', '', $name);
    $p = preg_split('/\s+/', trim($name));
    return strtoupper(substr($p[0], 0, 1) . (isset($p[1]) ? substr($p[1], 0, 1) : ''));
}
