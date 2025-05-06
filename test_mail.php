<?php
// Verificar extensões necessárias
echo "Verificando extensões PHP necessárias:\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? 'OK' : 'FALTA') . "\n";
echo "Sockets: " . (extension_loaded('sockets') ? 'OK' : 'FALTA') . "\n";

// Verificar configurações do PHP
echo "\nVerificando configurações do PHP:\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'ON' : 'OFF') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";

// Verificar se o PHPMailer está instalado
echo "\nVerificando PHPMailer:\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "PHPMailer instalado: OK\n";
} else {
    echo "PHPMailer não encontrado\n";
}

// Testar conexão SMTP
echo "\nTestando conexão SMTP:\n";
$smtp = fsockopen('smtp.gmail.com', 587, $errno, $errstr, 30);
if ($smtp) {
    echo "Conexão SMTP bem-sucedida\n";
    fclose($smtp);
} else {
    echo "Erro na conexão SMTP: $errstr ($errno)\n";
} 