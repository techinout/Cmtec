<?php
include '../../Controller/conexao.php';
include '../../Model/Equipamento.php';

$equipamento = new Equipamento($pdo);
$equipamentos = $equipamento->listarTodos();

header('Content-Type: application/json');
echo json_encode($equipamentos);
?>
