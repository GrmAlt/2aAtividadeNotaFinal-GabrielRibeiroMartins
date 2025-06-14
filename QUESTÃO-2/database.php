<?php
class Database {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO('sqlite:tasks.db');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->createTables();
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }
    private function createTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS tarefas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            descricao TEXT NOT NULL,
            data_vencimento DATE,
            concluida BOOLEAN DEFAULT 0,
            recorrente BOOLEAN DEFAULT 0,
            intervalo_recorrencia INTEGER DEFAULT NULL,
            ultima_criacao DATE DEFAULT CURRENT_DATE
        );

        CREATE TABLE IF NOT EXISTS dependencias (
            tarefa_id INTEGER,
            dependencia_id INTEGER,
            FOREIGN KEY(tarefa_id) REFERENCES tarefas(id),
            FOREIGN KEY(dependencia_id) REFERENCES tarefas(id),
            PRIMARY KEY(tarefa_id, dependencia_id)
        );
        ";

        $this->pdo->exec($sql);
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>