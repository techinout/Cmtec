<?php
session_start();
include '../../Controller/conexao.php';
include '../../Model/Resposta.php';
include '../../Model/Chamado.php'; 

$codFuncionario = $_SESSION['user']['codFuncionario'] ?? null;

if (!$codFuncionario) {
    echo "<script>
    alert('Funcionário não autenticado.');
    window.location.href = '../../admin/paginas/html/chamados.php';
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codChamado = $_POST['codChamado'] ?? null;
    $respostaTexto = $_POST['respostaTexto'] ?? null;
    $descricaoChamado = $_POST['descricaoChamado'] ?? null;
    $ambienteChamado = $_POST['ambienteChamado'] ?? null;
    $equipamentoChamado = $_POST['equipamentoChamado'] ?? null;
    $dataChamado = $_POST['dataChamado'] ?? null;

    // Inicializa a variável de imagem
    $imagemPath = '';

    // Verifica se uma imagem foi enviada
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/resposta/'; // Diretório de uploads
        $uniqueName = uniqid() . '_' . basename($_FILES['imagem']['name']);
        $filePath = $uploadDir . $uniqueName;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $filePath)) {
            $imagemPath = $uniqueName; // Armazena o nome da imagem no banco de dados
        }
    }

    // Insere a resposta no banco de dados
    $resposta = new Resposta($pdo);
    if ($resposta->criar($codChamado, $codFuncionario, $respostaTexto, $imagemPath, $descricaoChamado, $ambienteChamado, $equipamentoChamado, $dataChamado)) {
        
        // Atualiza o status do chamado para 3 (por exemplo, "Respondido")
        $chamado = new Chamado($pdo);
        $chamado->atualizarStatus($codChamado, 3); // Atualiza o status para 3
        
        echo "<script>
        alert('Resposta enviada com sucesso!');
        window.location.href = '../../admin/paginas/html/chamados.php';
        </script>";
    } else {
        echo "<script>
        alert('Erro ao enviar a resposta.');
        window.location.href = '../../admin/paginas/html/chamados.php';
        </script>";
    }
} else {
    echo "<script>
    alert('Método inválido.');
    window.location.href = '../../admin/paginas/html/chamados.php';
    </script>";
}

?>
