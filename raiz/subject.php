<?php
session_start();
require_once 'config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar se o ID da matéria foi fornecido
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$subject_id = $_GET['id'];

// Buscar informações da matéria
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$subject_id]);
$subject = $stmt->fetch();

if (!$subject) {
    header('Location: index.php');
    exit;
}

// Buscar conteúdo da matéria
$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM content c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.subject_id = ? 
    ORDER BY c.date DESC, c.created_at DESC
");
$stmt->execute([$subject_id]);
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subject['name']); ?> - Cedup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Cedup</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="upload.php">Compartilhar Conteúdo</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Olá, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo htmlspecialchars($subject['name']); ?></h1>
            <a href="upload.php?subject_id=<?php echo $subject_id; ?>" class="btn btn-primary">Compartilhar Conteúdo</a>
        </div>

        <p class="lead mb-4"><?php echo htmlspecialchars($subject['description']); ?></p>

        <?php if (empty($contents)): ?>
            <div class="alert alert-info">
                Nenhum conteúdo disponível para esta matéria.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($contents as $content): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($content['title']); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    Data: <?php echo date('d/m/Y', strtotime($content['date'])); ?> | 
                                    Compartilhado por: <?php echo htmlspecialchars($content['username']); ?>
                                </small>
                            </p>

                            <?php if ($content['content_type'] === 'image'): ?>
                                <img src="<?php echo htmlspecialchars($content['file_path']); ?>" class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($content['title']); ?>">
                            <?php elseif ($content['content_type'] === 'pdf'): ?>
                                <div class="text-center">
                                    <a href="<?php echo htmlspecialchars($content['file_path']); ?>" class="btn btn-primary" target="_blank">
                                        <i class="bi bi-file-pdf"></i> Abrir PDF
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="content-text">
                                    <?php echo nl2br(htmlspecialchars($content['content_text'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 