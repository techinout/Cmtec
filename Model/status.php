<?php
class Status {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos os status
    public function listarTodos() {
        $stmt = $this->pdo->prepare("SELECT * FROM Status");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
