<?php
// forgot_password.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Verificar se o email existe no banco de dados
    // (Aqui você faria a conexão com seu banco de dados)
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Gerar token único
        $token = bin2hex(random_bytes(50));
        $expira = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        // Salvar token no banco de dados
        $stmt = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE id = ?");
        $stmt->execute([$token, $expira, $user['id']]);
        
        // Enviar email com o link
        $reset_link = "https://seusite.com/reset_password.php?token=$token";
        $mensagem = "Clique no link para redefinir sua senha: $reset_link";
        mail($email, "Redefinição de Senha", $mensagem);
        
        $_SESSION['mensagem'] = "Enviamos um link para redefinir sua senha para o email informado.";
        header('Location: login.php');
        exit;
    } else {
        $erro = "Email não encontrado em nosso sistema.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Esqueci minha senha</title>
</head>
<body>
    <h2>Recuperar Senha</h2>
    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Enviar Link</button>
    </form>
    <p><a href="login.php">Voltar para login</a></p>
</body>
</html>
// Configurações do e-mail
$to = $email;
$subject = "Redefinição de Senha - SeuSite";
$reset_link = "https://seusite.com/reset_password.php?token=$token";

// Corpo do e-mail (pode ser HTML ou texto simples)
$message = "
<html>
