<?php
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Verifica se usuário é administrador — opcional
// if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['email'] !== 'admin@admin.com') {
//     header("Location: ../index.php");
//     exit;
// }

// Busca todos os pedidos
$stmt = $pdo->query("SELECT p.*, u.nome AS cliente FROM pedidos p 
                     LEFT JOIN usuarios u ON p.usuario_id = u.id
                     ORDER BY p.data DESC");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container flex-fill my-4">

<h2 class="mb-4">Painel de Pedidos</h2>

<?php if ($pedidos): ?>
  <?php foreach ($pedidos as $pedido): ?>
    <div class="card mb-4">
      <div class="card-header bg-dark text-white">
        Pedido #<?= $pedido['id'] ?> | Cliente: <?= htmlspecialchars($pedido['cliente']) ?> | R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?> | <?= date('d/m/Y H:i', strtotime($pedido['data'])) ?>
      </div>
      <div class="card-body">
        <p><strong>Status:</strong> <?= htmlspecialchars($pedido['status']) ?></p>
        <p><strong>Endereço de Entrega:</strong> <?= nl2br(htmlspecialchars($pedido['endereco'])) ?></p>

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

        <!-- Formulário para alterar status -->
        <form method="POST" class="mt-3">
          <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
          <div class="mb-2">
            <label>Status:</label>
            <select name="status" class="form-select" required>
              <?php
              $statusDisponiveis = ['Aguardando Pagamento', 'Pago', 'Enviado', 'Entregue', 'Cancelado'];
              foreach ($statusDisponiveis as $status) {
                  $selected = ($pedido['status'] == $status) ? 'selected' : '';
                  echo "<option value='$status' $selected>$status</option>";
              }
              ?>
            </select>
          </div>
          <button type="submit" class="btn btn-success">Atualizar Status</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>Nenhum pedido cadastrado.</p>
<?php endif; ?>

</main>

<?php include '../includes/rodape.php'; ?>

<?php
// Processa atualização de status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pedido_id']) && isset($_POST['status'])) {
    $pedido_id = intval($_POST['pedido_id']);
    $novo_status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE pedidos SET status = :status WHERE id = :id");
    $stmt->bindValue(':status', $novo_status);
    $stmt->bindValue(':id', $pedido_id);
    $stmt->execute();

    // Redireciona para evitar repost no refresh
    header("Location: pedidos.php");
    exit;
}
?>
