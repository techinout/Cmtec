<?php
include './Controller/conexao.php'; // Verifique o caminho do arquivo

// Obtém os dados do formulário
$nome = $_POST['name'];
$email = $_POST['email'];
$senha = $_POST['password'];

// Valida o email
if (!preg_match('/^[^\s@]+@etec\.sp\.gov\.br$/', $email)) {
   header("Location: login.html?errorDominio");
  exit();
    exit;
}

// Verifica se o email já existe no banco de dados
$sql = "SELECT * FROM Aluno WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
  header("Location: login.html?error");
  exit();
    exit;
}

// Criptografa a senha
$senhaCriptografada = password_hash($senha, PASSWORD_BCRYPT);

// Insere no banco de dados
$sql = "INSERT INTO Aluno (nome, email, senha) VALUES (:nome, :email, :senha)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nome', $nome);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':senha', $senhaCriptografada);

if ($stmt->execute()) {
  header("Location: login.html?success");
  exit();
} else {
  header("Location: login.html?error");
  exit();
}
?>
