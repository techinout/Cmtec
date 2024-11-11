<?php
session_start();
include '../../Controller/conexao.php';  // Inclua seu arquivo de conexão com o banco de dados
include '../../Model/Resposta.php';

if (!isset($_SESSION['token'])) {
    header('Location: ../login.html');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Obtém a resposta vinculada ao chamado
        $sql = "SELECT codResposta, imagem FROM Resposta WHERE codChamado = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resposta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resposta) {
            // Obtém o caminho da imagem antes de excluir a resposta
            $imagemCaminho = $resposta['imagem'];
            if (!empty($imagemCaminho) && file_exists($imagemCaminho)) {
                unlink($imagemCaminho);  // Exclui a imagem do servidor
            }

            // Exclui a resposta do banco de dados
            $respostaModel = new Resposta($pdo);
            $respostaModel->excluir($resposta['codResposta']);
        }

        // Atualiza o status do chamado para 2
        $sql = "UPDATE Chamado SET codStatus = 2 WHERE codChamado = :id";
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
