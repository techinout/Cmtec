<?php
include '../../Controller/conexao.php';
include '../../Model/Ambiente.php';

$ambiente = new Ambiente($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    // Obtendo os dados do formulário
    $descricao = $_POST['descricao'];
    $codAmbiente = $_POST['codAmbiente'];

    // Verifica se o ambiente com a nova descrição já existe
    $stmt = $pdo->prepare('SELECT * FROM Ambiente WHERE descricao = :descricao AND codAmbiente != :codAmbiente');
    $stmt->execute(['descricao' => $descricao, 'codAmbiente' => $codAmbiente]);
    $ambienteData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ambienteData) {
        // Ambiente já existe com a nova descrição
        echo "<script>
        alert('O ambiente já está cadastrado.');
        window.location.href = '../../admin/paginas/html/ambientes.php';
      </script>";
        exit;
    } else {
        // Atualiza o ambiente
        $stmt = $pdo->prepare('UPDATE Ambiente SET descricao = :descricao WHERE codAmbiente = :codAmbiente');
        $stmt->execute(['descricao' => $descricao, 'codAmbiente' => $codAmbiente]);

        echo "<script>
        alert('O ambiente foi atualizado com sucesso.');
        window.location.href = '../../admin/paginas/html/ambientes.php';
      </script>";
        exit;
    }
} else {
    // Caso o método HTTP não seja POST, redireciona com uma mensagem de erro
    echo "<script>
    alert('Método HTTP inválido.');
    window.location.href = '../../admin/paginas/html/ambientes.php';
  </script>";
    exit;
}

?>
