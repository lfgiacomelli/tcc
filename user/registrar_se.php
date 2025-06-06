<?php
session_start();

$erro_email = isset($_SESSION['erro_email']) ? $_SESSION['erro_email'] : '';
unset($_SESSION['erro_email']); 
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX - Registrar</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/registrar_se.css">
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <h1>ZoomX</h1>
            <p>Crie sua conta gratuita</p>
        </div>

        <form class="register-form" action="../actions/actionregistrar_se.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-control" required placeholder="Digite seu nome" value="<?php echo isset($_SESSION['form_data']['nome']) ? htmlspecialchars($_SESSION['form_data']['nome']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Digite seu email" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <div class="password-container">
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Insira sua Senha" autocomplete="new-password" required>

                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-control" required placeholder="(00) 00000-0000" value="<?php echo isset($_SESSION['form_data']['telefone']) ? htmlspecialchars($_SESSION['form_data']['telefone']) : ''; ?>">
            </div>

            <input type="hidden" name="acao" value="adicionar">
            <input type="hidden" name="ativo" value="true">
            <input type="hidden" name="created_at" value="<?php echo date('Y-m-d H:i:s'); ?>">
            <input type="hidden" name="updated_at" value="<?php echo date('Y-m-d H:i:s'); ?>">

            <button type="submit" class="btn">Registrar-se</button>
        </form>

        <div class="register-footer">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#senha');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            if (value.length > 2) {
                value = `(${value.substring(0,2)}) ${value.substring(2)}`;
            }
            if (value.length > 10) {
                value = `${value.substring(0,10)}-${value.substring(10)}`;
            }

            e.target.value = value;
        });

        <?php if (!empty($erro_email)): ?>
            Swal.fire({
                title: 'E-mail já cadastrado',
                html: `<?php echo $erro_email; ?><br><br>
                      <a href="login.php" style="color: #721c24; font-weight: bold; text-decoration: underline;">
                          Clique aqui para fazer login
                      </a>`,
                icon: 'error',
                confirmButtonColor: '#000',
                confirmButtonText: 'Entendi',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'righteous-font'
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>
<?php
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
