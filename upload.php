<?php
session_start();
require_once 'config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Buscar todas as matérias
$stmt = $pdo->query("SELECT * FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $content_type = $_POST['content_type'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $content_text = $_POST['content_text'] ?? '';
    $file = $_FILES['file'] ?? null;

    if (empty($subject_id) || empty($title) || empty($content_type)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        try {
            $pdo->beginTransaction();

            // Inserir o conteúdo no banco de dados
            $stmt = $pdo->prepare("INSERT INTO content (subject_id, user_id, title, content_type, date, content_text) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$subject_id, $_SESSION['user_id'], $title, $content_type, $date, $content_text]);
            $content_id = $pdo->lastInsertId();

            // Se for upload de arquivo
            if ($content_type === 'image' || $content_type === 'pdf') {
                if ($file && $file['error'] === UPLOAD_ERR_OK) {
                    $allowed_types = $content_type === 'image' ? ['image/jpeg', 'image/png', 'image/gif'] : ['application/pdf'];
                    
                    if (!in_array($file['type'], $allowed_types)) {
                        throw new Exception('Tipo de arquivo não permitido.');
                    }

                    $max_size = 5 * 1024 * 1024; // 5MB
                    if ($file['size'] > $max_size) {
                        throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB');
                    }

                    $upload_dir = 'uploads/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $file_name = $content_id . '_' . time() . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;

                    if (move_uploaded_file($file['tmp_name'], $file_path)) {
                        $stmt = $pdo->prepare("UPDATE content SET file_path = ? WHERE id = ?");
                        $stmt->execute([$file_path, $content_id]);
                    } else {
                        throw new Exception('Erro ao fazer upload do arquivo.');
                    }
                } else {
                    throw new Exception('Erro no upload do arquivo.');
                }
            }

            $pdo->commit();
            $success = 'Conteúdo compartilhado com sucesso!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Conteúdo - Cedup</title>
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
                        <a class="nav-link active" href="upload.php">Compartilhar Conteúdo</a>
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
        <h1 class="text-center mb-4">Compartilhar Conteúdo</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="subject_id" class="form-label">Matéria</label>
                                <select class="form-select" id="subject_id" name="subject_id" required>
                                    <option value="">Selecione uma matéria</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Data</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="content_type" class="form-label">Tipo de Conteúdo</label>
                                <select class="form-select" id="content_type" name="content_type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="image">Imagem</option>
                                    <option value="pdf">PDF</option>
                                    <option value="text">Texto</option>
                                </select>
                            </div>

                            <div id="file_upload" class="mb-3" style="display: none;">
                                <label for="file" class="form-label">Arquivo</label>
                                <input type="file" class="form-control" id="file" name="file">
                            </div>

                            <div id="text_content" class="mb-3" style="display: none;">
                                <label for="content_text" class="form-label">Conteúdo</label>
                                <textarea class="form-control" id="content_text" name="content_text" rows="5"></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Compartilhar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('content_type').addEventListener('change', function() {
            const fileUpload = document.getElementById('file_upload');
            const textContent = document.getElementById('text_content');
            
            if (this.value === 'image' || this.value === 'pdf') {
                fileUpload.style.display = 'block';
                textContent.style.display = 'none';
            } else if (this.value === 'text') {
                fileUpload.style.display = 'none';
                textContent.style.display = 'block';
            } else {
                fileUpload.style.display = 'none';
                textContent.style.display = 'none';
            }
        });
    </script>
</body>
</html> 