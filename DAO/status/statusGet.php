<?php
include '../../Controller/conexao.php';
include '../../Model/status.php';

$status = new Status($pdo);
$status = $status->listarTodos();

header('Content-Type: application/json');
echo json_encode($status);
?>
