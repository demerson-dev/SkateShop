<?php
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Proteção: verifica se o usuário está logado (opcional para admin)
// if (!isset($_SESSION['usuario'])) {
//     header("Location: ../pages/login.php");
//     exit;
// }

// Adicionar categoria
if (isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    if (!empty($nome)) {
        $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (:nome)");
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
        header("Location: categorias.php");
        exit;
    }
}

// Excluir categoria
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    header("Location: categorias.php");
    exit;
}

// Listar categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container flex-fill my-4">

<h2 class="mb-4">Gerenciar Categorias</h2>

<!-- Formulário -->
<form method="POST" class="row g-3 mb-4">
  <div class="col-auto">
    <input type="text" name="nome" class="form-control" placeholder="Nova categoria" required>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-success">Cadastrar</button>
  </div>
</form>

<!-- Lista de categorias -->
<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($categorias as $categoria): ?>
      <tr>
        <td><?= $categoria['id'] ?></td>
        <td><?= htmlspecialchars($categoria['nome']) ?></td>
        <td>
          <a href="?excluir=<?= $categoria['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir esta categoria?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="/meu_ecommerce/index.php" class="btn btn-secondary">Voltar para Home</a>

</main>

<?php include '../includes/rodape.php'; ?>
