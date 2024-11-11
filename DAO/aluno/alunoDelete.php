<?php
session_start();
include '../../Controller/conexao.php';  // Inclua seu arquivo de conexão com o banco de dados

if (!isset($_SESSION['token'])) {
    header('Location: ../login.html');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM Aluno WHERE codAluno = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        header('Location: ../../admin/paginas/html/usuarios.php');  // Redirecione para a lista de usuários após a exclusão
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    header('Location: ../../admin/paginas/html/usuarios.php');
}
?>
