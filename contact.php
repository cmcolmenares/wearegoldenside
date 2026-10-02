<?php
/**
 * GoldenSide – Procesador del formulario de contacto.
 *
 * Compatible con hosting compartido IONOS (PHP 7.4+ / 8.x).
 * Usa la función mail() nativa de PHP.
 *
 * CONFIGURACIÓN OBLIGATORIA antes de subir:
 *  1) Edita las constantes MAIL_TO y MAIL_FROM más abajo.
 *  2) MAIL_FROM debe ser una dirección creada en IONOS sobre tu propio dominio
 *     (p.ej. no-reply@wearegoldenside.com). Si usas un From de otro dominio
 *     (gmail, etc.) los correos se rechazarán por SPF/DKIM.
 *  3) Verifica en el panel de IONOS que PHP esté habilitado para tu dominio.
 *  4) Sube este archivo a la raíz del sitio (mismo nivel que index.html).
 */

// ───── Configuración ─────
const MAIL_TO       = 'wearegoldenside@gmail.com';            // Destinatario
const MAIL_FROM     = 'no-reply@wearegoldenside.com';         // Debe existir en IONOS
const MAIL_FROM_NAME = 'GoldenSide Web';
const MAIL_SUBJECT_PREFIX = '[Web GoldenSide]';

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
// Si el bot rellena este campo invisible, fingimos éxito y descartamos.
if (!empty($_POST['website'])) {
    jsonResponse(200, ['ok' => true]);
}

// ───── Lectura y saneamiento ─────
$nombre     = clean($_POST['nombre']    ?? '');
$emailRaw   = clean($_POST['email']     ?? '');
$asunto     = clean($_POST['asunto']    ?? '');
$mensaje    = trim($_POST['mensaje']    ?? '');
$privacidad = isset($_POST['privacidad']);

// ───── Validación ─────
$errors = [];
if ($nombre === '' || mb_strlen($nombre) > 100)            $errors[] = 'nombre';
if (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL))         $errors[] = 'email';
if (!in_array($asunto, ['prensa','booking','colaboracion','otro'], true)) $errors[] = 'asunto';
if ($mensaje === '' || mb_strlen($mensaje) > 5000)         $errors[] = 'mensaje';
if (!$privacidad)                                          $errors[] = 'privacidad';

if ($errors) {
    jsonResponse(422, ['ok' => false, 'error' => 'validation', 'fields' => $errors]);
}

// ───── Construcción del mensaje ─────
$asuntoLabels = [
    'prensa'        => 'Prensa',
    'booking'       => 'Booking',
    'colaboracion'  => 'Colaboración',
    'otro'          => 'Otro',
];
$asuntoLabel = $asuntoLabels[$asunto];

$subject = sprintf('%s %s – %s', MAIL_SUBJECT_PREFIX, $asuntoLabel, $nombre);

$bodyLines = [
    "Nuevo mensaje desde wearegoldenside.com",
    str_repeat('—', 40),
    "Nombre:  {$nombre}",
    "Email:   {$emailRaw}",
    "Asunto:  {$asuntoLabel}",
    str_repeat('—', 40),
    "Mensaje:",
    $mensaje,
    str_repeat('—', 40),
    "IP:      " . ($_SERVER['REMOTE_ADDR'] ?? 'n/a'),
    "UA:      " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'n/a', 0, 200),
    "Fecha:   " . date('c'),
];
$body = implode("\n", $bodyLines);

// ───── Cabeceras ─────
$fromHeader = sprintf('%s <%s>', MAIL_FROM_NAME, MAIL_FROM);
$headers = [
    'From: ' . $fromHeader,
    'Reply-To: ' . $emailRaw,
    'X-Mailer: PHP/' . phpversion(),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
];

// El parámetro -f fuerza el Return-Path, importante en IONOS para que SPF cuadre.
$additionalParams = '-f' . MAIL_FROM;

$sent = @mail(
    MAIL_TO,
    '=?UTF-8?B?' . base64_encode($subject) . '?=',
    $body,
    implode("\r\n", $headers),
    $additionalParams
);

if (!$sent) {
    jsonResponse(500, ['ok' => false, 'error' => 'mail_failed']);
}

jsonResponse(200, ['ok' => true]);
