<?php
session_start();
require 'config.php';

$eroare = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $parola = $_POST['parola'] ?? '';

  if ($email === '' || $parola === '') {
    $eroare = "Completează toate câmpurile.";
  } else {
    $stmt = $conn->prepare("SELECT id_manager, id_parc, nume, parola FROM manageri WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
      $m = $res->fetch_assoc();

      if (!empty($m['parola']) && password_verify($parola, $m['parola'])) {
        $_SESSION['manager_logat'] = true;
        $_SESSION['id_manager'] = (int)$m['id_manager'];
        $_SESSION['id_parc_manager'] = (int)$m['id_parc'];
        $_SESSION['manager_nume'] = $m['nume'];

        header("Location: manager_dashboard.php");
        exit;
      } else {
        $eroare = "Parolă greșită!";
      }
    } else {
      $eroare = "Nu există manager cu acest email.";
    }

    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Manager</title>
  <style>
    body{margin:0;font-family:Arial,sans-serif;background:linear-gradient(135deg,#0f172a,#020617);
      color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;}
    .card{width:360px;background:rgba(255,255,255,.08);padding:28px;border-radius:16px;
      box-shadow:0 20px 40px rgba(0,0,0,.6);}
    h2{text-align:center;margin:0 0 18px;}
    label{display:block;font-size:13px;margin:8px 0 4px;}
    input{width:100%;padding:10px;border:none;border-radius:10px;box-sizing:border-box;}
    button{margin-top:12px;width:100%;padding:10px;border:none;border-radius:10px;cursor:pointer;
      background:#38bdf8;font-weight:bold;}
    button:hover{opacity:.9;}
    .msg{margin-bottom:12px;padding:10px;border-radius:10px;font-size:13px;text-align:center;}
    .err{background:rgba(220,38,38,.25);border:1px solid #f87171;}
  </style>
</head>
<body>
  <div class="card">
    <h2>Autentificare Manager</h2>

    <?php if ($eroare): ?>
      <div class="msg err"><?= htmlspecialchars($eroare) ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required>

      <label>Parolă</label>
      <input type="password" name="parola" required>

      <button type="submit">Intră</button>
    </form>
  </div>
</body>
</html>
