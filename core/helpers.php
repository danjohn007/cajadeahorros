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
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get or generate CSRF token
 * @return string The CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
