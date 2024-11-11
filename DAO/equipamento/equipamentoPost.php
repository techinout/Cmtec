<?php
include '../../Controller/conexao.php';
include '../../Model/Ambiente.php';

$descricaoBase = $_POST['descricao'];  // Nome base do equipamento (ex: Lapis)
$quantidade = $_POST['quantidade'];  // Quantidade de equipamentos a criar

// SQL para verificar quantos equipamentos já existem com a descrição base
$sql_verifica = "SELECT descricao FROM Equipamentos WHERE descricao LIKE :descricao";
$stmt_verifica = $pdo->prepare($sql_verifica);

// Procurar equipamentos que começam com a mesma base (ex: 'Lapis%')
$descricaoLike = $descricaoBase . '%';
$stmt_verifica->bindParam(':descricao', $descricaoLike);
$stmt_verifica->execute();
$equipamentosExistentes = $stmt_verifica->fetchAll(PDO::FETCH_COLUMN);

// Encontrar o maior número já existente
$maiorNumero = 0;
$descricaoBaseExiste = false;

foreach ($equipamentosExistentes as $equipamentoExistente) {
    // Se o equipamento tiver um número, extrair o número
    if (preg_match('/^' . preg_quote($descricaoBase) . ' (\d+)$/', $equipamentoExistente, $matches)) {
        $numero = (int)$matches[1];
        if ($numero > $maiorNumero) {
            $maiorNumero = $numero;
        }
    } elseif ($equipamentoExistente == $descricaoBase) {
        // Se houver exatamente o nome base sem número
        $descricaoBaseExiste = true;
    }
}

// SQL para inserir novos equipamentos
$sql_equipamento = "INSERT INTO Equipamentos (descricao) VALUES (:descricao)";
$stmt_equipamento = $pdo->prepare($sql_equipamento);

// Criar novos equipamentos a partir do maior número encontrado
for ($i = 1; $i <= $quantidade; $i++) {
    if ($i == 1 && !$descricaoBaseExiste && $maiorNumero == 0) {
        // Se o primeiro equipamento e não houver nenhum numerado e também não houver o nome base existente, usar apenas o nome base
        $descricao = $descricaoBase;
    } else {
        // Adiciona o próximo número disponível
        $descricao = $descricaoBase . ' ' . ($maiorNumero + $i);
    }

    // Inserir novo equipamento
    $stmt_equipamento->bindParam(':descricao', $descricao);
    $stmt_equipamento->execute();
}

echo "<script>
alert('Equipamentos criados com sucesso!');
window.location.href = '../../admin/paginas/html/equipamentos.php';
</script>";
?>
