<?php
require_once 'database.php';
$id = intval($_GET['id']);
$pdo = (new Database())->getConnection();
$pdo->prepare("DELETE FROM tarefas WHERE id = ?")->execute([$id]);
header("Location: index.php");
exit;