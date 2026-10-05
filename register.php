<?php
/**
 * GoldenSide – Procesador del formulario de registro (/Comunidad).
 *
 * Compatible con hosting compartido IONOS (PHP 7.4+ / 8.x).
 * Inserta en el MySQL incluido en el hosting (vía PDO) y envía un email de
 * agradecimiento con la función mail() nativa (mismo mecanismo que contact.php).
 *
 * CONFIGURACIÓN OBLIGATORIA antes de subir:
 *  1) Crea una base de datos MySQL desde el panel de IONOS (Hosting → Bases
 *     de datos) y crea un archivo `.env` junto a este script a partir de
 *     `.env.example` con sus credenciales.
 *  2) `pdo_mysql` ya viene habilitada por defecto en el hosting compartido
 *     de IONOS; no requiere configuración extra.
 *  3) Ejecuta `sql/usuarios_registrados.sql` contra la base de datos antes
 *     del primer registro.
 *  4) Sube este archivo junto con `config.php` a la raíz del sitio (mismo
 *     nivel que index.html).
 */

require __DIR__ . '/config.php';

// ───── Helpers ─────
function jsonResponse(int $status, array $payload): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function clean(string $value): string {
    $value = trim($value);
    // Quita saltos de línea para prevenir header injection
    return preg_replace('/[\r\n]+/', ' ', $value);
}

// ───── Aceptar solo POST ─────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

// ───── Honeypot anti-spam ─────
if (!empty($_POST['website'])) {
    jsonResponse(200, ['ok' => true]);
}

// ───── Lectura y saneamiento ─────
$nombreCompleto = clean($_POST['nombre_completo'] ?? '');
$correoRaw      = clean($_POST['correo'] ?? '');
$privacidad     = isset($_POST['privacidad']);

// ───── Validación ─────
$errors = [];
if ($nombreCompleto === '' || mb_strlen($nombreCompleto) > 100) $errors[] = 'nombre_completo';
if (!filter_var($correoRaw, FILTER_VALIDATE_EMAIL) || mb_strlen($correoRaw) > 255) $errors[] = 'correo';
if (!$privacidad) $errors[] = 'privacidad';

if ($errors) {
    jsonResponse(422, ['ok' => false, 'error' => 'validation', 'fields' => $errors]);
}

// ───── Inserción en MySQL (base de datos interna del hosting IONOS) ─────
try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios_registrados (nombre_completo, correo) VALUES (:nombre, :correo)'
    );
    $stmt->execute([
        'nombre' => $nombreCompleto,
        'correo' => $correoRaw,
    ]);
} catch (PDOException $e) {
    // Error de MySQL 1062 = Duplicate entry (correo ya registrado)
    if (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062) {
        jsonResponse(422, ['ok' => false, 'error' => 'duplicate_email', 'fields' => ['correo']]);
    }
    jsonResponse(500, ['ok' => false, 'error' => 'db_failed']);
}

// ───── Email de agradecimiento ─────
$subject = sprintf('[Web GoldenSide] Bienvenido/a, %s', $nombreCompleto);

$bodyLines = [
    "Hola {$nombreCompleto},",
    "",
    "Gracias por registrarte en wearegoldenside.com.",
    "A partir de ahora serás de los primeros en enterarte de música nueva,",
    "shows y todo lo que se viene en la Underground Era.",
    "",
    "Nos vemos del otro lado de la máscara.",
    "",
    "— GoldenSide",
];
$body = implode("\n", $bodyLines);

$fromHeader = sprintf('%s <%s>', MAIL_FROM_NAME, MAIL_FROM);
$headers = [
    'From: ' . $fromHeader,
    'X-Mailer: PHP/' . phpversion(),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
];
$additionalParams = '-f' . MAIL_FROM;

// El registro en BD ya se guardó: si el email falla, no bloqueamos la respuesta de éxito.
@mail(
    $correoRaw,
    '=?UTF-8?B?' . base64_encode($subject) . '?=',
    $body,
    implode("\r\n", $headers),
    $additionalParams
);

jsonResponse(200, ['ok' => true]);
