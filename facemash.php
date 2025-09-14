<?php
// facemash.php

// ==== Storage helpers ====
const USERS_FILE = __DIR__ . '/users.json';

function load_users(): array {
    if (!file_exists(USERS_FILE)) {
$seed = [
    ['id' => 1, 'name' => 'User 1', 'image' => 'https://randomuser.me/api/portraits/men/11.jpg', 'rating' => 1200],
    ['id' => 2, 'name' => 'User 2', 'image' => 'https://randomuser.me/api/portraits/women/21.jpg', 'rating' => 1200],
    ['id' => 3, 'name' => 'User 3', 'image' => 'https://randomuser.me/api/portraits/men/31.jpg', 'rating' => 1200],
    ['id' => 4, 'name' => 'User 4', 'image' => 'https://randomuser.me/api/portraits/women/41.jpg', 'rating' => 1200],
    ['id' => 5, 'name' => 'User 5', 'image' => 'https://randomuser.me/api/portraits/men/51.jpg', 'rating' => 1200],
    ['id' => 6, 'name' => 'User 6', 'image' => 'https://randomuser.me/api/portraits/women/61.jpg', 'rating' => 1200],
    ['id' => 7, 'name' => 'User 7', 'image' => 'https://randomuser.me/api/portraits/men/71.jpg', 'rating' => 1200],
    ['id' => 8, 'name' => 'User 8', 'image' => 'https://randomuser.me/api/portraits/women/81.jpg', 'rating' => 1200],
];
        file_put_contents(USERS_FILE, json_encode($seed, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
    $data = json_decode(file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

function save_users(array $users): void {
    file_put_contents(USERS_FILE, json_encode(array_values($users), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
}

function index_by_id(array $users): array {
    $idx = [];
    foreach ($users as $i => $u) $idx[$u['id']] = $i;
    return $idx;
}

function get_random_pair(array $users): array {
    if (count($users) < 2) { return [null, null]; }
    $i = array_rand($users);
    do { $j = array_rand($users); } while ($j === $i);
    return [$users[$i], $users[$j]];
}

function update_elo(array &$users, int $winnerId, int $loserId, int $k = 32): void {
    $byId = index_by_id($users);
    if (!isset($byId[$winnerId], $byId[$loserId]) || $winnerId === $loserId) return;

    $wi = $byId[$winnerId];
    $li = $byId[$loserId];

    $Ra = $users[$wi]['rating'];
    $Rb = $users[$li]['rating'];

    $Ea = 1 / (1 + pow(10, ($Rb - $Ra) / 400));
    $Eb = 1 / (1 + pow(10, ($Ra - $Rb) / 400));

    $users[$wi]['rating'] = round($Ra + $k * (1 - $Ea));
    $users[$li]['rating'] = round($Rb + $k * (0 - $Eb));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $winnerId = isset($_POST['winner']) ? (int)$_POST['winner'] : 0;
    $loserId  = isset($_POST['loser'])  ? (int)$_POST['loser']  : 0;

    $users = load_users();
    if ($winnerId && $loserId && $winnerId !== $loserId) {
        update_elo($users, $winnerId, $loserId);
        save_users($users);
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$users = load_users();
[$left, $right] = get_random_pair($users);
if (!$left || !$right) {
    http_response_code(500);
    echo "É necessário pelo menos 2 usuários em users.json";
    exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>FACEMASH</title>
  <style>
    body { margin:0; font-family:"Times New Roman", Times, serif; text-align:center; background:#fff; }
    .bar { background:#7a0019; color:#fff; padding:16px 0; font-weight:bold; font-size:22px; }
    .wrap { max-width:760px; margin:24px auto; }
    .motto { font-size:14px; margin-bottom:10px; }
    .title { font-weight:bold; font-size:18px; margin:20px 0; }
    .faces { display:inline-flex; align-items:center; gap:20px; }
    .face { width:240px; cursor:pointer; border:none; background:transparent; }
    .face img { width:100%; height:300px; object-fit:cover; }
    .or { font-weight:bold; }
    .links, .meta { font-size:13px; margin-top:15px; }
    .links a, .meta a { color:#2a77b3; text-decoration:none; margin:0 5px; }
  </style>
</head>
<body>
  <div class="bar">FACEMASH</div>
  <div class="wrap">
    <p class="motto">Were we let in for our looks? No. Will we be judged on them? Yes.</p>
    <div class="title">Who’s Hotter? Click to Choose.</div>
    <form method="post" class="faces">
      <input type="hidden" name="winner" value="">
      <input type="hidden" name="loser" value="">
      <button class="face" type="submit"
        onclick="this.form.winner.value='<?= (int)$left['id'] ?>'; this.form.loser.value='<?= (int)$right['id'] ?>'">
        <img src="<?= h($left['image']) ?>" alt="<?= h($left['name']) ?>">
      </button>
      <div class="or">OR</div>
      <button class="face" type="submit"
        onclick="this.form.winner.value='<?= (int)$right['id'] ?>'; this.form.loser.value='<?= (int)$left['id'] ?>'">
        <img src="<?= h($right['image']) ?>" alt="<?= h($right['name']) ?>">
      </button>
    </form>
    <div class="links">
      <a href="#">ADAMS</a><a href="#">CABOT</a><a href="#">CURRIER</a><a href="#">DUNSTER</a>
      <a href="#">ELIOT</a><a href="#">KIRKLAND</a><a href="#">LEVERETT</a><a href="#">LOWELL</a>
      <a href="#">MATHER</a><a href="#">PFOHO</a><a href="#">WINTHROP</a><a href="#">RANDOM</a>
    </div>
    <div class="meta">
      <a href="#">About</a> · <a href="#">Submit</a> · <a href="#">Rankings</a> · <a href="#">Previous</a>
    </div>
  </div>
</body>
</html>
