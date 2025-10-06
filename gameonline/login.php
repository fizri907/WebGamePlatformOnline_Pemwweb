<?php

session_start();


if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';


    $valid_username = 'user';
    $valid_password = 'password123';

    if ($username === $valid_username && $password === $valid_password) {

        $_SESSION['username'] = $username;
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error_message = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - GameBox</title>
  <link rel="stylesheet" href="styles.css" />
  <style>

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: var(--bg);
        color: var(--text);
        padding: 2rem;
    }
    .login-container {
        background: var(--card);
        padding: 3rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        width: 100%;
        max-width: 420px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .login-container h2 {
        color: var(--accent);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1.8rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--muted);
    }
    .form-group input {
        width: 100%;
        padding: 0.8rem;
        border-radius: 8px;
        border: 1px solid #30305a;
        background: var(--bg-alt);
        color: var(--text);
        outline: none;
        transition: border-color 0.3s;
    }
    .form-group input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 5px rgba(255, 107, 129, 0.5);
    }
    .error {
        color: var(--accent);
        background: #3a1a1a;
        padding: 0.8rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: 600;
    }
    .btn-login {
        width: 100%;
        margin-top: 1.5rem;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>GameBox Login</h2>
    
    <?php if ($error_message): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="username">Username (Demo: user)</label>
        <input type="text" id="username" name="username" required autocomplete="username">
      </div>
      <div class="form-group">
        <label for="password">Password (Demo: password123)</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn play btn-login">Masuk</button>
    </form>
  </div>

</body>
</html>