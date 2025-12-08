<?php
session_start();
if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header("Location: index.php");
    exit;
}

require 'config.php';

$mesaj_succes = '';
$mesaj_eroare = '';

// 1. Procesăm trimiterea formularului de programare
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['programare_form'])) {
    $id_utilizator = $_SESSION['id_utilizator'] ?? null;
    $id_parc       = intval($_POST['id_parc'] ?? 0);
    $id_manager    = intval($_POST['id_manager'] ?? 0);
    $data          = trim($_POST['data_programare'] ?? '');
    $ora           = trim($_POST['ora_programare'] ?? '');
    $observatii    = trim($_POST['observatii'] ?? '');

    if (!$id_parc || !$id_manager || $data === '' || $ora === '') {
        $mesaj_eroare = "Te rugăm să completezi toate câmpurile obligatorii pentru programare.";
    } else {
        $data_ora = $data . ' ' . $ora . ':00';

        $stmt = $conn->prepare("
            INSERT INTO programari (id_utilizator, id_parc, id_manager, data_ora, observatii)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiiss", $id_utilizator, $id_parc, $id_manager, $data_ora, $observatii);

        if ($stmt->execute()) {
            $mesaj_succes = "Programarea a fost înregistrată cu succes!";
        } else {
            $mesaj_eroare = "A apărut o eroare la salvarea programării.";
        }

        $stmt->close();
    }
}

// 2. Luăm parcurile + managerii lor din baza de date
$parcuri = [];
$sql = "
    SELECT p.id_parc, p.nume_parc,
           m.id_manager, m.nume AS nume_manager, m.email, m.telefon
    FROM parcuri p
    LEFT JOIN manageri m ON m.id_parc = p.id_parc
    ORDER BY p.id_parc ASC
";
$rez = $conn->query($sql);
if ($rez && $rez->num_rows > 0) {
    while ($row = $rez->fetch_assoc()) {
        $parcuri[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parc Auto - Pagina Principală</title>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('audi.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      text-align: center;
      min-height: 100vh;
    }

    .content {
      background: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 15px;
      display: inline-block;
      margin-top: 40px;
      max-width: 1200px;
    }

    h1 { margin-bottom: 12px; }

    .intro {
      margin: 0 auto 24px;
      max-width: 900px;
      opacity: .95;
      line-height: 1.6;
    }

    /* Grid mașini */
    .cars-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto 36px;
    }

    .car-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 15px;
      transition: transform 0.3s, background 0.3s;
    }

    .car-card:hover {
      transform: scale(1.05);
      background: rgba(255, 255, 255, 0.2);
    }

    .car-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 10px;
    }

    .car-card h3 { margin: 10px 0; }

    .car-card button {
      background-color: #007bff;
      border: none;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .car-card button:hover { background-color: #0056b3; }

    /* Secțiune parcuri partenere */
    .section-title { margin: 12px 0 18px; }

    .parks-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto 36px;
    }

    .park-card {
      display: block;
      text-decoration: none;
      color: inherit;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 15px;
      transition: transform 0.3s, background 0.3s;
    }

    .park-card:hover {
      transform: scale(1.05);
      background: rgba(255, 255, 255, 0.2);
    }

    .park-card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      border-radius: 10px;
    }

    .park-card h4 { margin: 10px 0 0; }

    /* Secțiune programare întâlnire */
    .appointment-section {
      margin-top: 30px;
      padding: 20px;
      border-radius: 15px;
      background: rgba(0, 0, 0, 0.5);
      text-align: left;
    }

    .appointment-section h2 {
      text-align: center;
      margin-bottom: 15px;
    }

    .appointment-form {
      max-width: 700px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 20px;
    }

    .appointment-form label {
      font-size: 14px;
      display: block;
      margin-bottom: 4px;
    }

    .appointment-form select,
    .appointment-form input,
    .appointment-form textarea {
      width: 100%;
      padding: 8px;
      border-radius: 8px;
      border: none;
      box-sizing: border-box;
    }

    .appointment-form textarea {
      resize: vertical;
      min-height: 70px;
      grid-column: span 2;
    }

    .appointment-form .full-width {
      grid-column: span 2;
    }

    .appointment-form button {
      grid-column: span 2;
      padding: 10px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      background-color: #10b981;
      color: #fff;
      font-size: 15px;
      font-weight: bold;
      margin-top: 5px;
    }

    .appointment-form button:hover {
      background-color: #059669;
    }

    .msg {
      margin: 10px auto 15px;
      max-width: 700px;
      padding: 8px 10px;
      border-radius: 8px;
      font-size: 14px;
      text-align: center;
    }

    .msg.success {
      background: rgba(22, 163, 74, 0.25);
      border: 1px solid #4ade80;
    }

    .msg.error {
      background: rgba(220, 38, 38, 0.25);
      border: 1px solid #f87171;
    }

    .logout { margin-top: 25px; }

    .logout a {
      color: #00bfff;
      text-decoration: none;
      font-weight: bold;
    }

    .logout a:hover { text-decoration: underline; }

    /* =============================
           TEMA LIGHT GLOBAL
       ============================= */

    .light-mode {
      background: #f3f3f3 !important;
      color: #111 !important;
    }

    .light-mode .content,
    .light-mode .car-card,
    .light-mode .park-card,
    .light-mode .appointment-section {
      background: rgba(0,0,0,0.05) !important;
      color: #111 !important;
    }

    .light-mode .car-card button {
      background-color: #005bbb;
    }

    /* Buton schimbare temă */
    #themeToggle {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 8px 14px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      background: #7fd1ff;
      color: #000;
      font-weight: bold;
      box-shadow: 0 0 10px #0004;
      transition: 0.3s;
      z-index: 99999;
    }

    #themeToggle:hover {
      transform: scale(1.05);
    }

    @media (max-width: 700px) {
      .appointment-form {
        grid-template-columns: 1fr;
      }
      .appointment-form .full-width,
      .appointment-form button,
      .appointment-form textarea {
        grid-column: span 1;
      }
    }
  </style>
</head>

<body>

  <!-- BUTON DARK/LIGHT MODE -->
  <button id="themeToggle">Light Mode</button>

  <div class="content">
    <h1>Bun venit în parcul auto!</h1>

    <p class="intro">
      Pentru o ofertă mai amplă de mașini accesează parcurile noastre auto partenere.
    </p>

    <!-- Mașinile principale -->
    <div class="cars-grid">
      <div class="car-card">
        <img src="audi.jpg" alt="Audi RS6">
        <h3>Audi RS6</h3>
        <button onclick="window.location.href='detalii_audi.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="bmw.jpg" alt="BMW M4">
        <h3>BMW M4 Competition</h3>
        <button onclick="window.location.href='detalii_bmw.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="mercedes.jpg" alt="Mercedes AMG">
        <h3>Mercedes-AMG GT</h3>
        <button onclick="window.location.href='detalii_mercedes.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="porsche.jpg" alt="Porsche 911">
        <h3>Porsche 911 Turbo S</h3>
        <button onclick="window.location.href='detalii_porsche.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="lamborghini.jpg" alt="Lamborghini Huracán">
        <h3>Lamborghini Huracán</h3>
        <button onclick="window.location.href='detalii_lamborghini.php'">Vezi detalii</button>
      </div>

      <div class="car-card">
        <img src="ferrari.jpg" alt="Ferrari F8">
        <h3>Ferrari F8 Tributo</h3>
        <button onclick="window.location.href='detalii_ferrari.php'">Vezi detalii</button>
      </div>
    </div>

    <!-- Parcuri auto partenere -->
    <h2 class="section-title">Parcuri auto partenere</h2>

    <div class="parks-grid">
      <a class="park-card" href="parc_titan.php">
        <img src="park_titan.jpg" alt="AutoPark Titan">
        <h4>AutoPark Titan</h4>
      </a>

      <a class="park-card" href="parc_baneasa.php">
        <img src="parc_baneasa.jpg" alt="AutoPark Băneasa">
        <h4>AutoPark Băneasa</h4>
      </a>

      <a class="park-card" href="parc_militari.php">
        <img src="parc_militari.jpg" alt="AutoPark Militari">
        <h4>AutoPark Militari</h4>
      </a>

      <a class="park-card" href="parc_otopeni.php">
        <img src="parc_otopeni.jpg" alt="AutoPark Otopeni">
        <h4>AutoPark Otopeni</h4>
      </a>

      <a class="park-card" href="parc_pipera.php">
        <img src="parc_pipera.jpg" alt="AutoPark Pipera">
        <h4>AutoPark Pipera</h4>
      </a>

      <a class="park-card" href="parc_constanta.php">
        <img src="parc_constanta.jpg" alt="AutoPark Constanța">
        <h4>AutoPark Constanța</h4>
      </a>
    </div>

    <!-- SECȚIUNE PROGRAMARE ÎNTÂLNIRE -->
    <div class="appointment-section">
      <h2>Programează o întâlnire</h2>

      <?php if ($mesaj_succes): ?>
        <div class="msg success"><?= htmlspecialchars($mesaj_succes) ?></div>
      <?php endif; ?>

      <?php if ($mesaj_eroare): ?>
        <div class="msg error"><?= htmlspecialchars($mesaj_eroare) ?></div>
      <?php endif; ?>

      <?php if (!empty($parcuri)): ?>
      <form method="post" class="appointment-form">
        <input type="hidden" name="programare_form" value="1">

        <!-- Select parc -->
        <div class="full-width">
          <label for="id_parc">Alege parcul *</label>
          <select name="id_parc" id="id_parc" required>
            <option value="">-- Selectează un parc auto --</option>
            <?php foreach ($parcuri as $p): ?>
              <option
                value="<?= htmlspecialchars($p['id_parc']) ?>"
                data-manager-id="<?= htmlspecialchars($p['id_manager'] ?? 0) ?>"
                data-manager-nume="<?= htmlspecialchars($p['nume_manager'] ?? 'Nedefinit') ?>"
                data-manager-email="<?= htmlspecialchars($p['email'] ?? '-') ?>"
                data-manager-telefon="<?= htmlspecialchars($p['telefon'] ?? '-') ?>"
              >
                <?= htmlspecialchars($p['nume_parc']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Manager (auto-completat) -->
        <div>
          <label>Manager asignat</label>
          <input type="text" id="manager_nume" readonly placeholder="Selectează un parc">
        </div>

        <div>
          <label>Email manager</label>
          <input type="text" id="manager_email" readonly>
        </div>

        <div>
          <label>Telefon manager</label>
          <input type="text" id="manager_telefon" readonly>
        </div>

        <!-- ascundem id_manager ca să ajungă în PHP -->
        <input type="hidden" name="id_manager" id="manager_id_hidden">

        <!-- Data și ora -->
        <div>
          <label for="data_programare">Data întâlnirii *</label>
          <input type="date" name="data_programare" id="data_programare" required>
        </div>

        <div>
          <label for="ora_programare">Ora întâlnirii *</label>
          <input type="time" name="ora_programare" id="ora_programare" required>
        </div>

        <!-- Observații -->
        <div class="full-width">
          <label for="observatii">Observații (opțional)</label>
          <textarea name="observatii" id="observatii" placeholder="Ex: doresc test drive, prefer dimineața etc."></textarea>
        </div>

        <button type="submit">Trimite programarea</button>
      </form>
      <?php else: ?>
        <p style="text-align:center;">Momentan nu există parcuri definite în baza de date.</p>
      <?php endif; ?>
    </div>

    <div class="logout">
      <a href="logout.php">Ieși din cont</a>
    </div>

  </div>

  <!-- SCRIPT DARK / LIGHT MODE -->
  <script>
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body;

    if (localStorage.getItem('theme') === 'light') {
      body.classList.add('light-mode');
      toggleBtn.textContent = "Dark Mode";
    }

    toggleBtn.addEventListener("click", () => {
      body.classList.toggle("light-mode");

      if (body.classList.contains("light-mode")) {
        localStorage.setItem("theme", "light");
        toggleBtn.textContent = "Dark Mode";
      } else {
        localStorage.setItem("theme", "dark");
        toggleBtn.textContent = "Light Mode";
      }
    });
  </script>

  <!-- SCRIPT pentru completarea automată a managerului -->
  <script>
    const selectParc = document.getElementById('id_parc');
    const inpNume    = document.getElementById('manager_nume');
    const inpEmail   = document.getElementById('manager_email');
    const inpTel     = document.getElementById('manager_telefon');
    const inpIdMan   = document.getElementById('manager_id_hidden');

    function updateManagerFields() {
      const opt = selectParc.options[selectParc.selectedIndex];
      if (!opt || !opt.value) {
        inpNume.value  = '';
        inpEmail.value = '';
        inpTel.value   = '';
        inpIdMan.value = '';
        return;
      }

      inpNume.value  = opt.dataset.managerNume || 'Nedefinit';
      inpEmail.value = opt.dataset.managerEmail || '-';
      inpTel.value   = opt.dataset.managerTelefon || '-';
      inpIdMan.value = opt.dataset.managerId || 0;
    }

    if (selectParc) {
      selectParc.addEventListener('change', updateManagerFields);
      // apelăm o dată la load, în caz că vrei valoare preselectată
      updateManagerFields();
    }
  </script>

</body>
</html>
