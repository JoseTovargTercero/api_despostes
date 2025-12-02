<?php
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_POST["device_id"]) || !isset($_FILES["file"])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$db = new DB();
$id = $db->conn->real_escape_string($_POST["device_id"]);

$folder = "../uploads/";
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$filename = $id . "_" . time() . ".jpg";
$path = $folder . $filename;

move_uploaded_file($_FILES["file"]["tmp_name"], $path);

$db->conn->query("UPDATE dispositivos SET comprobante='$filename' WHERE device_id='$id'");

echo json_encode(["status" => "comprobante_subido"]);
