<?php
session_start();
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Proteção: só admin acessa
// if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
//     header("Location: /meu_ecommerce/index.php");
//     exit;
// }

// Verifica se existe busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Monta query
if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT p.*, c.nome AS categoria FROM produtos p
                           LEFT JOIN categorias c ON p.categoria_id = c.id
                           WHERE p.nome LIKE :busca
                           ORDER BY p.id DESC");
    $stmt->bindValue(':busca', "%$busca%");
} else {
    $stmt = $pdo->prepare("SELECT p.*, c.nome AS categoria FROM produtos p
                           LEFT JOIN categorias c ON p.categoria_id = c.id
                           ORDER BY p.id DESC");
}

$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container flex-fill my-4">
    <h2 class="mb-4">PRODUTOS</h2>

    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'admin'): ?>
        <a href="cadastrar_produto.php" class="btn btn-success mb-4">Cadastrar Novo Produto</a>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Categoria</th>
                <th>Estoque</th>
                <th>Destaque</th>
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= $produto['id'] ?></td>
                    <td>
                        <img src="/meu_ecommerce/assets/img/<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" width="60">
                    </td>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($produto['categoria'] ?? 'Sem categoria') ?></td>
                    <td><?= $produto['estoque'] ?></td>
                    <td><?= $produto['destaque'] ? 'Sim' : 'Não' ?></td>
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'admin'): ?>
                        <td>
                            <a href="excluir_produto.php?id=<?= $produto['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este produto?')">Excluir</a>
                        </td>
                    <?php endif; ?>
                    <td>
                        <a href="comprar_produto.php?id=<?= $produto['id'] ?>" class="btn btn-primary btn-sm">Comprar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="/meu_ecommerce/index.php" class="btn btn-secondary mt-3">Voltar para Home</a>
</main>

<?php include '../includes/rodape.php'; ?>