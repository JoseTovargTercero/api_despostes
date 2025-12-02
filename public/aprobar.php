<?php
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_POST["device_id"]) || !isset($_POST["accion"])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$db = new DB();
$id = $db->conn->real_escape_string($_POST["device_id"]);
$accion = $db->conn->real_escape_string($_POST["accion"]); // aprobado / rechazado

$db->conn->query("UPDATE dispositivos SET estado='$accion' WHERE device_id='$id'");

echo json_encode(["status" => "ok"]);
