<?php
session_start();
if (!isset($_SESSION['manager_logat']) || $_SESSION['manager_logat'] !== true) {
  header("Location: manager_login.php");
  exit;
}

require 'config.php';

$id_manager = (int)($_SESSION['id_manager'] ?? 0);
$id_parc = (int)($_SESSION['id_parc_manager'] ?? 0);

$mesaj = '';
$eroare = '';

// Update status programare
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $id_programare = (int)($_POST['id_programare'] ?? 0);
  $status = $_POST['status'] ?? '';

  $permise = ['in_asteptare', 'confirmata', 'anulata'];
  if (!in_array($status, $permise, true)) {
    $eroare = "Status invalid.";
  } else {
    $stmt = $conn->prepare("UPDATE programari SET status = ? WHERE id_programare = ? AND id_manager = ?");
    $stmt->bind_param("sii", $status, $id_programare, $id_manager);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
      $mesaj = "Status actualizat cu succes!";
    } else {
      $eroare = "Nu s-a putut actualiza (poate nu e programarea ta).";
    }
    $stmt->close();
  }
}

// Nume parc
$parc_nume = '';
$stmtP = $conn->prepare("SELECT nume_parc FROM parcuri WHERE id_parc = ? LIMIT 1");
$stmtP->bind_param("i", $id_parc);
$stmtP->execute();
$rP = $stmtP->get_result();
if ($rP && $rP->num_rows === 1) {
  $parc_nume = $rP->fetch_assoc()['nume_parc'];
}
$stmtP->close();

// Programari manager
$programari = [];
$sql = "
  SELECT pr.id_programare, pr.data_ora, pr.observatii, pr.status,
         u.username, u.email,
         p.nume_parc
  FROM programari pr
  LEFT JOIN utilizatori u ON u.id_utilizator = pr.id_utilizator
  LEFT JOIN parcuri p ON p.id_parc = pr.id_parc
  WHERE pr.id_manager = ?
  ORDER BY pr.data_ora DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_manager);
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
  while ($row = $res->fetch_assoc()) $programari[] = $row;
}
$stmt->close();

// Numărări pentru “badge-uri”
$count_total = count($programari);
$count_asteptare = 0;
$count_confirmata = 0;
$count_anulata = 0;

foreach ($programari as $pr) {
  if ($pr['status'] === 'in_asteptare') $count_asteptare++;
  if ($pr['status'] === 'confirmata') $count_confirmata++;
  if ($pr['status'] === 'anulata') $count_anulata++;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panou Manager</title>
  <style>
    :root{
      --c1:#7fd1ff;
      --bg1: rgba(0,0,0,.55);
      --card: rgba(255,255,255,.10);
      --card2: rgba(255,255,255,.08);
      --border: rgba(255,255,255,.12);
    }

    body{
      margin:0;
      font-family: Arial, sans-serif;
      color:#fff;
      min-height:100vh;

      /* 🔥 Schimbă imaginea aici cu a ta */
      background:
        linear-gradient(180deg, rgba(0,0,0,.70), rgba(0,0,0,.78)),
        url('audi.jpg') no-repeat center center fixed;

      background-size: cover;
      padding: 28px 0;
    }

    .wrap{
      max-width: 1200px;
      width: clamp(320px, 94vw, 1200px);
      margin: 0 auto;
      padding: 0 14px;
    }

    /* Top bar */
    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      margin-bottom: 14px;
    }

    .title{
      margin:0;
      font-size:28px;
      letter-spacing: .2px;
      text-shadow: 0 10px 25px rgba(0,0,0,.5);
    }

    .subtitle{
      margin:6px 0 0;
      opacity:.92;
    }

    .actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn{
      border:1px solid var(--c1);
      background: rgba(0,0,0,.25);
      color: var(--c1);
      padding:10px 14px;
      border-radius: 12px;
      text-decoration:none;
      cursor:pointer;
      transition:.2s;
      box-shadow: 0 10px 25px rgba(0,0,0,.25);
    }
    .btn:hover{ transform: translateY(-1px); background: rgba(127,209,255,.12); }

    .btn.red{
      border-color:#fb7185;
      color:#fb7185;
    }
    .btn.red:hover{ background: rgba(251,113,133,.12); }

    /* Cards */
    .card{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 16px;
      box-shadow: 0 24px 60px rgba(0,0,0,.45);
      backdrop-filter: blur(8px);
    }

    .msg{
      margin: 10px 0;
      padding: 10px 12px;
      border-radius: 14px;
      font-size: 13px;
      border:1px solid transparent;
      backdrop-filter: blur(8px);
    }
    .msg.ok{ background: rgba(22,163,74,.18); border-color: rgba(74,222,128,.7); }
    .msg.err{ background: rgba(220,38,38,.18); border-color: rgba(248,113,113,.7); }

    /* Stats */
    .stats{
      display:grid;
      grid-template-columns: repeat(4, minmax(160px, 1fr));
      gap: 12px;
      margin: 14px 0;
    }
    .stat{
      background: var(--card2);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 12px;
    }
    .stat .k{ font-size:12px; opacity:.85; }
    .stat .v{ font-size:22px; font-weight:800; margin-top:6px; }

    /* Table */
    .tablewrap{
      margin-top: 10px;
      overflow:auto;
      border-radius: 14px;
      border: 1px solid var(--border);
    }
    table{
      width:100%;
      border-collapse: collapse;
      min-width: 900px;
      background: rgba(0,0,0,.20);
    }
    th, td{
      padding: 12px;
      text-align:left;
      border-bottom: 1px solid rgba(255,255,255,.08);
      font-size: 14px;
      vertical-align: top;
      white-space: nowrap;
    }
    th{
      background: rgba(0,0,0,.45);
      position: sticky;
      top: 0;
      z-index: 2;
    }
    tr:hover td{ background: rgba(255,255,255,.05); }

    .small{ font-size:12px; opacity:.85; white-space: normal; }

    /* Status badges */
    .badge{
      display:inline-block;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      border:1px solid rgba(255,255,255,.18);
      background: rgba(0,0,0,.25);
    }
    .b-wait{ border-color: rgba(250,204,21,.6); color: #fde68a; }
    .b-ok{ border-color: rgba(74,222,128,.6); color: #bbf7d0; }
    .b-no{ border-color: rgba(248,113,113,.6); color: #fecaca; }

    select{
      padding: 8px 10px;
      border-radius: 12px;
      border: 1px solid rgba(127,209,255,.35);
      background: rgba(0,0,0,.25);
      color: #fff;
      outline: none;
    }

    .savebtn{
      border: 1px solid var(--c1);
      background: rgba(127,209,255,.10);
      color: var(--c1);
      padding: 8px 12px;
      border-radius: 12px;
      cursor:pointer;
      transition: .2s;
    }
    .savebtn:hover{ background: rgba(127,209,255,.18); transform: translateY(-1px); }

    /* Empty state */
    .empty{
      margin-top: 12px;
      padding: 24px 18px;
      border-radius: 16px;
      border: 1px dashed rgba(255,255,255,.25);
      background: rgba(0,0,0,.22);
      text-align:center;
    }
    .empty h3{
      margin: 0 0 6px;
      font-size: 20px;
    }
    .empty p{
      margin: 0;
      opacity: .9;
      line-height: 1.5;
    }

    @media (max-width: 900px){
      .stats{ grid-template-columns: 1fr 1fr; }
      .title{ font-size:24px; }
    }
  </style>
</head>
<body>
  <div class="wrap">

    <div class="topbar">
      <div>
        <h1 class="title">Panou Manager</h1>
        <p class="subtitle">
          Manager: <b><?= htmlspecialchars($_SESSION['manager_nume'] ?? '-') ?></b> •
          Parc: <b><?= htmlspecialchars($parc_nume ?: '-') ?></b>
        </p>
      </div>

      <div class="actions">
        <a class="btn" href="pagina_principala.php">Înapoi la site</a>
        <a class="btn red" href="manager_logout.php">Logout</a>
      </div>
    </div>

    <?php if ($mesaj): ?><div class="msg ok"><?= htmlspecialchars($mesaj) ?></div><?php endif; ?>
    <?php if ($eroare): ?><div class="msg err"><?= htmlspecialchars($eroare) ?></div><?php endif; ?>

    <div class="card">
      <h2 style="margin:0 0 6px;">Programări primite</h2>
      <div class="small">
        Total: <b><?= (int)$count_total ?></b> •
        În așteptare: <b><?= (int)$count_asteptare ?></b> •
        Confirmate: <b><?= (int)$count_confirmata ?></b> •
        Anulate: <b><?= (int)$count_anulata ?></b>
      </div>

      <div class="stats">
        <div class="stat">
          <div class="k">Total programări</div>
          <div class="v"><?= (int)$count_total ?></div>
        </div>
        <div class="stat">
          <div class="k">În așteptare</div>
          <div class="v"><?= (int)$count_asteptare ?></div>
        </div>
        <div class="stat">
          <div class="k">Confirmate</div>
          <div class="v"><?= (int)$count_confirmata ?></div>
        </div>
        <div class="stat">
          <div class="k">Anulate</div>
          <div class="v"><?= (int)$count_anulata ?></div>
        </div>
      </div>

      <?php if (count($programari) === 0): ?>
        <div class="empty">
          <h3>Nu există programări momentan</h3>
          <p>Reveniți mai târziu. Când un client programează o întâlnire, aceasta va apărea aici.</p>
        </div>
      <?php else: ?>
        <div class="tablewrap">
          <table>
            <thead>
              <tr>
                <th>Data & ora</th>
                <th>Client</th>
                <th>Parc</th>
                <th>Observații</th>
                <th>Status</th>
                <th>Acțiune</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($programari as $pr): ?>
              <?php
                $status = $pr['status'] ?? 'in_asteptare';
                $badgeClass = 'b-wait';
                if ($status === 'confirmata') $badgeClass = 'b-ok';
                if ($status === 'anulata') $badgeClass = 'b-no';
              ?>
              <tr>
                <td><?= htmlspecialchars($pr['data_ora']) ?></td>
                <td>
                  <b><?= htmlspecialchars($pr['username'] ?? '-') ?></b><br>
                  <span class="small"><?= htmlspecialchars($pr['email'] ?? '-') ?></span>
                </td>
                <td><?= htmlspecialchars($pr['nume_parc'] ?? '-') ?></td>
                <td class="small"><?= htmlspecialchars($pr['observatii'] ?? '-') ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                <td>
                  <form method="post" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="id_programare" value="<?= (int)$pr['id_programare'] ?>">
                    <select name="status">
                      <option value="in_asteptare" <?= $status==='in_asteptare'?'selected':''; ?>>in_asteptare</option>
                      <option value="confirmata" <?= $status==='confirmata'?'selected':''; ?>>confirmata</option>
                      <option value="anulata" <?= $status==='anulata'?'selected':''; ?>>anulata</option>
                    </select>
                    <button class="savebtn" type="submit">Salvează</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
