<?php
require_once 'database.php';

$db = new Database();
$pdo = $db->getConnection();

$getTasks = $pdo->query("SELECT * FROM tarefas WHERE recorrente = 1");
foreach ($getTasks as $task) {
    $ultima_criacao = new DateTime($task['ultima_criacao']);
    $agora = new DateTime();
    $intervalo = new DateInterval("P{$task['intervalo_recorrencia']}D");

    if ($ultima_criacao->add($intervalo) <= $agora) {
        $pdo->prepare("
            INSERT INTO tarefas (descricao, data_vencimento, recorrente, intervalo_recorrencia, ultima_criacao)
            VALUES (?, ?, 1, ?, CURRENT_DATE)
        ")->execute([
            $task['descricao'],
            $task['data_vencimento'],
            $task['intervalo_recorrencia']
        ]);
    }
}

$tarefas = $pdo->query("SELECT * FROM tarefas ORDER BY concluida ASC, data_vencimento ASC")->fetchAll(PDO::FETCH_ASSOC);

$tarefas_disponiveis = $pdo->query("SELECT id, descricao FROM tarefas WHERE concluida = 0")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4 text-center">Minhas Tarefas</h1>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Adicionar Nova Tarefa</div>
        <div class="card-body">
            <form action="add_task.php" method="post">
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="data_vencimento" class="form-label">Data de Vencimento</label>
                    <input type="date" name="data_vencimento" class="form-control">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="recorrente" id="recorrente" class="form-check-input">
                    <label for="recorrente" class="form-check-label">Tarefa Recorrente</label>
                </div>
                <div class="mb-3" id="intervaloGroup" style="display: none;">
                    <label for="intervalo_recorrencia" class="form-label">Recorrência (dias)</label>
                    <input type="number" name="intervalo_recorrencia" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Adicionar</button>
            </form>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">Definir Dependência</div>
        <div class="card-body">
            <form action="set_dependency.php" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tarefa_id" class="form-label">Selecione uma tarefa</label>
                        <select name="tarefa_id" class="form-select" required>
                            <?php foreach ($tarefas_disponiveis as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['descricao']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dependencia_id" class="form-label">Depende de:</label>
                        <select name="dependencia_id" class="form-select" required>
                            <?php foreach ($tarefas_disponiveis as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['descricao']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary">Salvar Dependência</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2>Tarefas Não Concluídas</h2>
            <ul class="list-group">
                <?php foreach ($tarefas as $t): if ($t['concluida']) continue; ?>
                <li class="list-group-item d-flex justify-content-between 
                    align-items-center">
                    <?= htmlspecialchars($t['descricao']) ?>
                    <span class="badge bg-secondary"><?= $t['data_vencimento'] ?: 'Sem data' ?></span>
                    <div>
                        <a href="update_task.php?id=<?= $t['id'] ?>" 
                            class="btn btn-sm btn-success me-1"></a>
                        <a href="delete_task.php?id=<?= $t['id'] ?>" 
                            onclick="return confirm('Tem certeza?')" class="btn btn-sm btn-danger"></a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Tarefas Concluídas</h2>
            <ul class="list-group">
                <?php foreach ($tarefas as $t): if (!$t['concluida']) continue; ?>
                <li class="list-group-item list-group-item-success text-decoration-line-through">
                    <?= htmlspecialchars($t['descricao']) ?>
                    <small class="text-muted ms-2">(<?= $t['data_vencimento'] ?: 'Sem data' ?>)</small>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
<script>
    document.getElementById('recorrente').addEventListener('change', function () {
        const grupo = document.getElementById('intervaloGroup');
        grupo.style.display = this.checked ? 'block' : 'none';
    });
</script>
  
  <script>
      const urlParams = new URLSearchParams(window.location.search);

      let mensagem = '';
      if (urlParams.has('dependencia_mensagem')) {
          const tipo = urlParams.get('dependencia_mensagem');

          switch (tipo) {
              case 'sucesso':
                  mensagem = "Dependência adicionada.";
                  break;
              case 'já_existe':
                  mensagem = "Essa dependência já existe.";
                  break;
              case 'erro':
                  mensagem = "Erro ao adicionar dependência.";
                  break;
              case 'invalida':
                  mensagem = "Selecione tarefas válidas.";
                  break;
              default:
                  mensagem = "Ação desconhecida.";
                  break;
          }

          alert(mensagem);
          window.history.replaceState({}, document.title, window.location.pathname);
      }
  </script>
</body>
</html>