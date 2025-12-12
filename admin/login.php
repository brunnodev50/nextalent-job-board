<?php
session_start();
if (isset($_SESSION['recrutador_id'])) { header("Location: painel.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | NexTalent</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background: #F1F5F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .logo-area { margin-bottom: 2rem; }
        .logo-area h1 { font-size: 1.75rem; color: #0F172A; font-weight: 800; letter-spacing: -0.03em; }
        .logo-area span { color: #2563EB; }
        .logo-area p { color: #64748B; font-size: 0.9rem; margin-top: 0.5rem; }

        /* Inputs com Ícones */
        .input-wrapper { position: relative; margin-bottom: 1rem; text-align: left; }
        .input-wrapper i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 1.2rem; transition: 0.2s; }
        .input-wrapper input { 
            width: 100%; 
            padding: 0.9rem 1rem 0.9rem 2.8rem; 
            border: 1px solid #E2E8F0; 
            border-radius: 8px; 
            font-size: 0.95rem; 
            transition: 0.2s;
            outline: none;
        }
        .input-wrapper input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .input-wrapper input:focus + i { color: #2563EB; }

        /* Mensagens de Erro */
        .alert {
            padding: 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-align: left;
        }
        .alert-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .alert i { font-size: 1.2rem; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-area">
            <h1>Nex<span>Talent</span></h1>
            <p>Faça login para gerenciar vagas e candidatos.</p>
        </div>

        <?php if(isset($_GET['erro'])): ?>
            <div class="alert alert-error">
                <i class="ph ph-warning-circle"></i>
                <div>
                    <?php 
                        $erro = $_GET['erro'];
                        if ($erro == 'vazio') echo "Por favor, preencha todos os campos.";
                        elseif ($erro == 'inexistente') echo "E-mail não encontrado no sistema.";
                        elseif ($erro == 'senha') echo "Senha incorreta. Tente novamente.";
                        elseif ($erro == 'sistema') echo "Erro no servidor. Contate o suporte.";
                        else echo "Erro desconhecido.";
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <div class="input-wrapper">
                <input type="email" name="email" placeholder="E-mail corporativo" required>
                <i class="ph ph-envelope-simple"></i>
            </div>
            
            <div class="input-wrapper">
                <input type="password" name="senha" placeholder="Sua senha" required>
                <i class="ph ph-lock-key"></i>
            </div>

            <button type="submit" class="btn btn--primary btn--full" style="padding: 1rem; font-size: 1rem; margin-top: 1rem;">
                Acessar Painel <i class="ph ph-arrow-right"></i>
            </button>
        </form>
        
        <p style="margin-top: 2rem; font-size: 0.8rem; color: #94A3B8;">
            &copy; <?php echo date('Y'); ?> NexTalent System
        </p>
    </div>

</body>
</html>