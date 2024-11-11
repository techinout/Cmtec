<?php
session_start();
include './Controller/conexao.php'; // Verifique o caminho do arquivo

// Obtém os dados do formulário
$usuario = $_POST['username'];
$senha = $_POST['password'];

// Consulta o banco de dados
$sql = "SELECT * FROM Aluno WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $usuario);
$stmt->execute();
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);


if ($aluno) {
    // Verifica se o usuário está ativo
    if ($aluno['status'] == 1) {
        // Verifica se a senha está correta
        if (password_verify($senha, $aluno['senha'])) {
            $_SESSION['token'] = bin2hex(random_bytes(16)) . '_aluno';
            $_SESSION['user'] = [
                'nome' => $aluno['nome'],
                'email' => $aluno['email'],
                'codAluno' => $aluno['codAluno']
            ];
            header('Location: usuario/index.php'); // Redireciona para a página do aluno após login
            exit;
        } else {
            header("Location: login.html?incorreto");
             exit();
        }
    } else {
        // Usuário está bloqueado
        header("Location: login.html?block");
             exit();
    }
} else {
    header("Location: login.html?incorreto");
             exit();
}

//esse aqui é especialmente feito para os items que já estão no banco de dados 
if ($aluno) {
    // Verifica se o usuário está ativo
    if ($aluno['status'] == 1) {
        // Verifica se a senha está correta
        if ($aluno && $senha === $aluno['senha']) {
            $_SESSION['token'] = bin2hex(random_bytes(16)) . '_aluno';
            $_SESSION['user'] = [
                'nome' => $aluno['nome'],
                'email' => $aluno['email'],
                'codAluno' => $aluno['codAluno']
            ];
            header('Location: usuario/index.php'); // Redireciona para a página admin após login
            exit;
        } else {
            header("Location: login.html?incorreto");
             exit();
        }
    } else {
        // Usuário está bloqueado
        header("Location: login.html?block");
        exit();
    }
} else {
    header("Location: login.html?incorreto");
    exit();
}
?>






