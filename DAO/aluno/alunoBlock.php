<?php
session_start();
include '../../Controller/conexao.php'; // Verifique o caminho do arquivo

if (!isset($_SESSION['token'])) {
    header('Location: ../login.html');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Verifica o status atual do aluno
    $sql = "SELECT status FROM Aluno WHERE codAluno = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $statusAtual = $stmt->fetchColumn(); // Obtém o status atual

    if ($statusAtual === false) {
        echo "<script>
            alert('Aluno não encontrado.');
            window.location.href = '../../admin/paginas/html/usuarios.php'; // Redirecione para a página onde está a tabela
        </script>";
        exit;
    }

    // Alterna o status do aluno
    if ($statusAtual == 0) { // Se o aluno estiver bloqueado
        $novoStatus = 1; // Muda para ativo
        $mensagem = 'Aluno desbloqueado com sucesso!';
    } else {
        $novoStatus = 0; // Muda para bloqueado
        $mensagem = 'Aluno bloqueado com sucesso!';
    }

    // Atualiza o status do aluno
    $sql = "UPDATE Aluno SET status = :novoStatus WHERE codAluno = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':novoStatus', $novoStatus, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>
            alert('$mensagem');
            window.location.href = '../../admin/paginas/html/usuarios.php'; // Redirecione para a página onde está a tabela
        </script>";
    } else {
        echo "<script>
            alert('Falha ao atualizar o status do aluno.');
            window.location.href = '../../admin/paginas/html/usuarios.php'; // Redirecione para a página onde está a tabela
        </script>";
    }
} else {
    echo "<script>
        alert('ID do aluno não fornecido.');
        window.location.href = '../../admin/paginas/html/usuarios.php'; // Redirecione para a página onde está a tabela
    </script>";
}
?>
