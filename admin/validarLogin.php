<?php
session_start();
include '../Controller/conexao.php'; // Verifique o caminho do arquivo

// Obtém os dados do formulário
$usuario = $_POST['username'];
$senha = $_POST['password'];

// Consulta o banco de dados
$sql = "SELECT * FROM Funcionario WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $usuario);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o usuário existe e a senha está correta
if ($funcionario && $senha === $funcionario['senha']) {
    $_SESSION['token'] = bin2hex(random_bytes(16)) . '_admin'; 
    $_SESSION['user'] = [
      'nome' => $funcionario['nome'],
      'email' => $funcionario['email'],
      'codFuncionario' => $funcionario['codFuncionario']
  ];// Gera um token único com identificador '_admin'
    header('Location: paginas/html/index.php'); // Redireciona para a página admin após login
    exit;
} else {
    echo "<script>
    alert('Nome de usuário ou senha incorretos.');
    window.location.href = 'login.html';
  </script>";
}
?>
