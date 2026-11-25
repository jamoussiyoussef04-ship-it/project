<?php

declare(strict_types=1);

/**
 * Fonctions utilitaires globales pour les contrôleurs et vues MVC.
 */

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('setFlash')) {
    function setFlash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        $_SESSION['flashes'][] = [
            'type'    => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('consumeFlashes')) {
    /**
     * @return array<int, array{type: string, message: string}>
     */
    function consumeFlashes(): array
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);
        return $flashes;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('formatDateFr')) {
    function formatDateFr(?string $dateStr): string
    {
        if ($dateStr === null || trim($dateStr) === '') {
            return '-';
        }
        try {
            $dt = new DateTime($dateStr);
            return strlen(trim($dateStr)) > 10 ? $dt->format('d/m/Y H:i') : $dt->format('d/m/Y');
        } catch (Throwable) {
            return $dateStr;
        }
    }
}

if (!function_exists('getCampagneBadgeClass')) {
    function getCampagneBadgeClass(string $statut): string
    {
        return match ($statut) {
            'Planifiee', 'En cours' => 'success',
            'Terminee'              => 'pending',
            'Annulee'               => 'failed',
            default                 => 'pending',
        };
    }
}

if (!function_exists('getParticipationBadgeClass')) {
    function getParticipationBadgeClass(string $statut): string
    {
        return match ($statut) {
            'Confirmee'  => 'success',
            'En attente' => 'pending',
            'Annulee'    => 'failed',
            default      => 'pending',
        };
    }
}
