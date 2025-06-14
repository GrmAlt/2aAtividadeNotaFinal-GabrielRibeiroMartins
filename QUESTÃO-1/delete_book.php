<?php
require 'database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("DELETE FROM livros WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>