<?php
session_start();
include '../../Controller/conexao.php';
include '../../Model/Aluno.php';

$aluno = new Aluno($pdo);
$codAluno = $_SESSION['user']['codAluno'] ?? null; // Adiciona verificação

if (!$codAluno) {
    die('Erro: Código do aluno não encontrado na sessão.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaNova = $_POST['senha'] ?? null;

    $imagemPath = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/usuario/';
        $uniqueName = uniqid() . '_' . basename($_FILES['imagem']['name']);
        $filePath = $uploadDir . $uniqueName;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $filePath)) {
            $imagemPath = $filePath;
        } else {
            die('Erro: Falha ao mover o arquivo de upload.');
        }
    }

    if ($senhaNova || $imagemPath) {
        $dados = [];
        if ($senhaNova) {
            $dados['senha'] = password_hash($senhaNova, PASSWORD_DEFAULT);
        }
        if ($imagemPath) {
            $dados['imagem'] = $imagemPath;
        }

        if ($aluno->atualizarParcial($codAluno, $dados)) {
            header("Location: ../../usuario/chamados.php?success");
            exit();
        } else {
            $_SESSION['mensagem'] = 'Erro ao atualizar os dados.';
            header('Location: ../../usuario/chamados.php?error');
            exit();
        }
    } else {
        die('Nenhum dado foi fornecido para atualização.');
    }
}

?>
