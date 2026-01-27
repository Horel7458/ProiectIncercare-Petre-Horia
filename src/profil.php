<?php
session_start();
if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
  header("Location: index.php");
  exit;
}

require 'config.php';

$id_utilizator = $_SESSION['id_utilizator'] ?? 0;

// 1) Date utilizator
$user = null;
$stmt = $conn->prepare("SELECT id_utilizator, username, email, nume_complet, data_creare
                        FROM utilizatori
                        WHERE id_utilizator = ? LIMIT 1");
$stmt->bind_param("i", $id_utilizator);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows === 1) {
  $user = $res->fetch_assoc();
}
$stmt->close();

if (!$user) {
  header("Location: logout.php");
  exit;
}

// 2) Programările utilizatorului
$programari = [];
$sqlProg = "
  SELECT pr.id_programare, pr.data_ora, pr.observatii, pr.status,
         p.nume_parc,
         m.nume AS nume_manager
  FROM programari pr
  LEFT JOIN parcuri p ON p.id_parc = pr.id_parc
  LEFT JOIN manageri m ON m.id_manager = pr.id_manager
  WHERE pr.id_utilizator = ?
  ORDER BY pr.data_ora DESC
";
$stmt2 = $conn->prepare($sqlProg);
$stmt2->bind_param("i", $id_utilizator);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2) {
  while ($row = $res2->fetch_assoc()) {
    $programari[] = $row;
  }
}
$stmt2->close();

// 3) Notificările utilizatorului
$notificari = [];
$q = $conn->prepare("SELECT mesaj, data_creare
                     FROM notificari
                     WHERE id_utilizator = ?
                     ORDER BY data_creare DESC");
$q->bind_param("i", $id_utilizator);
$q->execute();
$r = $q->get_result();
if ($r) {
  while ($row = $r->fetch_assoc()) {
    $notificari[] = $row;
  }
}
$q->close();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil utilizator</title>
  <style>
    body{
      margin:0;
      font-family: Arial, sans-serif;
      color:#fff;
      background: linear-gradient(135deg,#0f0f10,#20232a);
      min-height:100vh;
      padding:40px 0;
    }
    .wrap{
      max-width: 1000px;
      width:clamp(300px, 92vw, 1000px);
      margin:0 auto;
      padding:0 16px;
    }
    .header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      margin-bottom:16px;
    }
    .title{
      margin:0;
      font-size:28px;
    }
    .actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }
    .btn{
      border:1px solid #7fd1ff;
      background:transparent;
      color:#7fd1ff;
      padding:10px 14px;
      border-radius:10px;
      cursor:pointer;
      text-decoration:none;
      display:inline-block;
      transition:.2s;
    }
    .btn:hover{ background:rgba(127,209,255,.15); }
    .btn.red{
      border-color:#fb7185;
      color:#fb7185;
    }
    .btn.red:hover{ background:rgba(251,113,133,.12); }

    .card{
      background: rgba(255,255,255,0.08);
      border-radius: 16px;
      padding: 18px;
      box-shadow: 0 20px 40px rgba(0,0,0,.35);
      margin-bottom: 18px;
    }

    .grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:12px;
      margin-top:10px;
    }
    .item{
      background: rgba(0,0,0,0.25);
      border-radius: 12px;
      padding: 12px;
    }
    .label{
      font-size: 12px;
      opacity: .8;
      margin-bottom:6px;
    }
    .value{
      font-size: 15px;
      font-weight: bold;
      word-break: break-word;
    }

    h2{
      margin: 0 0 10px;
      font-size: 20px;
    }

    table{
      width:100%;
      border-collapse: collapse;
      overflow:hidden;
      border-radius:12px;
    }
    th, td{
      padding:10px;
      text-align:left;
      border-bottom:1px solid rgba(255,255,255,0.12);
      font-size:14px;
      vertical-align: top;
    }
    th{
      background: rgba(0,0,0,0.35);
      font-weight:bold;
    }
    tr:hover td{
      background: rgba(255,255,255,0.06);
    }

    .empty{
      text-align:center;
      opacity:.85;
      padding:14px 0 2px;
    }

    /* Notificari */
    .notif{
      background: rgba(0,0,0,0.22);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 12px;
      padding: 10px 12px;
      margin-bottom: 10px;
    }
    .notif small{
      display:block;
      margin-top:6px;
      opacity:.8;
      font-size:12px;
    }

    .badge{
      display:inline-block;
      padding: 5px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      border:1px solid rgba(255,255,255,.18);
    }
    .b-wait{ border-color: rgba(250,204,21,.6); color: #fde68a; }
    .b-ok{ border-color: rgba(74,222,128,.6); color: #bbf7d0; }
    .b-no{ border-color: rgba(248,113,113,.6); color: #fecaca; }

    @media (max-width: 700px){
      .grid{ grid-template-columns: 1fr; }
      .title{ font-size:24px; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1 class="title">Profil utilizator</h1>
      <div class="actions">
        <a class="btn" href="pagina_principala.php">Înapoi</a>
        <a class="btn red" href="logout.php">Logout</a>
      </div>
    </div>

    <div class="card">
      <h2>Date cont</h2>
      <div class="grid">
        <div class="item">
          <div class="label">Username</div>
          <div class="value"><?= htmlspecialchars($user['username']) ?></div>
        </div>

        <div class="item">
          <div class="label">Email</div>
          <div class="value"><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <div class="item">
          <div class="label">Nume complet</div>
          <div class="value"><?= htmlspecialchars($user['nume_complet'] ?: '-') ?></div>
        </div>

        <div class="item">
          <div class="label">Cont creat la</div>
          <div class="value"><?= htmlspecialchars($user['data_creare']) ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <h2>Programările mele</h2>

      <?php if (count($programari) === 0): ?>
        <div class="empty">Nu ai încă nicio programare.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Data & ora</th>
              <th>Parc</th>
              <th>Manager</th>
              <th>Observații</th>
              <th>Status</th>
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
                <td><?= htmlspecialchars($pr['nume_parc'] ?? '-') ?></td>
                <td><?= htmlspecialchars($pr['nume_manager'] ?? '-') ?></td>
                <td><?= htmlspecialchars($pr['observatii'] ?? '-') ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <div class="card">
      <h2>Notificări</h2>

      <?php if (count($notificari) === 0): ?>
        <div class="empty">Nu ai notificări momentan.</div>
      <?php else: ?>
        <?php foreach ($notificari as $n): ?>
          <div class="notif">
            <?= htmlspecialchars($n['mesaj']) ?>
            <small><?= htmlspecialchars($n['data_creare']) ?></small>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
