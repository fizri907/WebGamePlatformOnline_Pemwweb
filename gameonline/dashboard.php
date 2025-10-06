<?php

session_start();


if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit;
}


$logged_in_user = $_SESSION['username'];


$search_query = $_GET['q'] ?? '';


$filter_type = $_GET['filter'] ?? ''; 
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>GameBox — Platform Game Online (Demo)</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <header class="site-header">
    <div class="container header-inner">
      <div class="brand">GameBox</div>
      <nav class="nav">
        <a href="dashboard.php" class="nav-link">Home</a> 
        <a href="dashboard.php?filter=top" class="nav-link">Top</a>
        <a href="dashboard.php?filter=new" class="nav-link">New</a>
      </nav>

      <div class="controls">
        <form method="GET" action="dashboard.php" style="display: flex; gap: 0.6rem;">
            <input 
              id="search" 
              name="q" 
              class="search" 
              placeholder="Cari game, genre, atau developer..."
              value="<?= htmlspecialchars($search_query) ?>" 
            />
            <button type="submit" class="btn play small" style="padding: 0.4rem 0.6rem;">Cari</button>
        </form>
        
        <span class="muted" style="font-weight: 600;">Halo, <?= htmlspecialchars($logged_in_user) ?>!</span>
        <a href="logout.php" class="btn small alt">Keluar</a>
      </div>
    </div>
  </header>

  <main class="container main">

    <section class="controls-row">
      <div id="results-text" class="muted">Menampilkan game</div>
    </section>

    <section id="grid" class="grid"></section>

    <footer class="footer">
      © <span id="year"></span> GameBox · Demo — ganti dengan backend production untuk konten nyata.
    </footer>
  </main>

  <div id="modal" class="modal" aria-hidden="true">
    <div class="modal-inner" role="dialog" aria-modal="true" aria-label="Game modal">
      <header class="modal-header">
        <div id="modal-title" class="modal-title">Game</div>
        <button id="modal-close" class="modal-close" aria-label="Tutup">✕</button>
      </header>
      <div class="modal-body">
        <iframe id="game-iframe" src="" frameborder="0" allowfullscreen></iframe>
      </div>
      <footer class="modal-footer">
        <button id="modal-close-2" class="btn">Tutup</button>
      </footer>
    </div>
    <div id="modal-backdrop" class="modal-backdrop" tabindex="-1"></div>
  </div>

  <script src="script.js"></script>
</body>
</html>