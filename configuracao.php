<?php
session_start();
if (isset($_SESSION['user_id'])) {
    include 'header.php';
}
include 'db.php';

function criarEmpresa($pdo, $dadosEmpresa) {
    $stmt = $pdo->prepare('INSERT INTO configuracoes (nome_sistema, endereco_sistema) VALUES (?, ?)');
    if ($stmt->execute([
        $dadosEmpresa['nome_sistema'],
        $dadosEmpresa['endereco_sistema'], // Correção aqui para corresponder ao nome do campo no formulário
    ])) {
        return "Empresa cadastrada com sucesso!";
    } else {
        return 'Erro ao cadastrar a empresa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = criarEmpresa($pdo, $_POST);
}
?>
<body>
<div class="container">
    <div class="card p-4">
        <h2>Cadastrar Empresa</h2>
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= htmlspecialchars($mensagem) ?></div> <!-- Sanitizando a mensagem -->
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nome_empresa" class="form-label">Nome Empresa</label> <!-- Correção no id -->
                <input type="text" class="form-control" id="nome_sistema" name="nome_sistema" required>
            </div>
            <div class="mb-3">
                <label for="endereco_empresa" class="form-label">Endereço Empresa</label> <!-- Correção no id -->
                <input type="text" class="form-control" id="endereco_sistema" name="endereco_sistema" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
