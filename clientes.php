<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'header.php';

function buscarClientes($pdo) {
    $stmt = $pdo->query("SELECT clientes.id, clientes.cpf_cnpj, clientes.nome, clientes.email, processos.status 
                         FROM clientes
                         LEFT JOIN processos ON clientes.id = processos.cliente_id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientes = buscarClientes($pdo);

if (isset($_GET['delete'])) {
    $idCliente = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->execute([$idCliente]);

    if ($stmt->rowCount() > 0) {
        echo '<div class="alert alert-success mt-3">Cliente excluído com sucesso!</div>';
        $clientes = buscarClientes($pdo); 
    } else {
        echo '<div class="alert alert-danger mt-3">Erro ao excluir o cliente.</div>';
    }
}

if (isset($_GET['archive'])) {
    $idCliente = $_GET['archive'];
    $stmt = $pdo->prepare("UPDATE processos SET status = 'Arquivado' WHERE cliente_id = ?");
    $stmt->execute([$idCliente]);

    if ($stmt->rowCount() > 0) {
        echo '<div class="alert alert-success mt-3">Cliente arquivado com sucesso!</div>';
        $clientes = buscarClientes($pdo);
    } else {
        echo '<div class="alert alert-danger mt-3">Erro ao arquivar o cliente.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2>Lista de Clientes</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>CPF/CNPJ</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['cpf_cnpj']) ?></td>
                        <td>
                            <a href="detalhes_cliente.php?id=<?= $cliente['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($cliente['nome']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= htmlspecialchars($cliente['status']) ?></td>
                        <td>
                            <a href="editar_cliente.php?id=<?= $cliente['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="?delete=<?= $cliente['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                            <a href="?archive=<?= $cliente['id'] ?>" class="btn btn-secondary btn-sm">Arquivar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
