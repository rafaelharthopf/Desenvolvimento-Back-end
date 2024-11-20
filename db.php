<?php
$host = 'localhost';
$dbname = 'sistema_advocacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cpf_cnpj VARCHAR(20) NOT NULL,
        nome VARCHAR(255) NOT NULL,
        rg_ie VARCHAR(20),
        email VARCHAR(255),
        endereco TEXT,
        conjugue VARCHAR(255),
        nome_mae VARCHAR(255),
        data_nascimento DATE,
        local_nascimento VARCHAR(255),
        pasep_pis VARCHAR(20),
        numero_beneficio VARCHAR(20),
        foto_cliente VARCHAR(255) DEFAULT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS arquivos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT,
        nome VARCHAR(255) NOT NULL,
        caminho VARCHAR(255) NOT NULL,
        data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
        arquivo_comprimido LONGBLOB,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");


    $pdo->exec("CREATE TABLE IF NOT EXISTS configuracoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_sistema VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        telefone VARCHAR(20),
        endereco_sistema TEXT,
        dias_prazo INT DEFAULT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS processos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT,
        tipo VARCHAR(50),
        numero VARCHAR(50) NOT NULL,
        autor VARCHAR(255),
        reu VARCHAR(255),
        data_abertura DATE,
        descricao TEXT,
        status VARCHAR(50),
        advogado_responsavel VARCHAR(255),
        vara VARCHAR(255),
        prazo DATE,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        cargo VARCHAR(100),
        nome_completo VARCHAR(255) NOT NULL,
        cpf_cnpj VARCHAR(20),
        numero_oab VARCHAR(20)
    )");

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM configuracoes");
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if ($exists == 0) {
        $pdo->exec("INSERT INTO configuracoes (nome_sistema, email, telefone, endereco_sistema, dias_prazo) 
                    VALUES (
                        'Zola & Klébis Sociedade de Advogados', 
                        'zkadvogados@gmail.com', 
                        '1832232706', 
                        'R. Ângelo Rotta, 137 - Jardim Petropolis, Pres. Prudente - SP, 19060-420',
                        NULL
                    )");
    }

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}


// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Erro de conexão: " . $e->getMessage());
// }


?>
