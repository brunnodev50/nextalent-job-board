<?php
// config.php
// Configurações de conexão com o banco de dados

$host = 'localhost';
$dbname = 'nextalent';
$user = 'root';   // Usuário padrão do XAMPP/WAMP
$pass = '';       // Senha padrão é vazia no XAMPP

try {
    // Cria a conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configura o PDO para lançar exceções em caso de erro (ajuda no debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Opcional: Define o modo de fetch padrão para array associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Se der erro, para tudo e mostra a mensagem
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
