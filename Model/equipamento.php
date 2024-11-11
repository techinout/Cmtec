<?php
class Equipamento {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos os Ambientes
    public function listarTodos() {
        $sql = "SELECT * FROM Equipamentos";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar se um Ambiente já existe
    public function ambienteExiste($descricao) {
        $sql = "SELECT COUNT(*) FROM Ambiente WHERE descricao = :descricao";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;  // Retorna verdadeiro se o ambiente já existir
    }

    // Atualizar parcialmente um Ambiente
    public function atualizarParcial($codAmbiente, $dados) {
        $set = [];
        $params = [':codAmbiente' => $codAmbiente];
        
        foreach ($dados as $key => $value) {
            $set[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $set = implode(', ', $set);
        $sql = "UPDATE Ambiente SET $set WHERE codAmbiente = :codAmbiente";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}

?>
