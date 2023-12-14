
<?php
header('Content-Type: application/json');

include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = $conexao->query("SELECT * FROM aluno");
    $alunos = [];

    while ($row = $query->fetch_assoc()) {
        // Convertendo 'id' para número (int)
        $row['id'] = intval($row['id']);
        $alunos[] = $row;
    }

    echo json_encode($alunos);
}
// Rota para adicionar um aluno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nome'])) {
        $nome = $conexao->real_escape_string($data['nome']);
        $query = $conexao->query("INSERT INTO aluno (nome) VALUES ('$nome')");

        if ($query) {
            $idInserido = $conexao->insert_id;
            // Recupera o aluno recém-inserido para fornecer a resposta completa
            $resultado = $conexao->query("SELECT * FROM aluno WHERE id = $idInserido");
            $alunoInserido = $resultado->fetch_assoc();

            // Convertendo 'id' para número (int)
            $alunoInserido['id'] = intval($alunoInserido['id']);

            echo json_encode(['mensagem' => 'Aluno adicionado com sucesso', 'aluno' => $alunoInserido]);
        } else {
            echo json_encode(['erro' => 'Erro ao adicionar aluno']);
        }
    } else {
        echo json_encode(['erro' => 'Dados insuficientes']);
    }
}

// Rota para atualizar um aluno
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Obtém o corpo da solicitação
    $putData = file_get_contents("php://input");

    // Converte o corpo da solicitação para um array associativo
    $put_vars = json_decode($putData, true);

    // Verifica se o ID está presente no corpo da solicitação ou na URL
    $id = isset($put_vars['id']) ? $conexao->real_escape_string($put_vars['id']) : $conexao->real_escape_string($_GET['id']);
    $nome = $conexao->real_escape_string($put_vars['nome']);

    if (isset($id) && isset($nome)) {
        $query = $conexao->query("UPDATE aluno SET nome='$nome' WHERE id=$id");

        if ($query) {
            echo json_encode(['mensagem' => 'Aluno atualizado com sucesso']);
        } else {
            echo json_encode(['erro' => 'Erro ao atualizar aluno', 'mysql_error' => $conexao->error]);
        }
    } else {
        echo json_encode(['erro' => 'Dados insuficientes']);
    }
}

// Rota para excluir um aluno
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtém o corpo da solicitação
    $deleteData = file_get_contents("php://input");

    // Converte o corpo da solicitação para um array associativo
    $delete_vars = json_decode($deleteData, true);

    // Verifica se o ID está presente no corpo da solicitação ou na URL
    $id = isset($delete_vars['id']) ? $conexao->real_escape_string($delete_vars['id']) : $conexao->real_escape_string($_GET['id']);

    if (isset($id)) {
        $query = $conexao->query("DELETE FROM aluno WHERE id=$id");

        if ($query) {
            echo json_encode(['mensagem' => 'Aluno excluído com sucesso']);
        } else {
            echo json_encode(['erro' => 'Erro ao excluir aluno', 'mysql_error' => $conexao->error]);
        }
    } else {
        echo json_encode(['erro' => 'Dados insuficientes']);
    }
}

// Fechar a conexão
$conexao->close();

?>