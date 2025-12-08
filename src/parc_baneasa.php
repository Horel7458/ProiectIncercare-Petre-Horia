<?php
require 'config.php';   // conexiune la baza de date

// ID-ul parcului Băneasa din tabelul PARCURI (vezi cu SELECT * FROM parcuri)
$idParc = 1;

$manager = null;
$stmt = $conn->prepare("SELECT nume, email, telefon, functie FROM manageri WHERE id_parc = ? LIMIT 1");
$stmt->bind_param("i", $idParc);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $manager = $result->fetch_assoc();
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoPark Băneasa</title>
  <style>
    body {
      margin:0;
      font-family:Arial, sans-serif;
      color:#fff;
      background: linear-gradient(135deg,#0f0f10,#20232a);
      min-height:100vh;
      padding:40px 0;
      text-align:center;
    }

    .box {
      background: rgba(255,255,255,0.08);
      padding: 28px;
      border-radius: 16px;
      text-align:center;
      max-width: 900px;
      width:clamp(300px,90vw,900px);
      margin:auto;
    }

    a {
      color:#7fd1ff;
      text-decoration:none;
    }
    a:hover { text-decoration:underline; }

    .cta {
      margin-top:16px;
      display:flex;
      gap:12px;
      justify-content:center;
      flex-wrap:wrap;
    }

    .btn {
      border:1px solid #7fd1ff;
      background:transparent;
      color:#7fd1ff;
      padding:10px 16px;
      border-radius:10px;
      cursor:pointer;
      transition:0.3s;
      display:inline-block;
      margin:0 auto;
    }

    .btn:hover {
      background:rgba(127,209,255,.15);
    }

    .separator {
      margin:24px 0 14px;
      border:none;
      border-top:1px solid rgba(255,255,255,0.2);
    }

    /* ====== MODAL (popup manager) ====== */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
      display: none;           /* ascuns by default */
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .modal-content {
      background: #111827;
      border-radius: 14px;
      padding: 20px 22px;
      max-width: 420px;
      width: 90%;
      text-align: left;
      box-shadow: 0 20px 40px rgba(0,0,0,.6);
      position: relative;
    }

    .modal-content h3 {
      margin-top: 0;
      margin-bottom: 10px;
    }

    .modal-content p {
      margin: 6px 0;
    }

    .close-btn {
      position:absolute;
      top:8px;
      right:10px;
      border:none;
      background:transparent;
      color:#9ca3af;
      font-size:20px;
      cursor:pointer;
    }
    .close-btn:hover {
      color:#fff;
    }
  </style>
</head>
<body>

  <div class="box">
    <h1>AutoPark Băneasa</h1>
    <p>Adresă: Șos. București-Ploiești 42, București</p>
    <p>Program: L–V 09:00–19:00 • S 10:00–16:00</p>

    <div class="cta">
      <a class="btn" href="pagina_principala.php">Înapoi la listă</a>
      <a class="btn" href="parc_baneasa_masini.html">Vezi mașinile disponibile</a>
    </div>

    <hr class="separator">

    <h2>Managerul parcului</h2>

    <?php if ($manager): ?>
      <button id="openManager" class="btn">Vezi detaliile managerului</button>
    <?php else: ?>
      <p>Momentan nu există un manager definit pentru acest parc.</p>
    <?php endif; ?>
  </div>

  <?php if ($manager): ?>
  <!-- MODAL POPUP -->
  <div id="managerModal" class="modal-overlay">
    <div class="modal-content">
      <button class="close-btn" id="closeManager">&times;</button>
      <h3>Detalii manager</h3>
      <p><strong>Nume:</strong> <?= htmlspecialchars($manager['nume']) ?></p>
      <p><strong>Funcție:</strong> <?= htmlspecialchars($manager['functie']) ?></p>
      <p><strong>Telefon:</strong> <?= htmlspecialchars($manager['telefon']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($manager['email']) ?></p>
    </div>
  </div>
  <?php endif; ?>

  <script>
    const openBtn  = document.getElementById('openManager');
    const modal    = document.getElementById('managerModal');
    const closeBtn = document.getElementById('closeManager');

    if (openBtn && modal && closeBtn) {
      openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
      });

      closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
      });

      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.style.display = 'none';
        }
      });
    }
  </script>

</body>
</html>
