<?php
include '../includes/conexao.php';
include '../includes/cabecalho.php';

// Cadastro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // Verifica se e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-danger'>E-mail já cadastrado.</div>";
    } else {
        // Insere usuário
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha);
        $stmt->execute();

        echo "<div class='alert alert-success'>Cadastro realizado! Faça login.</div>";
    }
}
?>

<main class="container flex-fill my-4">

<h2 class="mb-4">Cadastrar-se</h2>

<form method="POST" class="row g-3 mb-4">
  <div class="col-12">
    <label>Nome:</label>
    <input type="text" name="nome" class="form-control" required>
  </div>
  <div class="col-12">
    <label>E-mail:</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="col-12">
    <label>Senha:</label>
    <input type="password" name="senha" class="form-control" required>
  </div>
  <div class="col-12">
    <button type="submit" class="btn btn-success">Cadastrar</button>
    <a href="login.php" class="btn btn-secondary">Fazer login</a>
  </div>
</form>

</main>

<?php include '../includes/rodape.php'; ?>
