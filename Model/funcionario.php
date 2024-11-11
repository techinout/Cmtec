<?php
class Funcionario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos os funcionários
    public function listarTodos() {
        $sql = "SELECT * FROM Funcionario";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
