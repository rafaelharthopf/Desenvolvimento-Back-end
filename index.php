<?php
session_start();
include 'db.php';

$empresaId = 1;
if ($empresaId == 1) {
    $stmt = $pdo->prepare("SELECT * FROM configuracoes WHERE id = ?");
    $stmt->execute([$empresaId]);
    $configuracao = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($userId) {
        $stmt = $pdo->prepare('SELECT nome_completo FROM usuarios WHERE id = ?');
        $stmt->execute([$userId]);
        $usuario = $stmt->fetch();
    } else {
        $usuario = null;
    }

    if ($configuracao) {
        $nomeEmpresa = htmlspecialchars($configuracao['nome_sistema']);
        $enderecoEmpresa = htmlspecialchars($configuracao['endereco_sistema']);
        $emailEmpresa = htmlspecialchars($configuracao['email']);
        $telefoneEmpresa = htmlspecialchars($configuracao['telefone']);
        $logoEmpresa = isset($configuracao['logo']) ? htmlspecialchars($configuracao['logo']) : '/uploads/logos/logo.jpg';
    } else {
        $nomeEmpresa = 'Nome da Empresa Não Encontrado';
        $enderecoEmpresa = 'Endereço da Empresa Não Encontrado';
        $emailEmpresa = 'Email da Empresa Não Encontrado';
        $telefoneEmpresa = 'Telefone da Empresa Não Encontrado';
        $logoEmpresa = '/uploads/logos/logo.jpg';
    }
} else {
    $nomeEmpresa = 'Sistema Advocacia';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo à <?php echo $nomeEmpresa; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Ícones -->
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 3rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 1.5rem;
            color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .empresa-logo {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .empresa-descricao {
            font-size: 1.1em;
            color: #555;
            margin-top: 20px;
        }
        .empresa-info {
            margin-top: 20px;
        }
        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 1rem 0;
        }
        footer .text-center {
            color: #6c757d;
        }
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mt-4 mb-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Olá, <?php echo htmlspecialchars($usuario['nome_completo']); ?>!</p>
                <a href="dashboard.php" class="btn btn-primary">Ir para o Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Entrar no Sistema</a>
            <?php endif; ?>
        </div>

        <h2 class="text-center">Bem-vindo à <?php echo $nomeEmpresa; ?></h2>

        <div class="text-center">
            <img src="uploads/logos/logo.jpg<?php echo $logoEmpresa; ?>" alt="Logo da Empresa" class="empresa-logo">
        </div>

        <div class="text-center mb-4">
            <a href="about.php" class="btn btn-info"><i class="fas fa-info-circle"></i> Sobre a Empresa</a>
        </div>

        <div class="empresa-descricao">
            <p>A <?php echo $nomeEmpresa; ?> é uma empresa consolidada no mercado jurídico, atuando com excelência há mais de 10 anos no estado de São Paulo. A empresa foi fundada com a missão de oferecer soluções jurídicas personalizadas para cada cliente, sempre com base na ética, compromisso e transparência. Com experiência em diversas áreas do direito, a <?php echo $nomeEmpresa; ?> se destaca no atendimento a pessoas físicas e jurídicas, buscando sempre a melhor solução para cada situação.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
