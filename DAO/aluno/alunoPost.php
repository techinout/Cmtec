<?php
include '../../Controller/conexao.php';
include '../../Model/Aluno.php';

$aluno = new Aluno($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? null;
    $senha = $data['senha'] ?? null;
    $nome = $data['nome'] ?? null;

    if ($email && $senha && $nome && $aluno->criar($email, $senha, $nome)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Aluno criado com sucesso!']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Falha ao criar aluno.']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Método HTTP não suportado.']);
}
?>
