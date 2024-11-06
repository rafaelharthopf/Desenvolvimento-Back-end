<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
include 'db.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$clienteId = $_GET['id'];
$cliente = $pdo->prepare('SELECT * FROM clientes WHERE id = ?');
$cliente->execute([$clienteId]);
$cliente = $cliente->fetch();

$processos = $pdo->prepare('SELECT * FROM processos WHERE cliente_id = ?');
$processos->execute([$clienteId]);
$processos = $processos->fetchAll();

if (!$cliente) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Cliente - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa; 
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        h2 {
            color: #007bff; 
            margin-bottom: 30px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        }
        .btn-primary {
            background-color: #007bff; 
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3; 
        }
        footer {
            margin-top: 30px;
            padding: 20px 0;
            background-color: #f8f9fa;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Detalhes do Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h2>

    <div class="card p-4">
        <h4>Processos</h4>
        <?php if (count($processos) > 0): ?>
            <ul class="list-unstyled">
                <?php foreach ($processos as $processo): ?>
                    <li>
                        <a href="detalhes_processo.php?id=<?php echo urlencode($processo['id']); ?>" class="text-decoration-none">
                            <?php echo htmlspecialchars($processo['numero']) . ' - ' . htmlspecialchars($processo['status']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Este cliente não possui processos registrados.</p>
        <?php endif; ?>
    </div>

    <div class="mt-3">
        <a href="editar_cliente.php?id=<?php echo urlencode($cliente['id']); ?>" class="btn btn-primary">Editar Cliente</a>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
