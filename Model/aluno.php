<?php
class Aluno {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos os alunos
    public function listarTodos() {
        $sql = "SELECT * FROM Aluno";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Criar um novo aluno
    public function criar($email, $senha, $nome) {
        $sql = "INSERT INTO Aluno (email, senha, nome) VALUES (:email, :senha, :nome)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':nome', $nome);
        return $stmt->execute();
    }

    // Atualizar totalmente um aluno
    public function atualizar($codAluno, $email, $senha, $nome) {
        $sql = "UPDATE Aluno SET email = :email, senha = :senha, nome = :nome WHERE codAluno = :codAluno";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':codAluno', $codAluno, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Atualizar parcialmente um aluno
    public function atualizarParcial($codAluno, $dados) {
        $set = [];
        $params = [':codAluno' => $codAluno];
        
        foreach ($dados as $key => $value) {
            $set[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $set = implode(', ', $set);
        $sql = "UPDATE Aluno SET $set WHERE codAluno = :codAluno";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function atualizarImagem($codAluno, $imagem) {
        $sql = "UPDATE Aluno SET imagem = :imagem WHERE codAluno = :codAluno";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->bindParam(':codAluno', $codAluno, PDO::PARAM_INT);
        return $stmt->execute();
    }    
    
}
?>
