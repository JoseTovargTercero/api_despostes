<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "../config/db.php";

// SOLO POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}


$db = new DB();
$conn = $db->conn;

// ============= POST DATA ==================
$device_id = $_POST['device_id'] ?? null;
$codigo    = $_POST['codigo'] ?? null;

if (!$device_id || !$codigo) {
    echo json_encode(["ok" => false, "mensaje" => "Faltan parámetros"]);
    exit;
}

// ============================================
// 1. Buscar código en tabla dispositivos
// ============================================

$sql = "SELECT device_id, estado, fecha_registro, fecha_actualizacion, comprobante 
        FROM dispositivos 
        WHERE comprobante = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "El código no existe"]);
    exit;
}

$row = $res->fetch_assoc();

// ============================================
// 2. Validar vencimiento (más de 1 año)
// ============================================
if (!empty($row['fecha_registro'])) {
    $registro = strtotime($row['fecha_registro']);
    $limite   = strtotime("-1 year");

    if ($registro < $limite) {
        echo json_encode(["ok" => false, "mensaje" => "El código está vencido"]);
        exit;
    }
}

// ============================================
// 3. Si ya está activo
// ============================================
if ($row['estado'] === "activo") {
    echo json_encode(["ok" => false, "mensaje" => "La membresía ya está activa"]);
    exit;
}

// ============================================
// 4. Activar
// ============================================
$ahora = date("Y-m-d H:i:s");

$sql = "UPDATE dispositivos 
        SET estado='aprobado', 
            fecha_actualizacion=?, 
            device_id=? 
        WHERE comprobante=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $ahora, $device_id, $codigo);

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "mensaje" => "Error al activar"]);
    exit;
}

echo json_encode(["ok" => true, "mensaje" => "Membresía activada"]);
exit;