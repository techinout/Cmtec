<?php
include '../../Controller/conexao.php';
include '../../Model/Chamado.php';

$chamado = new Chamado($pdo);
$chamados = $chamado->listarTodos();

header('Content-Type: application/json');
echo json_encode($chamados);
?>
