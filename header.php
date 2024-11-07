<?php 
session_start();
include 'db.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$empresaId = 1;
if ($empresaId == 1) {
    $stmt = $pdo->prepare("SELECT nome_sistema FROM configuracoes WHERE id = ?");
    $stmt->execute([$empresaId]);
    $configuracao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($configuracao) {
        $nomeEmpresa = htmlspecialchars($configuracao['nome_sistema']);
    } else {
        $nomeEmpresa = 'Nome da Empresa Não Encontrado';
    }
} else {
    $nomeEmpresa = 'Sistema Advocacia';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo $nomeEmpresa; ?></title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #007bff;
            padding: 10px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin-right: 20px;
        }

        .navbar a:hover {
            color: #f0f0f0;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown a {
            cursor: pointer;
        }

        .dropdown-content {
            display: none; 
            position: absolute;
            background-color: #007bff;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            min-width: 200px;
            z-index: 1000;
            opacity: 0; 
            transition: opacity 0.3s ease;
        }

        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .dropdown-content a:hover {
            background-color: #0056b3;
        }

        .navbar-links {
            display: flex;
            align-items: center;
        }

        .icon-link {
            color: white;
            margin-left: 15px;
            cursor: pointer;
        }

        .icon-link:hover {
            color: #f0f0f0;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                margin-bottom: 10px;
            }

            .dropdown-content {
                position: relative;
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php"><?php echo $nomeEmpresa; ?></a>
            <div class="navbar-links">
                <div class="dropdown">
                    <a href="javascript:void(0)">Clientes</a>
                    <div class="dropdown-content">
                        <a href="clientes.php">Listar Clientes</a>
                        <a href="cadastrar_cliente.php">Cadastrar Cliente</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)">Usuários</a>
                    <div class="dropdown-content">
                        <a href="usuarios.php">Listar Usuários</a>
                        <a href="cadastrar_usuario.php">Cadastrar Usuário</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)">Processos</a>
                    <div class="dropdown-content">
                        <a href="clientes_andamento.php">Ver Processos Em Andamento</a>
                        <a href="clientes_concluido.php">Ver Processos Concluídos</a>
                        <a href="clientes_parados.php">Ver Processos Parados</a>
                        <a href="clientes_arquivados.php">Ver Processos Arquivados</a>
                        <a href="cadastrar_processo.php">Cadastrar Processo</a>
                    </div>
                </div>
                <a href="editar_configuracoes.php" class="icon-link"><i class="fas fa-cog"></i></a>
                <a href="logout.php" class="icon-link"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <script>
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function() {
                const menu = this.querySelector('.dropdown-content');
                const isVisible = menu.style.display === 'block';
                
                document.querySelectorAll('.dropdown-content').forEach(item => {
                    item.style.display = 'none';
                    item.style.opacity = '0'; 
                });

                if (!isVisible) {
                    menu.style.display = 'block';
                    setTimeout(() => {
                        menu.style.opacity = '1'; 
                    }, 10); 
                }
            });
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-content').forEach(item => {
                    item.style.display = 'none';
                    item.style.opacity = '0';  
                });
            }
        });
    </script>

</body>
</html>
