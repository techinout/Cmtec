<?php
session_start(); // Inicie a sessão

include '../../Controller/conexao.php';
include '../../Model/Chamado.php';

date_default_timezone_set('America/Sao_Paulo');

$codAluno = $_SESSION['user']['codAluno'] ?? null;

if (!$codAluno) {
    echo "<script>
    alert('Usuário não autenticado.');
    window.location.href = '../../login.html';
  </script>";
    exit;
}

$chamado = new Chamado($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Coleta os dados do formulário
  $descricao = $_POST['descricao'] ?? null;
  $codAmbiente = $_POST['local'] ?? null;
  $codEquipamentos = $_POST['equipamento'] ?? null; // Corrigido para codEquipamentos
  $data = date('Y-m-d H:i:s');
  
  // Inicializa a variável de imagem
  $imagemPath = '';

  var_dump($_POST);  // Para ver os dados enviados pelo formulário
  var_dump($_FILES); // Para ver os arquivos enviados
  
  // Verifica se há arquivos de imagem
  if (isset($_FILES['imagens']) && $_FILES['imagens']['error'][0] === UPLOAD_ERR_OK) {
      $uploadDir = '../../uploads/chamados/';
      $imagemPaths = [];

      foreach ($_FILES['imagens']['name'] as $key => $name) {
          $tmpName = $_FILES['imagens']['tmp_name'][$key];
          $uniqueName = uniqid() . '_' . basename($name);
          $filePath = $uploadDir . $uniqueName;

          if (move_uploaded_file($tmpName, $filePath)) {
              $imagemPaths[] = $filePath;
          }
      }

      $imagemPath = implode(',', $imagemPaths);
  }

  // Verificar se todos os campos obrigatórios estão presentes
  if ($descricao && $codAluno && $codAmbiente && $codEquipamentos) {
    // Criação do chamado com status "Enviado"
    if ($chamado->criar($descricao, $codAluno, $codAmbiente, $codEquipamentos, $data, $imagemPath)) {
      header("Location: ../../usuario/chamados.php?success");
    } else {
      header("Location: ../../usuario/chamados.php?error");
    }
} else {
  
  header("Location: ../../usuario/chamados.php?error");
  exit();
}
}
?>
