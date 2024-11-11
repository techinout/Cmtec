<?php
include '../../Controller/conexao.php';
include '../../Model/Ambiente.php';

$ambiente = new Ambiente($pdo);
$ambientes = $ambiente->listarTodos();

header('Content-Type: application/json');
echo json_encode($ambientes);
?>
