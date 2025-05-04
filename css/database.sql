-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS cedup;
USE cedup;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar tabela de administradores
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar tabela de matérias
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar tabela de conteúdo
CREATE TABLE IF NOT EXISTS content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    content_type ENUM('image', 'pdf', 'text') NOT NULL,
    file_path VARCHAR(255),
    content_text TEXT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Criação da tabela de reclamações
CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'resolved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Criação da tabela de tokens de recuperação de senha
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Inserir administrador padrão
INSERT INTO admins (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserir matérias de exemplo
INSERT INTO subjects (name, description) VALUES
('Matemática', 'Estudo dos números, quantidades e formas'),
('Português', 'Estudo da língua portuguesa e suas regras'),
('História', 'Estudo dos eventos passados'),
('Geografia', 'Estudo da Terra e seus fenômenos'),
('Física', 'Estudo da matéria e energia'),
('Química', 'Estudo das substâncias e suas transformações'),
('Biologia', 'Estudo dos seres vivos'),
('Filosofia', 'Estudo dos problemas fundamentais relacionados à existência'),
('Lógica da Programação', 'Estudo dos princípios básicos da programação'),
('Inglês Aplicado', 'Estudo da língua inglesa com foco em aplicações práticas'),
('Banco de Dados', 'Estudo dos sistemas de armazenamento e gerenciamento de dados'),
('Montagem e Manutenção', 'Estudo da montagem e manutenção de computadores'),
('Sociologia', 'Estudo da sociedade e suas relações'),
('Introdução à Computação', 'Estudo dos conceitos básicos da computação'),
('Artes', 'Estudo das expressões artísticas e culturais'),
('Inglês', 'Estudo da língua inglesa e suas aplicações'),
('Educação Física', 'Estudo das atividades físicas e esportivas'); 