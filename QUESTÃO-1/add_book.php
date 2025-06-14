<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeLivro = trim($_POST['titulo']);
    $nomeAutor = trim($_POST['autor']);
    $anoPub = intval($_POST['ano']);

    if ($nomeLivro !== '' && $nomeAutor !== '' && $anoPub > 0) {
        $inserirLivro = $db->prepare("INSERT INTO livros (titulo, autor, ano_publicacao) VALUES (?, ?, ?)");
        $inserirLivro->execute([$nomeLivro, $nomeAutor, $anoPub]);
    }
}

header("Location: index.php");
exit;
?>
