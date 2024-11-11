<?php
session_start();
include '../../Controller/conexao.php';  // Verifique o caminho do arquivo de conexão

// Verifica se o usuário está logado
if (!isset($_SESSION['token'])) {
    header('Location: ../../login.html');
    exit;
}

// Verifica se o formulário foi enviado e se o codAluno está presente
if (isset($_POST['codAluno'])) {
    $codAluno = $_POST['codAluno'];

    try {
        // Prepara a exclusão do usuário com base no codAluno
        $sql = "DELETE FROM Aluno WHERE codAluno = :codAluno";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':codAluno', $codAluno, PDO::PARAM_INT);
        $stmt->execute();

        // Destrói a sessão após a exclusão
        session_destroy();

        // Redireciona para a página de login
        header('Location: ../../login.html');
        exit;
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    // Se o codAluno não foi fornecido, redireciona para a página de login
    header('Location: ../../login.html');
    exit;
}
?>
