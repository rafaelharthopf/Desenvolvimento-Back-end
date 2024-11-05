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

$processoId = $_GET['id'];
$processo = $pdo->prepare('SELECT * FROM processos WHERE id = ?');
$processo->execute([$processoId]);
$processo = $processo->fetch();

if (!$processo) {
    header('Location: dashboard.php');
    exit;
}

$cliente = $pdo->prepare('SELECT * FROM clientes WHERE id = ?');
$cliente->execute([$processo['cliente_id']]);
$cliente = $cliente->fetch();

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Processo - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa; /* Cor de fundo suave */
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        h2 {
            color: #007bff; /* Cor do título */
            margin-bottom: 30px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px; /* Bordas arredondadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave */
        }
        .btn-primary {
            background-color: #007bff; /* Azul padrão */
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Azul escuro ao passar o mouse */
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
    <h2>Detalhes do Processo: <?php echo htmlspecialchars($processo['numero']); ?></h2>
    
    <?php if (isset($mensagem)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card p-4">
        <h4>Cliente</h4>
        <p>
            <strong>Nome:</strong> <?php echo htmlspecialchars($cliente['nome']); ?><br>
            <strong>CPF/CNPJ:</strong> <?php echo htmlspecialchars($cliente['cpf_cnpj']); ?>
        </p>
    </div>

    <div class="card p-4">
        <h4>Informações do Processo</h4>
        <ul class="list-unstyled">
            <li><strong>Tipo:</strong> <?php echo htmlspecialchars($processo['tipo']); ?></li>
            <li><strong>Status:</strong> <?php echo htmlspecialchars($processo['status']); ?></li>
            <li><strong>Autor:</strong> <?php echo htmlspecialchars($processo['autor']); ?></li>
            <li><strong>Réu:</strong> <?php echo htmlspecialchars($processo['reu']); ?></li>
            <li><strong>Data de Abertura:</strong> <?php echo htmlspecialchars($processo['data_abertura']); ?></li>
            <li><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($processo['descricao'])); ?></li>
            <li><strong>Vara:</strong> <?php echo htmlspecialchars($processo['vara']); ?></li>
            <li><strong>Prazo:</strong> <?php echo htmlspecialchars($processo['prazo']); ?></li>
        </ul>
    </div>

    <form action="atualizar_status.php" method="POST" class="mt-3">
        <input type="hidden" name="processo_id" value="<?php echo htmlspecialchars($processoId); ?>">
        <h4>Atualizar Status</h4>
        <select class="form-select mb-2" name="status">
            <option value="Em Andamento" <?php echo $processo['status'] === 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
            <option value="Concluído" <?php echo $processo['status'] === 'Concluído' ? 'selected' : ''; ?>>Concluído</option>
            <option value="Parado" <?php echo $processo['status'] === 'Parado' ? 'selected' : ''; ?>>Parado</option>
            <option value="Arquivado" <?php echo $processo['status'] === 'Arquivado' ? 'selected' : ''; ?>>Arquivado</option>
        </select>
        <button class="btn btn-primary" type="submit">Atualizar Status</button>
    </form>

    <div class="mt-3">
        <a href="detalhes_cliente.php?id=<?php echo urlencode($cliente['id']); ?>" class="btn btn-secondary">Voltar aos Detalhes do Cliente</a>
        <a href="dashboard.php" class="btn btn-secondary">Voltar ao Dashboard</a>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
