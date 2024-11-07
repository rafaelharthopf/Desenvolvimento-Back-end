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
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Dashboard</h2>
    <div class="alert alert-info" role="alert">
        Bem-vindo, <?php echo htmlspecialchars($usuario['nome_completo']); ?>! Aqui você pode gerenciar seus clientes e processos.
    </div>
    <div class="row">
        <div class="col-12">
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
