<?php
require 'database.php';

$resultadoConsulta = $db->query("SELECT * FROM livros ORDER BY ano_publicacao DESC");

$listaDeLivros = $resultadoConsulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Livraria</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>

  <div class="container">
    <h1>Livraria</h1>

    <div class="search-box">
      <input type="text" id="bookSearch" placeholder="Digite o nome do livro..." />
      <button onclick="searchBook()">Buscar</button>
    </div>

    <form id="bookForm" action="add_book.php" method="post">
      <label>Título:
        <input type="text" name="titulo" id="titulo" required>
      </label>
      <label>Autor:
        <input type="text" name="autor" id="autor" required>
      </label>
      <label>Ano:
        <input type="number" name="ano" id="ano" required>
      </label>
      <button type="submit">Adicionar Livro</button>
    </form>

    <hr>

    <h2>Livros Cadastrados</h2>
    <ul id="bookList">
      <?php
        if (!empty($listaDeLivros)):
          foreach ($listaDeLivros as $livroAtual):
      ?>
        <li>
          <?= htmlspecialchars($livroAtual['titulo']) ?> -
          <?= htmlspecialchars($livroAtual['autor']) ?> (<?= $livroAtual['ano_publicacao'] ?>)
          <a href="delete_book.php?id=<?= $livroAtual['id'] ?>">[Excluir]</a>
        </li>
      <?php
          endforeach;
        else:
      ?>
        <li>Nenhum livro cadastrado.</li>
      <?php endif; ?>
    </ul>
  </div>

  <script src="assets/script.js"></script>
</body>
</html>
