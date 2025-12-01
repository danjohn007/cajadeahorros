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
 * Get flash message
 * Uses the same format as Controller::setFlash() - ['type' => $type, 'message' => $message]
 * 
 * @param string|null $type Optional type to filter (success, error, warning, info)
 * @return array|string|null The flash message array, message string if type specified, or null
 */
function getFlash($type = null) {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    
    $flash = $_SESSION['flash'];
    
    if ($type !== null) {
        // Check if the flash message matches the requested type
        if (isset($flash['type']) && $flash['type'] === $type) {
            unset($_SESSION['flash']);
            return $flash['message'];
        }
        return null;
    }
    
    // Return the entire flash message and clear it
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Escape HTML entities for safe output
 * @param string|null $value The value to escape
 * @return string The escaped value
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
