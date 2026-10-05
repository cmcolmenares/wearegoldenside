<?php
/**
 * GoldenSide – Configuración compartida para scripts de servidor.
 *
 * Carga variables desde `.env` (no commiteado, ver `.env.example`) y las
 * expone como constantes. En hosting compartido IONOS no siempre es posible
 * definir variables de entorno reales a nivel de PHP-FPM, por eso se usa un
 * archivo `.env` junto a este script como alternativa sin dependencias.
 */

function loadEnvFile(string $path): void {
    if (!is_readable($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || substr($line, 0, 1) === '#') {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($key !== '' && getenv($key) === false) {
            putenv("{$key}={$value}");
        }
    }
}

function env(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

loadEnvFile(__DIR__ . '/.env');

// ───── Base de datos (MySQL incluido en el hosting IONOS) ─────
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', ''));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));

// ───── Remitente de correos transaccionales ─────
// Debe ser una dirección creada en IONOS sobre el propio dominio (SPF/DKIM).
define('MAIL_FROM', env('MAIL_FROM', 'no-reply@wearegoldenside.com'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'GoldenSide Web'));
