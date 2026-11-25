<?php
// Fonctions utilitaires du Back Office (session, messages flash, CSRF, URLs).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Échappe une valeur pour l'affichage HTML (protection XSS). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Construit une URL du Back Office : url('articles', ['categorie' => 2]). */
function url(string $page, array $params = []): string
{
    return 'index.php?' . http_build_query(['page' => $page] + $params);
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Message affiché une seule fois après une redirection. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $message];
}

function get_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/** Protection CSRF pour tous les formulaires POST. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

/** Date AAAA-MM-JJ -> JJ/MM/AAAA. */
function fdate(?string $date): string
{
    $d = $date ? DateTime::createFromFormat('Y-m-d', substr($date, 0, 10)) : false;
    return $d ? $d->format('d/m/Y') : '';
}

/** Texte coupé à $max caractères. */
function excerpt(?string $text, int $max = 80): string
{
    $text = trim((string) $text);
    return mb_strlen($text) > $max ? mb_substr($text, 0, $max) . '…' : $text;
}
