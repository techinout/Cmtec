<?php
class Resposta {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todas as respostas
    public function listarTodos() {
        $sql = "SELECT * FROM Resposta";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar respostas por aluno
    public function listarPorAluno($codAluno) {
        $sql = "
            SELECT r.* 
            FROM Resposta r
            JOIN Chamado c ON r.codChamado = c.codChamado
            WHERE c.codAluno = :codAluno
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codAluno' => $codAluno]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Criar uma nova resposta
    public function criar($codChamado, $codFuncionario, $respostaTexto, $imagem = null, $descricaoChamado = null, $ambienteChamado = null, $equipamentoChamado = null, $dataChamado = null, $imagemChamado = null) {
        $sql = "INSERT INTO Resposta (codChamado, codFuncionario, respostaTexto, imagem, descricaoChamado, ambienteChamado, equipamentoChamado, dataChamado, imagemChamado) 
                VALUES (:codChamado, :codFuncionario, :respostaTexto, :imagem, :descricaoChamado, :ambienteChamado, :equipamentoChamado, :dataChamado, :imagemChamado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codChamado' => $codChamado,
            ':codFuncionario' => $codFuncionario,
            ':respostaTexto' => $respostaTexto,
            ':imagem' => $imagem,
            ':descricaoChamado' => $descricaoChamado,
            ':ambienteChamado' => $ambienteChamado,
            ':equipamentoChamado' => $equipamentoChamado,
            ':dataChamado' => $dataChamado,
            ':imagemChamado' => $imagemChamado
        ]);
        return $this->pdo->lastInsertId();
    }

    // Atualizar uma resposta existente
    public function atualizar($codResposta, $respostaTexto, $imagem = null, $descricaoChamado = null, $ambienteChamado = null, $equipamentoChamado = null, $dataChamado = null, $imagemChamado = null) {
        $sql = "UPDATE Resposta SET 
                respostaTexto = :respostaTexto, 
                imagem = :imagem, 
                descricaoChamado = :descricaoChamado, 
                ambienteChamado = :ambienteChamado, 
                equipamentoChamado = :equipamentoChamado, 
                dataChamado = :dataChamado, 
                imagemChamado = :imagemChamado 
                WHERE codResposta = :codResposta";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codResposta' => $codResposta,
            ':respostaTexto' => $respostaTexto,
            ':imagem' => $imagem,
            ':descricaoChamado' => $descricaoChamado,
            ':ambienteChamado' => $ambienteChamado,
            ':equipamentoChamado' => $equipamentoChamado,
            ':dataChamado' => $dataChamado,
            ':imagemChamado' => $imagemChamado
        ]);
        return $stmt->rowCount();
    }

    // Excluir uma resposta
    public function excluir($codResposta) {
        $sql = "DELETE FROM Resposta WHERE codResposta = :codResposta";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codResposta' => $codResposta]);
        return $stmt->rowCount();
    }

    // Obter uma resposta por ID
    public function obterPorId($codResposta) {
        $sql = "SELECT * FROM Resposta WHERE codResposta = :codResposta";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codResposta' => $codResposta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
