<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$dbHost = 'localhost'; 
$dbUser = 'root';
$dbPass = '';     
$dbName = 'gamebox_db'; 

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error); 
}
$conn->set_charset("utf8mb4");

function executeQuery($conn, $sql, $types = null, $params = null) {
    if ($types && $params) {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Gagal prepare statement: ' . htmlspecialchars($conn->error)); 
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt;
    } else {
        return $conn->query($sql);
    }
}

$logged_in_user = $_SESSION['username'];
$mode = $_GET['mode'] ?? 'public'; 
$search_query = $_GET['q'] ?? '';
$error = '';
$game_data = []; 


if ($mode === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $thumbnail = trim($_POST['thumbnail'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $rating = $_POST['rating'] ?? 0.0;
    $minutes = $_POST['minutes'] ?? 0;

    if (empty($title) || empty($url) || empty($thumbnail)) {
        $error = 'Judul, Thumbnail, dan URL wajib diisi.';
    } else {
        $sql = "INSERT INTO games (title, thumbnail, url, tags, rating, minutes) VALUES (?, ?, ?, ?, ?, ?)";
        $types = "sssidi";
        $params = [$title, $thumbnail, $url, $tags, $rating, $minutes];

        $stmt = executeQuery($conn, $sql, $types, $params);
        
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Game '{$title}' berhasil ditambahkan!";
            header('Location: dashboard.php?mode=manage');
            exit;
        } else {
            $error = 'Gagal menambahkan game. Coba lagi.';
        }
        $stmt->close();
    }
}

if ($mode === 'edit') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $sql_select = "SELECT * FROM games WHERE id = ?";
        $stmt_select = executeQuery($conn, $sql_select, "i", [$id]);
        $result = $stmt_select->get_result();

        if ($result->num_rows === 0) {
            $_SESSION['message'] = "Game tidak ditemukan.";
            header('Location: dashboard.php?mode=manage');
            exit;
        }
        $game_data = $result->fetch_assoc();
        $stmt_select->close();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
        $title = trim($_POST['title'] ?? '');
        $thumbnail = trim($_POST['thumbnail'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $rating = $_POST['rating'] ?? 0.0;
        $minutes = $_POST['minutes'] ?? 0;
        
        if (empty($title) || empty($url) || empty($thumbnail)) {
             $error = 'Judul, Thumbnail, dan URL wajib diisi.';
        } else {
            $sql_update = "UPDATE games SET title=?, thumbnail=?, url=?, tags=?, rating=?, minutes=? WHERE id=?";
            $types = "sssiddi";
            $params = [$title, $thumbnail, $url, $tags, $rating, $minutes, $id];

            $stmt_update = executeQuery($conn, $sql_update, $types, $params);
            
            if ($stmt_update->affected_rows > 0) {
                $_SESSION['message'] = "Game '{$title}' berhasil diupdate!";
            } else {
                $_SESSION['message'] = "Tidak ada perubahan yang dilakukan pada game.";
            }
            $stmt_update->close();
            header('Location: dashboard.php?mode=manage');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $game_data = array_merge($game_data, $_POST);
    }
}

if ($mode === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $sql = "DELETE FROM games WHERE id = ?";
        $stmt = executeQuery($conn, $sql, "i", [$id]);
        
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Game berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Gagal menghapus game atau game tidak ditemukan.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "ID game tidak valid.";
    }
    header('Location: dashboard.php?mode=manage');
    exit;
}

$games = [];
$stmt = null; 

if ($mode === 'manage') {

    $sql = "SELECT id, title, tags, rating FROM games ORDER BY title ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $games[] = $row;
        }
    }
} else { 

    $sql = "SELECT id, title, thumbnail, url, tags, rating, minutes FROM games";
    $params = [];
    $types = "";

    if ($search_query) {
        $sql .= " WHERE title LIKE ? OR tags LIKE ?";
        $searchTerm = "%" . $search_query . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types = "ss";
    }

    try {
        if ($types) {
            $stmt = executeQuery($conn, $sql, $types, $params);
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $games[] = $row;
            }
        }
    } catch (Exception $e) {

    } finally {
        if ($stmt) {
            $stmt->close();
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= ($mode === 'manage' || $mode === 'add' || $mode === 'edit') ? 'Manajemen Game' : 'GameBox' ?> — Demo</title>
  <link rel="stylesheet" href="styles.css" />
  <?php if ($mode === 'manage' || $mode === 'add' || $mode === 'edit'): // CSS untuk CRUD ?>
  <style>
    .table-crud {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
        background: var(--card);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .table-crud th, .table-crud td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #30305a;
    }
    .table-crud th {
        background: #30305a;
        font-weight: 700;
        color: var(--accent);
    }
    .table-crud tr:hover {
        background: #2a2a4e;
    }
    .action-links a {
        margin-right: 0.8rem;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }
    .action-links .edit { color: #3498db; }
    .action-links .delete { color: var(--accent); }
    .action-links .edit:hover { color: #5dade2; }
    .action-links .delete:hover { color: var(--accent-hover); }
    .form-container {
        max-width: 600px;
        margin: 2rem auto;
        padding: 2rem;
        background: var(--card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--muted); }
    .form-group input, .form-group textarea { 
        width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #30305a; 
        background: var(--bg-alt); color: var(--text); outline: none; 
    }
    .btn-submit { width: 100%; margin-top: 1.5rem; }
  </style>
  <?php endif; ?>
</head>
<body>

  <header class="site-header">
    <div class="container header-inner">
      <div class="brand">GameBox</div>
      <nav class="nav">
        <a href="dashboard.php?mode=public" class="nav-link <?= $mode === 'public' ? 'active' : '' ?>">Home Publik</a> 
        <a href="dashboard.php?mode=manage" class="nav-link <?= $mode === 'manage' || $mode === 'add' || $mode === 'edit' ? 'active' : '' ?>">Manajemen (CRUD)</a>
        <?php if ($mode === 'public'): ?>
        <a href="#" class="nav-link">Top</a>
        <a href="#" class="nav-link">New</a>
        <?php endif; ?>
      </nav>

      <div class="controls">
        <?php if ($mode === 'public'):?>
        <form method="GET" action="dashboard.php" style="display: flex; gap: 0.6rem;">
            <input type="hidden" name="mode" value="public" />
            <input 
              id="search" 
              name="q" 
              class="search" 
              placeholder="Cari game, genre, atau developer..."
              value="<?= htmlspecialchars($search_query) ?>" 
            />
            <button type="submit" class="btn play small" style="padding: 0.4rem 0.6rem;">Cari</button>
        </form>
        <?php endif; ?>
        
        <span class="muted" style="font-weight: 600;">Halo, <?= htmlspecialchars($logged_in_user) ?>!</span>
        <a href="logout.php" class="btn small alt">Keluar</a>
      </div>
    </div>
  </header>

  <main class="container main">

    <?php if (isset($_SESSION['message'])):?>
        <div style="background: #215e21; color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if ($mode === 'public'):?>
        <section class="controls-row">
            <?php if ($search_query): ?>
                <div id="results-text" class="muted">Hasil untuk "<?= htmlspecialchars($search_query) ?>"</div>
            <?php elseif (empty($games)): ?>
                <div id="results-text" class="muted">Tidak ada game yang tersedia saat ini.</div>
            <?php else: ?>
                <div id="results-text" class="muted">Menampilkan <?= count($games) ?> game</div>
            <?php endif; ?>
        </section>

        <section id="grid" class="grid">
            <?php if (empty($games)): ?>
                <p>Tidak ada game yang cocok dengan pencarian Anda.</p>
            <?php else: ?>
                <?php foreach ($games as $game): 
                    $tags_display = htmlspecialchars(str_replace(",", ", ", $game['tags'])); 
                    $game_url = htmlspecialchars($game['url']);
                    $game_title = htmlspecialchars($game['title']);
                    $game_thumbnail = htmlspecialchars($game['thumbnail']);
                ?>
                <div class="card-game">
                    <div class="thumb"><img src="<?= $game_thumbnail ?>" alt="<?= $game_title ?>"></div>
                    <div class="meta">
                        <div class="title"><?= $game_title ?></div>
                        <div class="tags"><?= $tags_display ?></div>
                        <button 
                            class="btn play small" 
                            onclick="openGame({title: '<?= $game_title ?>', url: '<?= $game_url ?>'})"
                        >Mainkan</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    <?php elseif ($mode === 'manage'):?>
        <h2>Manajemen Game</h2>

        <a href="dashboard.php?mode=add" class="btn play small" style="margin-bottom: 1rem;">+ Tambah Game Baru</a>

        <?php if (empty($games)): ?>
            <p class="muted">Belum ada game di database.</p>
        <?php else: ?>
        <table class="table-crud">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Tags</th>
                    <th>Rating</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                <tr>
                    <td><?= htmlspecialchars($game['id']) ?></td>
                    <td><?= htmlspecialchars($game['title']) ?></td>
                    <td><?= htmlspecialchars($game['tags']) ?></td>
                    <td><?= htmlspecialchars($game['rating']) ?></td>
                    <td class="action-links">
                        <a href="dashboard.php?mode=edit&id=<?= $game['id'] ?>" class="edit">Edit</a>
                        <a href="dashboard.php?mode=delete&id=<?= $game['id'] ?>" class="delete" 
                           onclick="return confirm('Anda yakin ingin menghapus game ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    <?php elseif ($mode === 'add' || $mode === 'edit'): ?>
        <div class="form-container">
            <h2><?= $mode === 'add' ? 'Tambah Game Baru' : 'Edit Game: ' . htmlspecialchars($game_data['title']) ?></h2>
            
            <?php if ($error): ?>
                <p class="error" style="background: #3a1a1a; padding: 1rem; border-radius: 8px;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="dashboard.php?mode=<?= $mode ?><?= $mode === 'edit' ? '&id=' . $id : '' ?>">
              <div class="form-group">
                <label for="title">Judul Game <span style="color:red;">*</span></label>
                <input type="text" id="title" name="title" required value="<?= htmlspecialchars($game_data['title'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="thumbnail">Link Thumbnail <span style="color:red;">*</span></label>
                <input type="url" id="thumbnail" name="thumbnail" required value="<?= htmlspecialchars($game_data['thumbnail'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="url">Link URL Game (Iframe) <span style="color:red;">*</span></label>
                <input type="url" id="url" name="url" required value="<?= htmlspecialchars($game_data['url'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="tags">Tags (Dipisahkan koma, cth: Arcade, Classic)</label>
                <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($game_data['tags'] ?? '') ?>">
              </div>
              <div class="form-group" style="display: flex; gap: 1rem;">
                <div style="flex: 1;">
                    <label for="rating">Rating (0.0 - 5.0)</label>
                    <input type="number" step="0.1" min="0" max="5" id="rating" name="rating" value="<?= htmlspecialchars($game_data['rating'] ?? 4.0) ?>">
                </div>
                <div style="flex: 1;">
                    <label for="minutes">Perkiraan Waktu Main (Menit)</label>
                    <input type="number" min="1" id="minutes" name="minutes" value="<?= htmlspecialchars($game_data['minutes'] ?? 5) ?>">
                </div>
              </div>

              <button type="submit" class="btn play btn-submit"><?= $mode === 'add' ? 'Simpan Game' : 'Simpan Perubahan' ?></button>
              <a href="dashboard.php?mode=manage" class="btn alt btn-submit">Batal</a>
            </form>
        </div>
    <?php endif; ?>

    <footer class="footer">
      © <span id="year-footer"></span> GameBox · Demo.
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
  <script>document.getElementById("year-footer").textContent = new Date().getFullYear();</script>
</body>
</html>