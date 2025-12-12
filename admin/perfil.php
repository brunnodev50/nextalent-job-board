<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['recrutador_id'])) { header("Location: login.php"); exit; }

$msg = '';
$tipoMsg = '';

// Processamento do Formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $novaSenha = $_POST['nova_senha'];
    $id = $_SESSION['recrutador_id'];

    try {
        // 1. Lógica de Upload de Foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($ext, $permitidos)) {
                // Configura diretório
                $dir = '../foto-perfil/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                // Nome aleatório
                $novoNome = bin2hex(random_bytes(16)) . '.' . $ext;
                $caminhoFinal = $dir . $novoNome;
                $caminhoBanco = 'foto-perfil/' . $novoNome; // Salva relativo

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoFinal)) {
                    // Atualiza foto no banco
                    $stmtFoto = $pdo->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
                    $stmtFoto->execute([$caminhoBanco, $id]);
                    
                    // Atualiza Sessão
                    $_SESSION['recrutador_foto'] = $caminhoBanco;
                }
            } else {
                $msg = "Formato de imagem inválido (Use JPG, PNG ou WEBP).";
                $tipoMsg = "error";
            }
        }

        // 2. Atualização de Dados Pessoais
        if (!empty($novaSenha)) {
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $id]);
        }

        $_SESSION['recrutador_nome'] = $nome;
        
        if (!$msg) {
            $msg = "Perfil atualizado com sucesso!";
            $tipoMsg = "success";
        }

    } catch (PDOException $e) {
        $msg = "Erro ao salvar: " . $e->getMessage();
        $tipoMsg = "error";
    }
}

// Busca dados atualizados
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['recrutador_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$fotoPerfil = $user['foto'] ? '../' . $user['foto'] : 'https://ui-avatars.com/api/?name='.urlencode($user['nome']).'&background=random';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | NexTalent</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background: #F8FAFC; padding: 2rem 1rem; }
        .profile-container { max-width: 600px; margin: 0 auto; }
        
        .profile-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border: 1px solid #E2E8F0;
        }

        /* Área da Foto */
        .avatar-upload {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }
        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .avatar-edit {
            position: absolute;
            right: 0;
            bottom: 0;
            background: #2563EB;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: 0.2s;
            border: 2px solid white;
        }
        .avatar-edit:hover { background: #1E40AF; transform: scale(1.1); }

        .msg { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; }
        .msg.success { background: #DCFCE7; color: #166534; }
        .msg.error { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
                <h2>Editar Perfil</h2>
                <a href="painel.php" class="btn btn--outline">Voltar</a>
            </div>

            <?php if($msg): ?>
                <div class="msg <?php echo $tipoMsg; ?>"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="avatar-upload">
                    <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" class="avatar-preview" id="preview">
                    <label for="fotoUpload" class="avatar-edit">
                        <i class="ph ph-camera"></i>
                    </label>
                    <input type='file' id="fotoUpload" name="foto" accept=".png, .jpg, .jpeg" style="display: none;" onchange="previewImage(this)">
                </div>

                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <hr style="border:0; border-top:1px solid #E2E8F0; margin: 2rem 0;">
                
                <h4 style="margin-bottom: 1rem; color: #64748B;">Segurança</h4>
                <div class="form-group">
                    <label>Nova Senha (Opcional)</label>
                    <input type="password" name="nova_senha" placeholder="Deixe vazio para manter a atual">
                </div>

                <button type="submit" class="btn btn--primary btn--full" style="margin-top: 1rem;">
                    Salvar Alterações
                </button>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>