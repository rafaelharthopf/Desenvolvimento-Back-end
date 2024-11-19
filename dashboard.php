<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
include 'header.php';
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT nome_completo FROM usuarios WHERE id = ?');
$stmt->execute([$userId]);
$usuario = $stmt->fetch();

$stmt = $pdo->prepare('SELECT dias_prazo FROM configuracoes WHERE id = 1');
$stmt->execute();
$config = $stmt->fetch();
$diasPrazo = $config ? $config['dias_prazo'] : 7; 

$clientes = $pdo->query('
    SELECT DISTINCT c.* 
    FROM clientes c 
    JOIN processos p ON c.id = p.cliente_id 
    WHERE p.status = "Em andamento"
')->fetchAll();

$processos = $pdo->query('SELECT * FROM processos WHERE status = "Em andamento"')->fetchAll();

$hoje = new DateTime();
$hoje->setTime(0, 0, 0);
$alertas = [];

foreach ($processos as $processo) {
    $dataPrazo = new DateTime($processo['prazo']);
    $diferenca = $hoje->diff($dataPrazo)->days;

    if ($hoje <= $dataPrazo && $diferenca <= $diasPrazo) { 
        $alertas[] = "O processo nº {$processo['numero']} está com o prazo próximo: faltam {$diferenca} dias.";
    } elseif ($diferenca == 0) {
        $alertas[] = "<div class='alert alert-danger'>O processo nº {$processo['numero']} está vencido.</div>";
    } elseif ($hoje > $dataPrazo) {
        $alertas[] = "<div class='alert alert-danger'>O processo nº {$processo['numero']} está vencido.</div>";
    }
}

$clientesTotal = $pdo->query('
    SELECT DISTINCT c.* 
    FROM clientes c 
    LEFT JOIN processos p ON c.id = p.cliente_id
')->fetchAll();

$processosTotal = $pdo->query('SELECT * FROM processos WHERE status = "Em andamento"')->fetchAll();


$totalClientes = count($clientesTotal);
$totalProcessos = count($processosTotal);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> 
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
        .alert-warning {
            background-color: #fff3cd;
            border-left: 5px solid #ffec3d;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-left: 5px solid #f5c6cb;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-left: 5px solid #bee5eb;
        }
        .no-alerts {
            font-size: 1.2rem;
            text-align: center;
            color: #6c757d;
        }
        .bi {
            margin-right: 10px;
        }
        .statistics-card {
            background-color: #f0f8ff;
        }
        .statistics-card .card-header {
            background-color: #007bff;
            color: white;
        }
        .statistics-card .card-body {
            font-size: 1.2rem;
        }
        .statistics-card .btn {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Dashboard</h2>
    <div class="alert alert-info" role="alert">
        Bem-vindo, <?php echo htmlspecialchars($usuario['nome_completo']); ?>! Aqui você pode gerenciar seus clientes e processos.
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card statistics-card">
                <div class="card-header">
                    <h4 class="card-title">Estatísticas</h4>
                </div>
                <div class="card-body">
                    <p>Total de Clientes: <strong><?php echo $totalClientes; ?></strong></p>
                    <p>Total de Processos: <strong><?php echo $totalProcessos; ?></strong></p>
                    <a href="clientes.php" class="btn btn-primary">Ver Clientes</a>
                    <a href="processos.php" class="btn btn-secondary mt-2">Ver Processos</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lembretes de Prazos</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($alertas)): ?>
                        <p class="no-alerts">Nenhum prazo próximo.</p>
                    <?php else: ?>
                        <?php foreach ($alertas as $alerta): ?>
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.5rem;"></i>
                                <div>
                                    <?php echo htmlspecialchars($alerta); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
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
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Processos Em Andamento</h4>
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
