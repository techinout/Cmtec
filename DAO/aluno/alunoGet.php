<?php
include '../../Controller/conexao.php';
include '../../Model/Aluno.php';

$aluno = new Aluno($pdo);
$alunos = $aluno->listarTodos();

header('Content-Type: application/json');
echo json_encode($alunos);
?>
