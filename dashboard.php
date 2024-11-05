<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
include 'db.php';
include 'header.php';

$clientes = $pdo->query('SELECT * FROM clientes')->fetchAll();
$processos = $pdo->query('SELECT * FROM processos')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 1.5rem;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Dashboard</h2>
    
    <!-- Alertas -->
    <div class="alert alert-info" role="alert">
        Bem-vindo ao sistema de advocacia! Aqui você pode gerenciar seus clientes e processos.
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Cartão para Clientes -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Clientes</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($clientes as $cliente): ?>
                            <li class="list-group-item">
                                <a href="detalhes_cliente.php?id=<?php echo urlencode($cliente['id']); ?>">
                                    <?php echo htmlspecialchars($cliente['nome']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Cartão para Processos -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Processos</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($processos as $processo): ?>
                            <li class="list-group-item">
                                <a href="detalhes_processo.php?id=<?php echo urlencode($processo['id']); ?>">
                                    <?php echo htmlspecialchars($processo['numero']) . ' - ' . htmlspecialchars($processo['status']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>
