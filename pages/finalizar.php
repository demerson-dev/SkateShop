<?php
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Verifica se o carrinho existe e tem itens
if (empty($_SESSION['carrinho'])) {
    echo "<p>Seu carrinho está vazio.</p>";
    echo '<a href="produtos.php" class="btn btn-primary">Ver Produtos</a>';
    include '../includes/rodape.php';
    exit;
}

// Se usuário não estiver logado, redireciona
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Buscar dados do carrinho
$produtos = [];
$total = 0.0;

$ids = implode(',', array_keys($_SESSION['carrinho']));
$stmt = $pdo->query("SELECT * FROM produtos WHERE id IN ($ids)");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($produtos as $produto) {
    $quantidade = $_SESSION['carrinho'][$produto['id']];
    $total += $produto['preco'] * $quantidade;
}

// Processa o pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $endereco = trim($_POST['endereco']);
    $usuario_id = $_SESSION['usuario']['id'];

    // Insere pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, valor_total, status, endereco, data)
                           VALUES (:usuario_id, :valor_total, 'Aguardando Pagamento', :endereco, NOW())");
    $stmt->bindValue(':usuario_id', $usuario_id);
    $stmt->bindValue(':valor_total', $total);
    $stmt->bindValue(':endereco', $endereco);
    $stmt->execute();

    $pedido_id = $pdo->lastInsertId();

    // Insere itens do pedido
    foreach ($produtos as $produto) {
        $quantidade = $_SESSION['carrinho'][$produto['id']];
        $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
                               VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)");
        $stmt->bindValue(':pedido_id', $pedido_id);
        $stmt->bindValue(':produto_id', $produto['id']);
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':preco_unitario', $produto['preco']);
        $stmt->execute();

        // Atualiza estoque
        $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - :quantidade WHERE id = :id");
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':id', $produto['id']);
        $stmt->execute();
    }

    // Limpa carrinho
    unset($_SESSION['carrinho']);

    echo "<h3>Pedido realizado com sucesso!</h3>";
    echo "<p>Em breve entraremos em contato para confirmar o pagamento.</p>";
    echo '<a href="meus_pedidos.php" class="btn btn-success">Ver Meus Pedidos</a>';
    include '../includes/rodape.php';
    exit;
}
?>

<main class="container flex-fill my-4">

<h2 class="mb-4">Finalizar Compra</h2>

<!-- Resumo -->
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Produto</th>
      <th>Quantidade</th>
      <th>Preço Unitário</th>
      <th>Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($produtos as $produto): ?>
      <tr>
        <td><?= htmlspecialchars($produto['nome']) ?></td>
        <td><?= $_SESSION['carrinho'][$produto['id']] ?></td>
        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
        <td>R$ <?= number_format($produto['preco'] * $_SESSION['carrinho'][$produto['id']], 2, ',', '.') ?></td>
      </tr>
    <?php endforeach; ?>
    <tr>
      <th colspan="3" class="text-end">Total:</th>
      <th>R$ <?= number_format($total, 2, ',', '.') ?></th>
    </tr>
  </tbody>
</table>

<!-- Formulário endereço -->
<form method="POST" class="mt-4">
  <div class="mb-3">
    <label for="endereco" class="form-label">Endereço de Entrega:</label>
    <textarea name="endereco" id="endereco" class="form-control" required></textarea>
  </div>
  <button type="submit" class="btn btn-success">Confirmar Pedido</button>
  <a href="/meu_ecommerce/pages/carrinho.php" class="btn btn-secondary">Voltar ao Carrinho</a>
</form>

</main>

<?php include '../includes/rodape.php'; ?>
