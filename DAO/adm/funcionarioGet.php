<?php
include '../../Controller/conexao.php'; // Verifique o caminho do arquivo
include '../../Model/Funcionario.php';

$funcionario = new Funcionario($pdo);
$funcionarios = $funcionario->listarTodos();

header('Content-Type: application/json');
echo json_encode($funcionarios);
?>
