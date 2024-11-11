<?php
include '../../Controller/conexao.php';
include '../../Model/Chamado.php';

$chamado = new Chamado($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $codChamado = $_POST['id'];
        $novoStatus = $_POST['status'];

        // Atualiza o status do chamado
        if ($chamado->atualizarStatus($codChamado, $novoStatus)) {
            echo "<script>
        alert('Chamado Atualizado com sucesso!');
        window.location.href = '../../admin/paginas/html/chamados.php';
        </script>";
        } else {
            echo "<script>
            alert('Falha Ao Atualizar o Chamado!');
            window.location.href = '../../admin/paginas/html/chamados.php';
            </script>";
        }
    } else {
        echo "<script>
    alert('Parametros invalidos!');
    window.location.href = '../../admin/paginas/html/chamados.php';
    </script>";
    }
} else {
    echo "<script>
            alert('método HTPP nao suportado!');
            window.location.href = '../../admin/paginas/html/chamados.php';
            </script>";
}
?>
