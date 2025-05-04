# Cedup - Sistema de Compartilhamento de Conteúdo

Sistema web para compartilhamento de conteúdo escolar entre alunos, desenvolvido com PHP e MySQL.

## Funcionalidades

- Compartilhamento de conteúdo (imagens, PDFs, texto)
- Visualização de conteúdo por matéria
- Sistema de login para alunos e administradores
- Painel administrativo para gerenciamento
- Interface moderna e responsiva

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP necessárias:
  - PDO
  - PDO_MySQL
  - GD (para processamento de imagens)

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/cedup.git
cd cedup
```

2. Importe o banco de dados:
- Acesse o phpMyAdmin
- Crie um novo banco de dados chamado `cedup`
- Importe o arquivo `database.sql`

3. Configure a conexão com o banco de dados:
- Edite o arquivo `config/database.php`
- Atualize as credenciais do banco de dados:
```php
$host = 'localhost';
$dbname = 'cedup';
$username = 'seu_usuario';
$password = 'sua_senha';
```

4. Configure as permissões:
```bash
chmod 777 uploads/
```

## Estrutura do Projeto

```
cedup/
├── admin/
│   ├── dashboard.php
│   ├── login.php
│   └── logout.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── img/
│       └── cedup_login.jpg
├── config/
│   └── database.php
├── uploads/
├── index.php
├── login.php
├── register.php
├── subject.php
├── upload.php
├── logout.php
└── database.sql
```

## Uso

1. Acesse o sistema através do navegador:
```
http://localhost/cedup
```

2. Faça login como aluno:
- Usuário: seu_email
- Senha: sua_senha

3. Faça login como administrador:
- Usuário: admin
- Senha: admin123

## Segurança

- Senhas armazenadas com hash
- Validação de entrada de dados
- Proteção contra SQL Injection
- Validação de tipos de arquivo
- Limite de tamanho de arquivo

## Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para detalhes. 