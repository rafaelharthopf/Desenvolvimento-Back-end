<?php
session_start();
include 'db.php'; 
if (isset($_SESSION['user_id'])) {
    include 'header.php'; 
}

$empresaId = 1;
if ($empresaId == 1) {
    $stmt = $pdo->prepare("SELECT * FROM configuracoes WHERE id = ?");
    $stmt->execute([$empresaId]);
    $configuracao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($configuracao) {
        $nomeEmpresa = htmlspecialchars($configuracao['nome_sistema']);
        $enderecoEmpresa = htmlspecialchars($configuracao['endereco_sistema']);
        $emailEmpresa = htmlspecialchars($configuracao['email']);
        $telefoneEmpresa = htmlspecialchars($configuracao['telefone']);
    } else {
        $nomeEmpresa = 'Nome da Empresa Não Encontrado';
        $enderecoEmpresa = 'Endereço da Empresa Não Encontrado';
        $emailEmpresa = 'Email da Empresa Não Encontrado';
        $telefoneEmpresa = 'Telefone da Empresa Não Encontrado';
    }
} else {
    $nomeEmpresa = 'Sistema Advocacia';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sobre a Empresa - <?php echo $nomeEmpresa; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.6/inputmask.min.js"></script>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            border-radius: 10px;
        }
        .info {
            margin-top: 20px;
        }
        .info b {
            color: #007bff;
        }
        .empresa-descricao {
            margin-top: 30px;
            font-size: 1.1em;
            color: #555;
        }
        .empresa-valor {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card p-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn btn-primary btn-sm d-flex align-items-center w-auto">
                <i class="fas fa-tachometer-alt me-2"></i> Ir para o Dashboard
            </a>
        <?php else: ?>
            <a href="index.php" class="btn btn-secondary btn-sm d-flex align-items-center w-auto">
                <i class="fas fa-home me-2"></i> Página Inicial
            </a>
        <?php endif; ?>
            <h2>Sobre a Empresa</h2>
            <h4>Quadro de Sócios</h4>
            <p><b>Deborah Rocha Rodrigues Zola</b> - Sócio-Administrador</p>
            <p><b>Fabricio de Oliveira Klebis</b> - Sócio-Administrador</p>
            <p><b>Joao Emilio Zola Junior</b> - Sócio-Administrador</p>

            <h4>Sobre a Empresa</h4>
            <p class="empresa-descricao">
                A Zola e Klebis Sociedade de Advogados é uma empresa consolidada no mercado jurídico, atuando com excelência há mais de 10 anos no estado de São Paulo. A empresa foi fundada com a missão de oferecer soluções jurídicas personalizadas para cada cliente, sempre com base na ética, compromisso e transparência. Com experiência em diversas áreas do direito, a Zola e Klebis se destaca no atendimento a pessoas físicas e jurídicas, buscando sempre a melhor solução para cada situação.
            </p>

            <p class="empresa-valor">
                Nossa missão é garantir que cada cliente tenha sua situação jurídica resolvida com a máxima eficiência e respeito, sempre com um atendimento humanizado e transparente.
            </p>
            <p class="empresa-descricao">
                O escritório é composto por sócios altamente capacitados e com mais de 20 anos de experiência na advocacia. Buscamos a excelência em cada serviço prestado, e nossa dedicação ao cliente é a base de nossa atuação. A Zola e Klebis é referência em compromisso com a qualidade e integridade.
            </p>
            <p class="empresa-descricao">
                Nosso objetivo é ser o escritório de confiança de nossos clientes, oferecendo serviços jurídicos de alta qualidade e soluções criativas para problemas complexos.
            </p>
            <hr>
            <h4>Contatos</h4>
            <p><b>E-mail:</b> <a href="mailto:<?php echo $emailEmpresa ?>"><?php echo $emailEmpresa ?></a></p>
            <p><b>Telefone:</b> <span id="telefone"><?php echo $telefoneEmpresa ?></span></p>
            <hr>
            <h4>Localização</h4>
            <p id="telefone"><b>Endereço:</b> <?php echo $enderecoEmpresa; ?></p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var telefoneElement = document.getElementById("telefone");
            var telefoneValue = telefoneElement.innerText || telefoneElement.textContent;

            var im = new Inputmask("(99) 9999-9999", {
                placeholder: "(__) ____-____"
            });

            telefoneElement.innerHTML = im.format(telefoneValue);
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>
