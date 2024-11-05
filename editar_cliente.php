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

if (!$cliente) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosCliente = [
        'cpf_cnpj' => $_POST['cpf_cnpj'],
        'nome' => $_POST['nome'],
        'rg_ie' => $_POST['rg_ie'] ?? null,
        'email' => $_POST['email'],
        'endereco' => $_POST['endereco'] ?? null,
        'conjugue' => $_POST['conjugue'] ?? null,
        'nome_mae' => $_POST['nome_mae'] ?? null,
        'data_nascimento' => $_POST['data_nascimento'] ?? null,
        'local_nascimento' => $_POST['local_nascimento'] ?? null,
        'pasep_pis' => $_POST['pasep_pis'] ?? null,
        'numero_beneficio' => $_POST['numero_beneficio'] ?? null,
    ];

    $stmt = $pdo->prepare('UPDATE clientes SET cpf_cnpj = ?, nome = ?, rg_ie = ?, email = ?, endereco = ?, conjugue = ?, nome_mae = ?, data_nascimento = ?, local_nascimento = ?, pasep_pis = ?, numero_beneficio = ? WHERE id = ?');
    $stmt->execute([
        $dadosCliente['cpf_cnpj'],
        $dadosCliente['nome'],
        $dadosCliente['rg_ie'],
        $dadosCliente['email'],
        $dadosCliente['endereco'],
        $dadosCliente['conjugue'],
        $dadosCliente['nome_mae'],
        $dadosCliente['data_nascimento'],
        $dadosCliente['local_nascimento'],
        $dadosCliente['pasep_pis'],
        $dadosCliente['numero_beneficio'],
        $clienteId
    ]);

    $_SESSION['mensagem'] = "Dados do cliente atualizados com sucesso.";
    header("Location: detalhes_cliente.php?id=" . urlencode($clienteId));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente - Sistema Advocacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h2>

    <form action="editar_cliente.php?id=<?php echo urlencode($clienteId); ?>" method="POST">
        <div class="mb-3">
            <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
            <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo htmlspecialchars($cliente['cpf_cnpj']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="rg_ie" class="form-label">RG/IE</label>
            <input type="text" class="form-control" id="rg_ie" name="rg_ie" value="<?php echo htmlspecialchars($cliente['rg_ie']); ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo htmlspecialchars($cliente['endereco']); ?>">
        </div>
        <div class="mb-3">
            <label for="conjugue" class="form-label">Cônjuge</label>
            <input type="text" class="form-control" id="conjugue" name="conjugue" value="<?php echo htmlspecialchars($cliente['conjugue']); ?>">
        </div>
        <div class="mb-3">
            <label for="nome_mae" class="form-label">Nome da Mãe</label>
            <input type="text" class="form-control" id="nome_mae" name="nome_mae" value="<?php echo htmlspecialchars($cliente['nome_mae']); ?>">
        </div>
        <div class="mb-3">
            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($cliente['data_nascimento']); ?>">
        </div>
        <div class="mb-3">
            <label for="local_nascimento" class="form-label">Local de Nascimento</label>
            <input type="text" class="form-control" id="local_nascimento" name="local_nascimento" value="<?php echo htmlspecialchars($cliente['local_nascimento']); ?>">
        </div>
        <div class="mb-3">
            <label for="pasep_pis" class="form-label">PASEP/PIS</label>
            <input type="text" class="form-control" id="pasep_pis" name="pasep_pis" value="<?php echo htmlspecialchars($cliente['pasep_pis']); ?>">
        </div>
        <div class="mb-3">
            <label for="numero_beneficio" class="form-label">Número do Benefício</label>
            <input type="text" class="form-control" id="numero_beneficio" name="numero_beneficio" value="<?php echo htmlspecialchars($cliente['numero_beneficio']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="detalhes_cliente.php?id=<?php echo urlencode($cliente['id']); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
