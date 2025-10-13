<?php
require_once 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Isi username dan password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash, twofa_enabled FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['twofa_enabled']) {
                $_SESSION['tmp_user_id'] = $user['id'];
                header('Location: 2fa_verify.php');
                exit;
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit;
            }
        } else {
            $errors[] = 'Username atau password salah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - GameBox</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title">Login</h4>
          <?php if(isset($_GET['registered'])): ?>
            <div class="alert alert-success">Pendaftaran berhasil. Silakan masuk.</div>
          <?php endif; ?>

          <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
              <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
            </div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary">Masuk</button>
            <a href="register.php" class="btn btn-link">Daftar</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
