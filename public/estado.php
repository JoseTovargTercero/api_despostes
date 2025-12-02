<?php
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_GET["device_id"])) {
    echo json_encode(["error" => "device_id requerido"]);
    exit;
}

$db = new DB();
$id = $db->conn->real_escape_string($_GET["device_id"]);

$q = $db->conn->query("SELECT estado FROM dispositivos WHERE device_id='$id'");
if ($q->num_rows == 0) {
    echo json_encode(["estado" => "no_registrado"]);
    exit;
}

$row = $q->fetch_assoc();
echo json_encode(["estado" => $row["estado"]]);
