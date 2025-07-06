<?php
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Inicializa carrinho na sessão se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar item ao carrinho via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produto'])) {
    $id_produto = intval($_POST['id_produto']);
    $quantidade = intval($_POST['quantidade']);

    // Verifica se já está no carrinho
    if (isset($_SESSION['carrinho'][$id_produto])) {
        $_SESSION['carrinho'][$id_produto] += $quantidade;
    } else {
        $_SESSION['carrinho'][$id_produto] = $quantidade;
    }

    header("Location: carrinho.php");
    exit;
}

// Remover item do carrinho
if (isset($_GET['remover'])) {
    $id_remover = intval($_GET['remover']);
    unset($_SESSION['carrinho'][$id_remover]);
    header("Location: carrinho.php");
    exit;
}

// Carregar dados dos produtos do carrinho
$produtos = [];
$total = 0.0;

if (!empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    $stmt = $pdo->query("SELECT * FROM produtos WHERE id IN ($ids)");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total
    foreach ($produtos as $produto) {
        $quantidade = $_SESSION['carrinho'][$produto['id']];
        $total += $produto['preco'] * $quantidade;
    }
}
?>

<main class="container flex-fill my-4">

<h2 class="mb-4">Carrinho de Compras</h2>

<?php if ($produtos): ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Produto</th>
        <th>Preço Unitário</th>
        <th>Quantidade</th>
        <th>Subtotal</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produtos as $produto): ?>
        <tr>
          <td><?= htmlspecialchars($produto['nome']) ?></td>
          <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
          <td><?= $_SESSION['carrinho'][$produto['id']] ?></td>
          <td>R$ <?= number_format($produto['preco'] * $_SESSION['carrinho'][$produto['id']], 2, ',', '.') ?></td>
          <td>
            <a href="?remover=<?= $produto['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remover este item?')">Remover</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="3" class="text-end">Total:</th>
        <th colspan="2">R$ <?= number_format($total, 2, ',', '.') ?></th>
      </tr>
    </tbody>
  </table>

  <a href="/meu_ecommerce/pages/produtos.php" class="btn btn-secondary">Continuar Comprando</a>
  <a href="/meu_ecommerce/pages/finalizar.php" class="btn btn-success">Finalizar Compra</a>

<?php else: ?>
  <p>Seu carrinho está vazio.</p>
  <a href="/meu_ecommerce/pages/produtos.php" class="btn btn-primary">Ver Produtos</a>
<?php endif; ?>

</main>

<?php include '../includes/rodape.php'; ?>
