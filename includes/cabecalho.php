<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Skate Shop</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">


  <!-- CSS Personalizado (se quiser usar) -->
  <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
<div id="wrapper" class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="/meu_ecommerce/index.php">
      <img src="/meu_ecommerce/assets/img/logo.png" alt="Skate Shop" height="80">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuSite" aria-controls="menuSite" aria-expanded="false" aria-label="Toggle navegação">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menuSite">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="/meu_ecommerce/pages/produtos.php">Produtos</a>
        </li>
      </ul>

      <form class="d-flex" action="/meu_ecommerce/pages/produtos.php" method="GET">
        <input class="form-control me-2" type="search" name="busca" placeholder="Buscar produto" aria-label="Buscar">
        <button class="btn btn-outline-light" type="submit">Buscar</button>
      </form>

      <ul class="navbar-nav ms-3">

        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'admin'): ?>
          <li class="nav-item">
           <a class="nav-link" href="/meu_ecommerce/pages/produtos.php">Gerenciar produtos</a>
          </li>
        <?php endif; ?>
  
        <?php if (isset($_SESSION['usuario'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="/meu_ecommerce/pages/carrinho.php">Carrinho</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/meu_ecommerce/pages/meus_pedidos.php">Meus pedidos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/meu_ecommerce/pages/logout.php">Sair (<?= htmlspecialchars($_SESSION['usuario']['nome']) ?>)</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/meu_ecommerce/pages/login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

