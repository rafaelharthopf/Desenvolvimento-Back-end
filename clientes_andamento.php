<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'header.php';

function buscarClientesAndamento($pdo) {
    $stmt = $pdo->query("SELECT clientes.id AS cliente_id, clientes.cpf_cnpj, clientes.nome, clientes.email, processos.id AS processo_id, processos.numero AS processo_numero, processos.tipo 
                         FROM clientes
                         LEFT JOIN processos ON clientes.id = processos.cliente_id
                         WHERE processos.status = 'Em Andamento'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientesAndamento = buscarClientesAndamento($pdo);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes Em Andamento</title>
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
        <h2>Clientes Em Andamento</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>CPF/CNPJ</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo do Processo</th>
                    <th>Número do Processo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientesAndamento as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['cpf_cnpj']) ?></td>
                        <td><?= htmlspecialchars($cliente['nome']) ?></td>
                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= htmlspecialchars($cliente['tipo']) ?></td>
                        <td>
                            <a href="detalhes_processo.php?id=<?= urlencode($cliente['processo_id']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($cliente['processo_numero']) ?>
                            </a>
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
