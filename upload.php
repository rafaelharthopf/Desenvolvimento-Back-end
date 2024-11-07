<?php
session_start();
include 'db.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $clienteId = $_POST['cliente_id'];
    $arquivoTmp = $_FILES['arquivo']['tmp_name'];
    $nomeArquivo = uniqid() . '-' . $_FILES['arquivo']['name'];
    $diretorioDestino = 'uploads/';
    
    if (move_uploaded_file($arquivoTmp, $diretorioDestino . $nomeArquivo)) {
        $stmt = $pdo->prepare("INSERT INTO arquivos (cliente_id, nome_arquivo, caminho_arquivo) VALUES (?, ?, ?)");
        $stmt->execute([$clienteId, $nomeArquivo, $diretorioDestino . $nomeArquivo]);
        echo "<div class='alert alert-success'>Arquivo enviado com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Falha no upload do arquivo.</div>";
    }
}

$clienteId = $_GET['id'] ?? null;
if ($clienteId) {
    $stmt = $pdo->prepare('SELECT nome FROM clientes WHERE id = ?');
    $stmt->execute([$clienteId]);
    $cliente = $stmt->fetch();
} else {
    echo "<div class='alert alert-warning'>Cliente não encontrado.</div>";
}

?>

<h2>Upload de Arquivo para: <?= htmlspecialchars($cliente['nome'] ?? 'Cliente Desconhecido') ?></h2>

<form action="upload.php?id=<?= $clienteId ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="cliente_id" value="<?= $clienteId ?>">
    <div class="mb-3">
        <label for="arquivo" class="form-label">Escolha um arquivo</label>
        <input type="file" name="arquivo" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
</form>

<?php include 'footer.php'; ?>
