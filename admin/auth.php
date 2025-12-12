<?php
// admin/auth.php
session_start();
require_once '../config.php';

// Tratamento de Erros de Requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'] ?? '';

// 1. Validação de Campos Vazios
if (empty($email) || empty($senha)) {
    header("Location: login.php?erro=vazio");
    exit;
}

try {
    // 2. Busca Usuário
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verifica se usuário existe
    if (!$user) {
        header("Location: login.php?erro=inexistente");
        exit;
    }

    // 4. Verifica Senha
    if (password_verify($senha, $user['senha'])) {
        // Sucesso: Cria Sessão
        $_SESSION['recrutador_id'] = $user['id'];
        $_SESSION['recrutador_nome'] = $user['nome'];
        $_SESSION['recrutador_foto'] = $user['foto']; // Guarda a foto na sessão
        
        header("Location: painel.php");
        exit;
    } else {
        // Senha Errada
        header("Location: login.php?erro=senha");
        exit;
    }

} catch (PDOException $e) {
    // Erro de Banco de Dados
    error_log("Erro Login: " . $e->getMessage()); // Loga no servidor
    header("Location: login.php?erro=sistema");
    exit;
}
?>