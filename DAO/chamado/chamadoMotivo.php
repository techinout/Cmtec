<?php
include '../../Controller/conexao.php';
include '../../Model/Chamado.php';

$chamado = new Chamado($pdo);

// Verifique se a requisição é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture os dados do formulário
    $id = $_POST['id'];
    $motivoInativacao = $_POST['motivoInativacao'];

    // Prepare a consulta SQL para atualizar o chamado
    $sql = "UPDATE Chamado SET codStatus = 4, motivoInativacao = :motivoInativacao WHERE codChamado = :id";

    // Prepare a declaração
    $stmt = $pdo->prepare($sql);

    // Bind os parâmetros
    $stmt->bindParam(':motivoInativacao', $motivoInativacao);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute a consulta
    if ($stmt->execute()) {
        // Exibir um alerta e redirecionar
        echo "<script>
                alert('Chamado apagado com sucesso!');
                window.location.href = '../../admin/paginas/html/chamados.php';
              </script>";
        exit();
    } else {
        // Lidar com erro
        echo "Erro ao inativar o chamado.";
    }
} else {
    // Se não for um POST, redirecione ou exiba uma mensagem de erro
    header("Location: ../../admin/paginas/html/chamados.php?");
    exit();
}
?>
