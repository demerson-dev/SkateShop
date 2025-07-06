<?php
session_start();
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Verificar se ID veio na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: produtos.php");
    exit;
}

// Buscar produto
$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não achar, volta
if (!$produto) {
    header("Location: produtos.php");
    exit;
}

// Se enviou o form de compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantidade = (int) $_POST['quantidade'];

    if ($quantidade <= 0) {
        $quantidade = 1;
    }

    // Inicia carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Adiciona ou incrementa quantidade
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$id] = $quantidade;
    }

    // Redireciona para carrinho
    header("Location: carrinho.php");
    exit;
}
?>

<main class="container flex-fill my-4">
    <h2 class="mb-4"><?= htmlspecialchars($produto['nome']) ?></h2>

    <div class="row">
        <div class="col-md-5">
            <img src="/meu_ecommerce/assets/img/<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-7">
            <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
            <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
            <p><strong>Estoque:</strong> <?= $produto['estoque'] ?></p>

            <!-- Form para adicionar ao carrinho -->
            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="quantidade" class="form-label">Quantidade</label>
                    <input type="number" name="quantidade" id="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Adicionar ao carrinho</button>
            </form>

            <a href="produtos.php" class="btn btn-secondary mt-3">Voltar para Produtos</a>
        </div>
    </div>
</main>

<?php include '../includes/rodape.php'; ?>
