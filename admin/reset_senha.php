<?php
// admin/reset_senha.php
require_once '../config.php';

$email = 'admin@nextalent.com';
$senhaNova = '123456';

// Gera o hash compatível com o SEU servidor
$hash = password_hash($senhaNova, PASSWORD_DEFAULT);

try {
    // 1. Apaga o usuário antigo para evitar duplicidade
    $pdo->prepare("DELETE FROM usuarios WHERE email = ?")->execute([$email]);

    // 2. Cria o usuário do zero com a senha certa
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute(['Admin Recrutador', $email, $hash]);

    echo "<h1>Sucesso!</h1>";
    echo "<p>Usuário: <strong>$email</strong></p>";
    echo "<p>Senha: <strong>$senhaNova</strong></p>";
    echo "<p>Hash gerado: $hash</p>";
    echo "<br><a href='login.php'>Ir para o Login</a>";

} catch (PDOException $e) {
    echo "Erro ao resetar senha: " . $e->getMessage();
}
?>