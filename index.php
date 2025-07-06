<?php
include 'includes/conexao.php';
include 'includes/cabecalho.php';

// Busca produtos com destaque
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE destaque = 1 LIMIT 8");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container flex-fill my-4">

<!-- Banner (imagem) -->
<div class="hero-banner"></div>

<!-- Busca (Home) -->
<form class="row g-3 mb-4" action="/meu_ecommerce/pages/produtos.php" method="GET">
  <div class="col-md-10">
    <input type="text" name="busca" class="form-control" placeholder="Buscar produto pelo nome">
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">Buscar</button>
  </div>
</form>

<!-- Texto de boas-vindas -->
<div class="p-5 mb-4 bg-secondary text-white rounded-3 text-center">
  <div class="container py-5">
    <h1 class="display-4">Bem-vindo à Skate Shop!</h1>
    <p class="lead">A melhor loja de acessórios e roupas para quem vive sobre rodas.</p>
  </div>
</div>

<!-- Produtos em Destaque -->
<h2 class="mb-4">Destaques</h2>
<div class="row">
  <?php if ($produtos): ?>
    <?php foreach ($produtos as $produto): ?>
      <div class="col-md-3 mb-4">
        <div class="card h-100">
          <img src="assets/img/<?= $produto['imagem'] ?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
            <p class="card-text">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
            <a href="/meu_ecommerce/pages/comprar_produto.php?id=<?= $produto['id'] ?>" class="btn btn-primary mt-auto">Ver produto</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Nenhum produto em destaque no momento.</p>
  <?php endif; ?>
</div>

</main>

<?php include 'includes/rodape.php'; ?>
