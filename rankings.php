<?php
// rankings.php
const USERS_FILE = __DIR__ . '/users.json';

function load_users(): array {
    if (!file_exists(USERS_FILE)) return [];
    $data = json_decode(file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

$users = load_users();
usort($users, fn($a, $b) => $b['rating'] <=> $a['rating']);

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>FACEMASH - Rankings</title>
  <style>
    body { margin:0; font-family:"Times New Roman", Times, serif; text-align:center; background:#fff; }
    .bar { background:#7a0019; color:#fff; padding:16px 0; font-weight:bold; font-size:22px; }
    .wrap { max-width:760px; margin:24px auto; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { padding:8px; border-bottom:1px solid #ddd; }
    th { background:#f0f0f0; }
    img { width:60px; height:80px; object-fit:cover; }
    .links, .meta { font-size:13px; margin-top:15px; }
    .links a, .meta a { color:#2a77b3; text-decoration:none; margin:0 5px; }
  </style>
</head>
<body>
  <div class="bar">FACEMASH</div>
  <div class="wrap">
    <h2>Rankings</h2>
    <table>
      <tr>
        <th>Rank</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Rating</th>
      </tr>
      <?php foreach ($users as $i => $u): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><img src="<?= h($u['image']) ?>" alt="<?= h($u['name']) ?>"></td>
        <td><?= h($u['name']) ?></td>
        <td><?= h($u['rating']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>

    <div class="links">
      <a href="facemash.php">Return to Facemash</a>
    </div>
  </div>
</body>
</html>

