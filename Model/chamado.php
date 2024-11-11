<?php
class Chamado {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos os Chamados para um aluno específico em ordem de data mais recente
    public function listarPorAluno($codAluno) {
        $sql = "SELECT * FROM Chamado WHERE codAluno = :codAluno ORDER BY data DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':codAluno', $codAluno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodos() {
        $sql = 'SELECT * FROM Chamado';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Criar um novo Chamado
    public function criar($descricao, $codAluno, $codAmbiente, $codEquipamentos, $data, $imagemPath) {
        $sql = "INSERT INTO Chamado (descricao, codAluno, data, imagem, codAmbiente, codEquipamentos, codStatus)
                VALUES (:descricao, :codAluno, :data, :imagem, :codAmbiente, :codEquipamentos, :codStatus)";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Ajuste os parâmetros conforme necessário
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':codAluno', $codAluno);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':imagem', $imagemPath);
        $stmt->bindParam(':codAmbiente', $codAmbiente);
        $stmt->bindParam(':codEquipamentos', $codEquipamentos);
        $stmt->bindValue(':codStatus', 1); // Valor padrão para "Enviado"
        
        return $stmt->execute();
    }

    
    // Método para atualizar o status de um chamado
    public function atualizarStatus($codChamado, $novoStatus) {
        $sql = "UPDATE Chamado SET codStatus = :novoStatus WHERE codChamado = :codChamado";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':novoStatus', $novoStatus, PDO::PARAM_INT);
        $stmt->bindParam(':codChamado', $codChamado, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
