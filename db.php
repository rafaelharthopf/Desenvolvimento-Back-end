<?php
$host = 'localhost';
$dbname = 'sistema_advocacia';
$username = 'root';
$password = 'Bavarias1996';

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
        numero_beneficio VARCHAR(20)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS arquivos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT,
        nome VARCHAR(255) NOT NULL,
        caminho VARCHAR(255) NOT NULL,
        data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
        arquivo_comprimido BOOLEAN DEFAULT 0,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS configuracoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_sistema VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        telefone VARCHAR(20),
        endereco_sistema TEXT,
        dias_prazo INT
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

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}



// $host = 'localhost';
// $dbname = 'sistema_advocacia';
// $username = 'root';
// $password = '';

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Erro de conexão: " . $e->getMessage());
// }


?>
