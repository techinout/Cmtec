<?php
class Ambiente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos os Ambientes
    public function listarTodos() {
        $sql = "SELECT * FROM Ambiente";
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

    // Criar um novo Ambiente
    public function criar($descricao) {
        if ($this->ambienteExiste($descricao)) {
            return false;  // Retorna falso se o ambiente já existir
        }
        $sql = "INSERT INTO Ambiente (descricao) VALUES (:descricao)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':descricao', $descricao);
        return $stmt->execute();
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
