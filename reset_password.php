<?php
// reset_password.php
session_start();

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['mensagem'] = "Token inválido.";
    header('Location: login.php');
    exit;
}

// Verificar token no banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
$stmt = $pdo->prepare("SELECT id, reset_expira FROM usuarios WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user || strtotime($user['reset_expira']) < time()) {
    $_SESSION['mensagem'] = "Token inválido ou expirado.";
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];
    
    if ($nova_senha !== $confirma_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        // Atualizar senha e limpar token
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?");
        $stmt->execute([$senha_hash, $user['id']]);
        
        $_SESSION['mensagem'] = "Senha redefinida com sucesso!";
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Redefinir Senha</title>
</head>
<body>
    <h2>Redefinir Senha</h2>
    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    <form method="POST">
        <label>Nova Senha:</label>
        <input type="password" name="nova_senha" required>
        <label>Confirmar Nova Senha:</label>
        <input type="password" name="confirma_senha" required>
        <button type="submit">Redefinir Senha</button>
    </form>
</body>
</html>