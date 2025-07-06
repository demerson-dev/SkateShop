<?php
session_start();
include '../includes/conexao.php';

// Só admin pode excluir
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: /meu_ecommerce/index.php");
    exit;
}

// Valida se recebeu ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: produtos.php");
    exit;
}

$id = (int) $_GET['id'];

// (Opcional) buscar o produto antes para excluir a imagem associada
$stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($produto) {
    // Excluir imagem física (se existir)
    $caminhoImagem = '../assets/img/' . $produto['imagem'];
    if (file_exists($caminhoImagem)) {
        unlink($caminhoImagem);
    }

    // Excluir produto do banco
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
}

// Redireciona de volta
header("Location: produtos.php");
exit;
?>
