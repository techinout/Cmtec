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
        // Obtém o caminho da imagem antes de excluir o chamado
        $sql = "SELECT imagem FROM Chamado WHERE codChamado = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $chamado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($chamado && !empty($chamado['imagem'])) {
            $imagemCaminho = $chamado['imagem'];

            // Verifica se o arquivo de imagem existe e tenta removê-lo
            if (file_exists($imagemCaminho)) {
                unlink($imagemCaminho);  // Exclui a imagem do servidor
            }
        }

        // Exclui o chamado do banco de dados
        $sql = "DELETE FROM Chamado WHERE codChamado = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Redireciona para a página de chamados
        header('Location: ../../admin/paginas/html/chamados.php');
        exit;
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    header('Location: ../../admin/paginas/html/chamados.php');
    exit;
}
?>
