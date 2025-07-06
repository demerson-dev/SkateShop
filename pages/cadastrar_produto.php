<?php
session_start();
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Proteção: só admin acessa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: /meu_ecommerce/index.php");
    exit;
}

// Buscar categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cadastro do produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome']);
    $descricao  = trim($_POST['descricao']);
    $preco      = (float) $_POST['preco'];
    $estoque    = (int) $_POST['estoque'];
    $categoria  = (int) $_POST['categoria'];
    $destaque   = isset($_POST['destaque']) ? 1 : 0;

    // Upload de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem_nome = uniqid() . '_' . $_FILES['imagem']['name'];
        move_uploaded_file($_FILES['imagem']['tmp_name'], '../assets/img/' . $imagem_nome);
    } else {
        $imagem_nome = 'produto_padrao.png'; // caso não envie imagem
    }

    // Inserir produto no banco
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, imagem, estoque, categoria_id, destaque)
                           VALUES (:nome, :descricao, :preco, :imagem, :estoque, :categoria, :destaque)");
    $stmt->execute([
        ':nome'      => $nome,
        ':descricao' => $descricao,
        ':preco'     => $preco,
        ':imagem'    => $imagem_nome,
        ':estoque'   => $estoque,
        ':categoria' => $categoria,
        ':destaque'  => $destaque
    ]);

    header("Location: produtos.php");
    exit;
}
?>

<main class="container flex-fill my-4">
    <h2 class="mb-4">Cadastrar Novo Produto</h2>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nome do Produto</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Preço (R$)</label>
            <input type="number" step="0.01" name="preco" class="form-control" required>
        </div>

        <div class="col-12">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3"></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Estoque</label>
            <input type="number" name="estoque" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Imagem</label>
            <input type="file" name="imagem" class="form-control">
        </div>

        <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="destaque" id="destaque">
                <label class="form-check-label" for="destaque">Produto em Destaque</label>
            </div>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success">Cadastrar Produto</button>
            <a href="produtos.php" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</main>

<?php include '../includes/rodape.php'; ?>
