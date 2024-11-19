<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'header.php';

function buscarProcessos($pdo) {
    $stmt = $pdo->query("SELECT processos.id, processos.numero, processos.status, processos.prazo, clientes.nome AS cliente_nome 
                         FROM processos 
                         LEFT JOIN clientes ON processos.cliente_id = clientes.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$processos = buscarProcessos($pdo);

if (isset($_GET['delete'])) {
    $idProcesso = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM processos WHERE id = ?");
    $stmt->execute([$idProcesso]);

    if ($stmt->rowCount() > 0) {
        echo '<div class="alert alert-success mt-3">Processo excluído com sucesso!</div>';
        $processos = buscarProcessos($pdo); 
    } else {
        echo '<div class="alert alert-danger mt-3">Erro ao excluir o processo.</div>';
    }
}

if (isset($_GET['archive'])) {
    $idProcesso = $_GET['archive'];
    $stmt = $pdo->prepare("UPDATE processos SET status = 'Arquivado' WHERE id = ?");
    $stmt->execute([$idProcesso]);

    if ($stmt->rowCount() > 0) {
        echo '<div class="alert alert-success mt-3">Processo arquivado com sucesso!</div>';
        $processos = buscarProcessos($pdo);
    } else {
        echo '<div class="alert alert-danger mt-3">Erro ao arquivar o processo.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Processos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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
        <h2>Lista de Processos</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Prazo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processos as $processo): ?>
                    <tr>
                        <td>
                            <a href="detalhes_processo.php?id=<?php echo urlencode($processo['id']); ?>" class="text-decoration-none">
                                <?= htmlspecialchars($processo['numero']) ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($processo['cliente_nome']); ?></td>
                        <td><?= htmlspecialchars($processo['status']) ?></td>
                        <td><?= htmlspecialchars($processo['prazo']) ?></td>
                        <td>
                            <a href="editar_processo.php?id=<?= $processo['id'] ?>" class="btn btn-warning btn-sm"> <i class="fa-regular fa-pen-to-square"></i> Editar</a>
                            <a href="?delete=<?= $processo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este processo?')"><i class="fa-regular fa-trash-can"></i> Excluir</a>
                            <a href="?archive=<?= $processo['id'] ?>" class="btn btn-secondary btn-sm"><i class="fa-solid fa-box-archive"></i> Arquivar</a>
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
