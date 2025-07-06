<?php
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Verifica login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

// Busca pedidos do usuário
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = :usuario_id ORDER BY data DESC");
$stmt->bindValue(':usuario_id', $usuario_id);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container flex-fill my-4">

<h2 class="mb-4">Meus Pedidos</h2>

<?php if ($pedidos): ?>
  <?php foreach ($pedidos as $pedido): ?>
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        Pedido #<?= $pedido['id'] ?> | R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?> | <?= date('d/m/Y H:i', strtotime($pedido['data'])) ?>
      </div>
      <div class="card-body">
        <p><strong>Status:</strong> <?= htmlspecialchars($pedido['status']) ?></p>
        <p><strong>Endereço:</strong> <?= nl2br(htmlspecialchars($pedido['endereco'])) ?></p>

        <!-- Itens do pedido -->
        <h5 class="mt-3">Itens:</h5>
        <ul>
          <?php
          $stmtItens = $pdo->prepare("SELECT i.*, p.nome FROM itens_pedido i
                                      LEFT JOIN produtos p ON i.produto_id = p.id
                                      WHERE i.pedido_id = :pedido_id");
          $stmtItens->bindValue(':pedido_id', $pedido['id']);
          $stmtItens->execute();
          $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

          foreach ($itens as $item):
          ?>
            <li>
              <?= htmlspecialchars($item['nome']) ?> — <?= $item['quantidade'] ?> x R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?> = R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>Você ainda não fez nenhum pedido.</p>
  <a href="produtos.php" class="btn btn-primary">Fazer uma compra</a>
<?php endif; ?>

</main>

<?php include '../includes/rodape.php'; ?>
