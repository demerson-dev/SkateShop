<?php
session_start();
include '../includes/conexao.php';

// Verificar login
if (isset($_POST['email'], $_POST['senha'])) {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se encontrou o usuário e se a senha bate
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'tipo' => $usuario['tipo']
        ];
        header("Location: ../index.php");
        exit;
    } else {
        $erro = "E-mail ou senha inválidos.";
    }
}
?>

<?php include '../includes/cabecalho.php'; ?>

<main class="container my-5">
  <h2 class="mb-4">Login</h2>

  <?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3 mb-3">
    <div class="col-md-6">
      <label for="email" class="form-label">E-mail:</label>
      <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label for="senha" class="form-label">Senha:</label>
      <input type="password" name="senha" id="senha" class="form-control" required>
    </div>
    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Entrar</button>
      <a href="cadastro.php" class="btn btn-outline-primary">Cadastrar-se</a>
    </div>
  </form>
</main>

<?php include '../includes/rodape.php'; ?>
