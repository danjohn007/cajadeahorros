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
 * Generate URL for public assets
 * @param string $path The path to the asset
 * @return string The full URL to the asset
 */
function asset($path = '') {
    return PUBLIC_URL . '/' . ltrim($path, '/');
}

/**
 * Get a configuration value from the database
 * Caches values in session for performance
 * @param string $key The configuration key
 * @param mixed $default Default value if key not found
 * @return mixed The configuration value
 */
function getConfig($key, $default = '') {
    // Initialize config cache in session if not exists
    if (!isset($_SESSION['config_cache'])) {
        $_SESSION['config_cache'] = [];
    }
    
    // Return from cache if available
    if (isset($_SESSION['config_cache'][$key])) {
        return $_SESSION['config_cache'][$key];
    }
    
    // Fetch from database
    try {
        $db = Database::getInstance();
        $result = $db->fetch(
            "SELECT valor FROM configuraciones WHERE clave = :clave",
            ['clave' => $key]
        );
        
        $value = $result['valor'] ?? $default;
        $_SESSION['config_cache'][$key] = $value;
        return $value;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Clear configuration cache
 * Call this after updating configurations
 */
function clearConfigCache() {
    unset($_SESSION['config_cache']);
}

/**
 * Get multiple configuration values
 * @param array $keys Array of configuration keys
 * @return array Associative array of key => value
 */
function getConfigs($keys) {
    $result = [];
    foreach ($keys as $key) {
        $result[$key] = getConfig($key);
    }
    return $result;
}

/**
 * Get system colors from configuration
 * @return array Array with color_primario, color_secundario, color_acento
 */
function getSystemColors() {
    return [
        'color_primario' => getConfig('color_primario', '#1e40af'),
        'color_secundario' => getConfig('color_secundario', '#3b82f6'),
        'color_acento' => getConfig('color_acento', '#89ab37')
    ];
}

/**
 * Get site name from configuration
 * @return string Site name
 */
function getSiteName() {
    return getConfig('nombre_sitio', APP_NAME);
}

/**
 * Get logo path from configuration
 * @return string Logo URL
 */
function getLogo() {
    $logo = getConfig('logo', '');
    if ($logo) {
        return asset('images/' . $logo);
    }
    return '';
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

/**
 * Adjust color brightness/lightness
 * @param string $hexColor Hex color code (e.g., #1e40af)
 * @param float $percent Percentage to adjust (-1 to 1, negative = darker, positive = lighter)
 * @return string Adjusted hex color
 */
function adjustColor($hexColor, $percent) {
    // Remove # if present
    $hex = ltrim($hexColor, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    if ($percent > 0) {
        // Lighten
        $r = $r + ((255 - $r) * $percent);
        $g = $g + ((255 - $g) * $percent);
        $b = $b + ((255 - $b) * $percent);
    } else {
        // Darken
        $r = $r + ($r * $percent);
        $g = $g + ($g * $percent);
        $b = $b + ($b * $percent);
    }
    
    // Clamp values
    $r = max(0, min(255, round($r)));
    $g = max(0, min(255, round($g)));
    $b = max(0, min(255, round($b)));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Convert hex color to rgba
 * @param string $hexColor Hex color code
 * @param float $alpha Alpha value (0-1)
 * @return string RGBA color string
 */
function hexToRgba($hexColor, $alpha = 1) {
    $hex = ltrim($hexColor, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgba($r, $g, $b, $alpha)";
}

/**
 * Send email using SMTP configuration from database
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (plain text or HTML)
 * @param bool $isHtml Whether body is HTML
 * @return bool|string True on success, error message on failure
 */
function sendSystemEmail($to, $subject, $body, $isHtml = false) {
    $db = Database::getInstance();
    
    // Get SMTP configuration
    $getConfig = function($key) use ($db) {
        $result = $db->fetch("SELECT valor FROM configuraciones WHERE clave = :clave", ['clave' => $key]);
        return $result['valor'] ?? '';
    };
    
    $smtpHost = $getConfig('smtp_host');
    $smtpPort = (int)($getConfig('smtp_port') ?: 587);
    $smtpUser = $getConfig('smtp_user');
    $smtpPass = $getConfig('smtp_password');
    $smtpEncryption = $getConfig('smtp_encryption') ?: 'tls';
    $fromEmail = $getConfig('correo_sistema') ?: $smtpUser;
    $fromName = $getConfig('smtp_from_name') ?: 'Sistema Caja de Ahorros';
    
    if (empty($smtpHost) || empty($smtpUser) || empty($smtpPass)) {
        return 'Configuración SMTP incompleta';
    }
    
    try {
        // Determine connection type based on port and encryption
        $secure = '';
        if ($smtpEncryption === 'ssl' || $smtpPort == 465) {
            $secure = 'ssl://';
        }
        
        // SSL context configuration
        // Note: Certificate verification is disabled for compatibility with self-signed certificates
        // commonly used in internal/development SMTP servers. For production environments with
        // properly signed certificates, consider enabling verification.
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $socket = stream_socket_client(
            $secure . $smtpHost . ':' . $smtpPort,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            $errorMsg = $errstr ?: 'Connection failed';
            return "No se pudo conectar al servidor SMTP: {$errorMsg} (errno: {$errno})";
        }
        
        stream_set_timeout($socket, 30);
        
        // Read greeting
        $response = _getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '220') {
            fclose($socket);
            return "Error en respuesta del servidor";
        }
        
        // EHLO
        fputs($socket, "EHLO localhost\r\n");
        $ehloResponse = _getSmtpResponse($socket);
        
        // STARTTLS if needed
        if ($smtpEncryption === 'tls' && $smtpPort != 465 && strpos($ehloResponse, 'STARTTLS') !== false) {
            fputs($socket, "STARTTLS\r\n");
            $response = _getSmtpResponse($socket);
            if ($response && substr($response, 0, 3) == '220') {
                $cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
                if ($cryptoEnabled === false) {
                    // TLS 1.2 failed, try with any TLS method
                    $cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                }
                if ($cryptoEnabled) {
                    fputs($socket, "EHLO localhost\r\n");
                    $ehloResponse = _getSmtpResponse($socket);
                }
                // If TLS fails, continue without it - some servers allow this
            }
        }
        
        // AUTH LOGIN
        if (strpos($ehloResponse, 'AUTH') !== false) {
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            if ($response && substr($response, 0, 3) == '334') {
                fputs($socket, base64_encode($smtpUser) . "\r\n");
                $response = fgets($socket, 515);
                if ($response && substr($response, 0, 3) == '334') {
                    fputs($socket, base64_encode($smtpPass) . "\r\n");
                    $response = fgets($socket, 515);
                    if (!$response || substr($response, 0, 3) != '235') {
                        fclose($socket);
                        return "Error de autenticación SMTP";
                    }
                }
            }
        }
        
        // MAIL FROM
        fputs($socket, "MAIL FROM:<{$fromEmail}>\r\n");
        $response = _getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error en MAIL FROM";
        }
        
        // RCPT TO
        fputs($socket, "RCPT TO:<{$to}>\r\n");
        $response = _getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error en RCPT TO";
        }
        
        // DATA
        fputs($socket, "DATA\r\n");
        $response = _getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '354') {
            fclose($socket);
            return "Error en DATA";
        }
        
        // Build message
        $headers = "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: " . ($isHtml ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        
        fputs($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
        $response = _getSmtpResponse($socket);
        
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        if (!$response || substr($response, 0, 3) != '250') {
            return "Error al enviar mensaje";
        }
        
        return true;
        
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

/**
 * Helper function to read SMTP response
 */
function _getSmtpResponse($socket) {
    $response = '';
    while ($line = fgets($socket, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) == ' ') {
            break;
        }
    }
    return $response;
}

/**
 * Check if a module is enabled based on system configuration
 * @param string $modulo The module identifier
 * @param array $modulosDeshabilitados Array of disabled module identifiers
 * @param string $userRole Current user's role
 * @return bool True if module is enabled, false otherwise
 */
function isModuloEnabled($modulo, $modulosDeshabilitados, $userRole) {
    // Programador always sees all modules
    if ($userRole === 'programador') {
        return true;
    }
    return !in_array($modulo, $modulosDeshabilitados);
}

/**
 * Format phone number for tel: links (removes non-numeric characters except +)
 * @param string $phone Phone number
 * @return string Formatted phone number
 */
function formatPhoneForTel($phone) {
    return preg_replace('/[^0-9+]/', '', $phone);
}

/**
 * Generate styled HTML email template
 * @param string $title Email title/subject
 * @param string $content Main content of the email (can be HTML)
 * @param string $buttonText Optional button text
 * @param string $buttonUrl Optional button URL
 * @return string Styled HTML email content
 */
function generateStyledEmail($title, $content, $buttonText = '', $buttonUrl = '') {
    $db = Database::getInstance();
    
    // Get system configuration
    $getConfig = function($key, $default = '') use ($db) {
        $result = $db->fetch("SELECT valor FROM configuraciones WHERE clave = :clave", ['clave' => $key]);
        return $result['valor'] ?? $default;
    };
    
    $siteName = $getConfig('nombre_sitio', 'Sistema de Caja de Ahorros');
    $logoFile = $getConfig('logo', '');
    $colorPrimario = $getConfig('color_primario', '#1e40af');
    $colorSecundario = $getConfig('color_secundario', '#3b82f6');
    $telefonoContacto = $getConfig('telefono_contacto', '');
    $emailContacto = $getConfig('email_contacto', '');
    $horarioAtencion = $getConfig('horario_atencion', 'Lunes a Viernes 9:00 - 18:00');
    
    // Build logo URL
    $logoUrl = '';
    if ($logoFile && defined('PUBLIC_URL')) {
        $logoUrl = PUBLIC_URL . '/images/' . $logoFile;
    }
    
    // Build button HTML if provided
    $buttonHtml = '';
    if ($buttonText && $buttonUrl) {
        $buttonHtml = <<<HTML
        <tr>
            <td align="center" style="padding: 30px 0;">
                <a href="{$buttonUrl}" style="display: inline-block; padding: 14px 32px; background-color: {$colorSecundario}; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                    {$buttonText}
                </a>
            </td>
        </tr>
HTML;
    }
    
    // Logo section
    $logoHtml = '';
    if ($logoUrl) {
        $logoHtml = '<img src="' . htmlspecialchars($logoUrl) . '" alt="' . htmlspecialchars($siteName) . '" style="max-width: 180px; height: auto;">';
    } else {
        $logoHtml = '<span style="font-size: 28px; font-weight: bold; color: #ffffff;">' . htmlspecialchars($siteName) . '</span>';
    }
    
    // Contact info
    $contactInfo = '';
    if ($telefonoContacto) {
        $contactInfo .= '<span style="margin: 0 10px;">📞 ' . htmlspecialchars($telefonoContacto) . '</span>';
    }
    if ($emailContacto) {
        $contactInfo .= '<span style="margin: 0 10px;">✉️ ' . htmlspecialchars($emailContacto) . '</span>';
    }
    
    $currentYear = date('Y');
    
    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {$colorPrimario} 0%, {$colorSecundario} 100%); padding: 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            {$logoHtml}
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px 30px; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb;">
                            <h1 style="margin: 0 0 20px 0; color: {$colorPrimario}; font-size: 24px; font-weight: 600;">
                                {$title}
                            </h1>
                            <div style="color: #374151; font-size: 16px; line-height: 1.6;">
                                {$content}
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Button Section -->
                    {$buttonHtml}
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 25px 30px; border: 1px solid #e5e7eb; border-top: 0; border-radius: 0 0 12px 12px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 15px;">
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            Horario de atención: <strong>{$horarioAtencion}</strong>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; padding-bottom: 15px; color: #6b7280; font-size: 13px;">
                                        {$contactInfo}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 15px;">
                                        <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                            © {$currentYear} {$siteName}. Todos los derechos reservados.
                                        </p>
                                        <p style="margin: 10px 0 0 0; color: #9ca3af; font-size: 11px;">
                                            Este correo fue enviado automáticamente, por favor no responda a este mensaje.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    
    return $html;
}

/**
 * Send styled system email
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $title Email title (displayed in email body)
 * @param string $content Main content
 * @param string $buttonText Optional button text
 * @param string $buttonUrl Optional button URL
 * @return bool|string True on success, error message on failure
 */
function sendStyledEmail($to, $subject, $title, $content, $buttonText = '', $buttonUrl = '') {
    $htmlBody = generateStyledEmail($title, $content, $buttonText, $buttonUrl);
    return sendSystemEmail($to, $subject, $htmlBody, true);
}
