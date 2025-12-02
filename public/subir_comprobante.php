<?php
header("Content-Type: application/json");
require_once "../config/db.php";

$db = new DB();
$cn = $db->conn;

// Validar parámetros
$device = $_POST["device_id"] ?? null;
$transaccion = $_POST["transaccion"] ?? null;

if (!$device) {
    echo json_encode(["status" => "error", "message" => "device_id requerido"]);
    exit;
}

if (!$transaccion) {
    echo json_encode(["status" => "error", "message" => "Número de transacción requerido"]);
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