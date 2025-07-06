<?php
// Credenciais de conexão
$host = "localhost";
$dbname = "loja_sk8";
$user = "root";
$senha = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $senha);
    // Habilita o modo de erros para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em produção, o ideal seria gravar o erro em log e não exibir
    die("Erro ao conectar: " . $e->getMessage());
}
?>

