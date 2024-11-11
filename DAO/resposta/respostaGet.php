<?php
include '../../Controller/conexao.php';
include '../../Model/resposta.php';

$resposta = new Resposta($pdo);
$respostas = $resposta->listarTodos();

header('Content-Type: application/json');
echo json_encode($respostas);
?>
