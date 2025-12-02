<?php
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_GET["device_id"])) {
    echo json_encode(["error" => "device_id requerido"]);
    exit;
}

$db = new DB();
$id = $db->conn->real_escape_string($_GET["device_id"]);

// Si ya existe, solo devuelve estado
$check = $db->conn->query("SELECT estado FROM dispositivos WHERE device_id='$id'");
if ($check->num_rows > 0) {
    $row = $check->fetch_assoc();
    echo json_encode([
        "status" => "existente",
        "estado" => $row["estado"]
    ]);
    exit;
}

// Crear registro nuevo
$db->conn->query("INSERT INTO dispositivos (device_id) VALUES ('$id')");

echo json_encode([
    "status" => "registrado",
    "estado" => "pendiente"
]);
