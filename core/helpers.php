<?php
/**
 * Helper functions for views
 * Sistema de Gestión Integral de Caja de Ahorros
 */

/**
 * Generate a URL for the application
 * @param string $path The path to append to BASE_URL
 * @return string The full URL
 */
function url($path = '') {
    if ($path === '') {
        return BASE_URL;
    }
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get or generate CSRF token
 * @return string The CSRF token
 */
function csrf_token() {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get flash message by type
 * @param string $type The type of flash message (success, error, warning, info)
 * @return string|null The flash message or null if not set
 */
function getFlash($type = null) {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($type !== null) {
        // Get specific type of flash message
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        // Also check for simple flash format
        if (isset($_SESSION['flash']) && isset($_SESSION['flash']['type']) && $_SESSION['flash']['type'] === $type) {
            $message = $_SESSION['flash']['message'];
            unset($_SESSION['flash']);
            return $message;
        }
        return null;
    }
    
    // Get all flash messages
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    
    return null;
}

/**
 * Set flash message
 * @param string $type The type of flash message
 * @param string $message The message content
 */
function setFlash($type, $message) {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Escape HTML entities for safe output
 * @param string|null $value The value to escape
 * @return string The escaped value
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
