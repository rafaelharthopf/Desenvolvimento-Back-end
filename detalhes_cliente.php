<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

$stmt = $pdo->prepare('SELECT * FROM arquivos WHERE cliente_id = ?');
$stmt->execute([$clienteId]);
$arquivos = $stmt->fetchAll();

if (!$cliente) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['arquivo'])) {
    $clienteId = $_GET['id'];

    $uploadDir = 'uploads/';
    $nomeArquivo = $_FILES['arquivo']['name'];
    $caminhoArquivoTemp = $_FILES['arquivo']['tmp_name'];

    $conteudoArquivo = file_get_contents($caminhoArquivoTemp);

    $conteudoComprimido = gzcompress($conteudoArquivo);

    $dataUpload = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO arquivos (cliente_id, nome, caminho, data_upload, arquivo_comprimido) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$clienteId, $nomeArquivo, $uploadDir . $nomeArquivo, $dataUpload, $conteudoComprimido]);

    echo "Arquivo carregado e comprimido com sucesso!";
}

$stmt = $pdo->prepare('SELECT nome, caminho, arquivo_comprimido, data_upload FROM arquivos WHERE cliente_id = ?');
$stmt->execute([$clienteId]);
$arquivos = $stmt->fetchAll();

if ($arquivos) {
    foreach ($arquivos as $arquivo) {
        $conteudoDescomprimido = gzuncompress($arquivo['arquivo_comprimido']);

        $caminhoDescomprimido = 'uploads/' . $arquivo['nome'];
        file_put_contents($caminhoDescomprimido, $conteudoDescomprimido);
    }
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
    <h3>Upload de Arquivo</h3>
    <form action="detalhes_cliente.php?id=<?php echo $clienteId; ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="arquivo" class="form-label">Escolha um arquivo para enviar:</label>
            <input type="file" class="form-control" id="arquivo" name="arquivo" required>
        </div>
        <button type="submit" class="btn btn-primary">Carregar Arquivo</button>
    </form>
    <h2>Arquivos de <?= htmlspecialchars($cliente['nome'] ?? 'Cliente Desconhecido') ?></h2>
    <?php if (empty($arquivos)): ?>
    <p>Nenhum arquivo anexado.</p>
    <?php else: ?>
    <ul class="list-group">
        <?php foreach ($arquivos as $arquivo): ?>
            <li class="list-group-item">
                <a href="<?= $arquivo['caminho'] ?>" download="<?= $arquivo['nome'] ?>" class="btn btn-link"><?= htmlspecialchars($arquivo['nome']) ?></a>
                <br>
                <small>Data de Upload: <?= htmlspecialchars($arquivo['data_upload']) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
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
