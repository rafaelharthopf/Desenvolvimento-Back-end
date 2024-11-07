<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
include 'header.php';

$hoje = new DateTime();
$hoje->setTime(0, 0, 0);
$processos = $pdo->query('SELECT * FROM processos WHERE prazo IS NOT NULL')->fetchAll();

$alertas = [];
foreach ($processos as $processo) {
    $dataPrazo = new DateTime($processo['prazo']);
    $diferenca = $hoje->diff($dataPrazo)->days;
    if ($hoje <= $dataPrazo && $diferenca <= 7) { 
        $alertas[] = "O processo nº {$processo['numero']} está com o prazo próximo: faltam {$diferenca} dias.";
    } elseif ($diferenca == 0) {
        $alertas[] = "<div class='alert alert-danger'>O processo nº {$processo['numero']} está vencido</div>";
    } elseif ($hoje > $dataPrazo) {
        $alertas[] = "<div class='alert alert-danger'>O processo nº {$processo['numero']} está vencido</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lembretes - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
        }
        h2 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .alert {
            font-size: 1.1rem;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-left: 5px solid #ffec3d;
        }
        .alert .bi {
            margin-right: 10px;
        }
        .alert-heading {
            font-weight: bold;
        }
        .no-alerts {
            font-size: 1.2rem;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Lembretes de Prazos</h2>
    
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>

</body>
</html>

<?php include 'footer.php'; ?>
