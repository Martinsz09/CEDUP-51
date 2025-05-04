<?php
session_start();
require_once '../config/database.php';

// Verificar se o admin está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Buscar estatísticas
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'subjects' => $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn(),
    'content' => $pdo->query("SELECT COUNT(*) FROM content")->fetchColumn()
];

// Buscar últimos conteúdos
$stmt = $pdo->query("
    SELECT c.*, s.name as subject_name, u.username 
    FROM content c 
    JOIN subjects s ON c.subject_id = s.id 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.created_at DESC 
    LIMIT 5
");
$recent_content = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Cedup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Cedup</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Painel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="subjects.php">Matérias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="content.php">Conteúdo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Usuários</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Olá, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Painel Administrativo</h1>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Usuários</h5>
                        <p class="card-text display-4"><?php echo $stats['users']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Matérias</h5>
                        <p class="card-text display-4"><?php echo $stats['subjects']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Conteúdo</h5>
                        <p class="card-text display-4"><?php echo $stats['content']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Últimos Conteúdos</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_content)): ?>
                    <div class="alert alert-info">
                        Nenhum conteúdo disponível.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Matéria</th>
                                    <th>Usuário</th>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_content as $content): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($content['title']); ?></td>
                                    <td><?php echo htmlspecialchars($content['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($content['username']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($content['date'])); ?></td>
                                    <td>
                                        <?php
                                        switch ($content['content_type']) {
                                            case 'image':
                                                echo 'Imagem';
                                                break;
                                            case 'pdf':
                                                echo 'PDF';
                                                break;
                                            case 'text':
                                                echo 'Texto';
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 