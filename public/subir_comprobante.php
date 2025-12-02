<?php
header("Content-Type: application/json");
require_once "../config/db.php";

$db = new DB();
$cn = $db->conn;

// Validar parámetros
$device = $_POST["device_id"] ?? null;
$transaccion = $_POST["transaccion"] ?? null;
$file = $_FILES["comprobante"] ?? null;

if (!$device) {
    echo json_encode(["status" => "error", "message" => "device_id requerido"]);
    exit;
}

if (!$transaccion) {
    echo json_encode(["status" => "error", "message" => "Número de transacción requerido"]);
    exit;
}

if (!$file) {
    echo json_encode(["status" => "error", "message" => "No se recibió archivo"]);
    exit;
}

// Extensiones permitidas
$allowed = ["jpg", "jpeg", "png", "webp"];
$ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode([
        "status" => "error",
        "message" => "Formato inválido. Solo JPG, PNG, WEBP"
    ]);
    exit;
}

// Carpeta de uploads
$uploadDir = __DIR__ . "/uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

// Nombre final del archivo
$nombreArchivo = time() . "_" . rand(1000, 9999) . "." . $ext;
$rutaFinal = $uploadDir . $nombreArchivo;

if (!move_uploaded_file($file["tmp_name"], $rutaFinal)) {
    echo json_encode(["status" => "error", "message" => "Error al guardar archivo"]);
    exit;
}

// Registrar en BD
$stmt = $cn->prepare("
    INSERT INTO pagos (device_id, transaccion, comprobante, estado, fecha)
    VALUES (?, ?, ?, 'pendiente', NOW())
");

$stmt->bind_param("sss", $device, $transaccion, $nombreArchivo);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "ok",
        "message" => "Comprobante enviado correctamente",
        "file" => $nombreArchivo
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error al guardar en base de datos"
    ]);
}