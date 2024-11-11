<?php
include '../../Controller/conexao.php';
include '../../Model/equipamento.php';

$equipamento = new Equipamento($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    // Obtendo os dados do formulário
    $descricao = $_POST['descricao'];
    $codEquipamentos = $_POST['codEquipamentos'];

    // Verifica se o equipamento com a nova descrição já existe
    $stmt = $pdo->prepare('SELECT * FROM Equipamentos WHERE descricao = :descricao AND codEquipamentos != :codEquipamentos');
    $stmt->execute(['descricao' => $descricao, 'codEquipamentos' => $codEquipamentos]);
    $equipamentoData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($equipamentoData) {
        // equipamento já existe com a nova descrição
        echo "<script>
        alert('O equipamento já está cadastrado.');
        window.location.href = '../../admin/paginas/html/equipamentos.php';
      </script>";
        exit;
    } else {
        // Atualiza o equipamento
        $stmt = $pdo->prepare('UPDATE Equipamentos SET descricao = :descricao WHERE codEquipamentos = :codEquipamentos');
        $stmt->execute(['descricao' => $descricao, 'codEquipamentos' => $codEquipamentos]);

        echo "<script>
        alert('O equipamento foi atualizado com sucesso.');
        window.location.href = '../../admin/paginas/html/equipamentos.php';
      </script>";
        exit;
    }
} else {
    // Caso o método HTTP não seja POST, redireciona com uma mensagem de erro
    echo "<script>
    alert('Método HTTP inválido.');
    window.location.href = '../../admin/paginas/html/equipamentos.php';
  </script>";
    exit;
}

?>
