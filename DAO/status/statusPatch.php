<?php
include '../../Controller/conexao.php';
include '../../Model/Aluno.php';

$aluno = new Aluno($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $user = $_SESSION['user'];
    $codAluno = $user['codAluno'];
    $senhaNova = $_POST['senha'];

    // Recupera a senha atual do aluno
    $stmt = $pdo->prepare('SELECT senha FROM Aluno WHERE codAluno = :codAluno');
    $stmt->execute(['codAluno' => $codAluno]);
    $alunoData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($alunoData) {
        // Verifica se a nova senha é igual à senha atual
        if (password_verify($senhaNova, $alunoData['senha'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'A nova senha não pode ser igual à senha atual.']);
            exit;
        }

        // Atualiza a senha
        $senhaNovaHash = password_hash($senhaNova, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE Aluno SET senha = :senha WHERE codAluno = :codAluno');
        $stmt->execute(['senha' => $senhaNovaHash, 'codAluno' => $codAluno]);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Senha atualizada com sucesso!']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Falha ao atualizar a senha.']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Método HTTP não suportado.']);
}
?>
