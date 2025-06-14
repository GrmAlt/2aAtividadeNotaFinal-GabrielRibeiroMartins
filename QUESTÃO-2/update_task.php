<?php
require_once 'database.php';

$id = intval($_GET['id']);

$pdo = (new Database())->getConnection();

$updateTask = $pdo->prepare("
    SELECT COUNT(*) 
    FROM dependencias d 
    LEFT JOIN tarefas t ON d.dependencia_id = t.id 
    WHERE d.tarefa_id = ? AND (t.concluida != 1 OR t.id IS NULL)");
$updateTask->execute([$id]);

if ($updateTask->fetchColumn() > 0) {
    header("Location: index.php?erro_dependencia=1");
    exit;
}
$pdo->prepare("UPDATE tarefas SET concluida = 1 WHERE id = ?")->execute([$id]);

header("Location: index.php");
exit;