<?php
require_once 'database.php';

$tarefa_id = intval($_POST['tarefa_id']);
$dependencia_id = intval($_POST['dependencia_id']);

if ($tarefa_id && $dependencia_id && $tarefa_id != $dependencia_id) {
    $pdo = (new Database())->getConnection();

    try {
        
        $insertDependency = $pdo->prepare("INSERT OR IGNORE INTO dependencias (tarefa_id, dependencia_id) VALUES (?, ?)");
        $insertDependency->execute([$tarefa_id, $dependencia_id]);

        if ($insertDependency->rowCount() > 0) {
            // Se inseriu com sucesso
            header("Location: index.php?dependencia_mensagem=sucesso");
        } else {
            // Já existia a relação
            header("Location: index.php?dependencia_mensagem=já_existe");
        }
    } catch (Exception $e) {
        header("Location: index.php?dependencia_mensagem=erro");
    }
} else {
    header("Location: index.php?dependencia_mensagem=invalida");
}

exit;