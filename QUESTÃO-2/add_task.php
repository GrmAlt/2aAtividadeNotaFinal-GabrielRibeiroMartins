<?php
require_once 'database.php';

$descricao = $_POST['descricao'];
$data_vencimento = $_POST['data_vencimento'] ?? null;
$recorrente = isset($_POST['recorrente']) ? 1 : 0;
$intervalo_recorrencia = $_POST['intervalo_recorrencia'] ?? null;

if ($descricao) {
    $pdo = (new Database())->getConnection();
    $addTask = $pdo->prepare("
        INSERT INTO tarefas (descricao, data_vencimento, recorrente, intervalo_recorrencia)
        VALUES (?, ?, ?, ?)
    ");
    $addTask->execute([$descricao, $data_vencimento, $recorrente, $intervalo_recorrencia]);
}

header("Location: index.php");
exit;