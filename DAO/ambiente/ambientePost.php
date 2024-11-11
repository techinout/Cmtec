<?php
include '../../Controller/conexao.php';
include '../../Model/Ambiente.php';

$descricao = $_POST['descricao'];

// Verifique se o ambiente já existe
$sql_verifica = "SELECT COUNT(*) FROM Ambiente WHERE descricao = :descricao";
$stmt_verifica = $pdo->prepare($sql_verifica);
$stmt_verifica->bindParam(':descricao', $descricao);
$stmt_verifica->execute();

if ($stmt_verifica->fetchColumn() > 0) {
    echo "<script>
    alert('O ambiente já está cadastrado.');
    window.location.href = '../../admin/paginas/html/ambientes.php';
  </script>";
    exit;
}

// Se não existe, insere o novo ambiente
$sql = "INSERT INTO Ambiente (descricao) VALUES (:descricao)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':descricao', $descricao);

if ($stmt->execute()) {
    echo "<script>
    alert('Ambiente criado com sucesso!');
    window.location.href = '../../admin/paginas/html/ambientes.php';
  </script>";
} else {
    echo "<script>
    alert('Falha ao criar ambiente.');
    window.location.href = '../../admin/paginas/html/ambientes.php';
  </script>";
}
?>
