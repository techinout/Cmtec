<?php
$dsn = 'mysql:host=localhost;dbname=Escola';
$username = 'root';
$password = ''; // Ajuste conforme necessário

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
}
?>
