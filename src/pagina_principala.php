<?php
session_start();
if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header("Location: index.php");
    exit;
}

require 'config.php';

$mesaj_succes = '';
$mesaj_eroare = '';

// 1) Procesăm programarea
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

// 2) Luăm parcurile + managerii lor
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

  <!-- Buton Top (ai zis că îl ai deja) -->
  <link rel="stylesheet" href="gotop.css?v=2">

  <style>
    /*  Setări generale + tipografie
       */
    :root{
      --c-accent:#7fd1ff;
      --c-bg: rgba(0,0,0,.55);
      --c-card: rgba(255,255,255,.10);
      --c-card2: rgba(255,255,255,.08);
      --c-border: rgba(255,255,255,.14);
      --c-green: #10b981;
      --c-green2:#059669;
      --c-blue:#007bff;
      --c-blue2:#0056b3;
      --c-text:#fff;
      --radius: 16px;
    }

    html{ scroll-behavior: smooth; }

    body{
      margin:0;
      font-family: Arial, sans-serif;
      color: var(--c-text);
      background: url('audi.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height:100vh;
      line-height: 1.5;
      font-size: 1rem; /* ~16px */
    }

    /* 
      Layout cerut: header/nav/main/aside/footer
        */
    .page{
      max-width: 1650px;
      margin: 0 auto;
      padding: 16px;
      display: grid;
      gap: 20px;
      grid-template-columns: 280px 1fr 340px;
      grid-template-areas:
        "header header header"
        "nav    main   aside"
        "footer footer footer";
      align-items: start;
    }

    header{
      grid-area: header;
      background: var(--c-bg);
      border: 1px solid var(--c-border);
      border-radius: var(--radius);
      padding: 16px;
      text-align: center;
      box-shadow: 0 18px 40px rgba(0,0,0,.35);
    }

    nav{
      grid-area: nav;
      background: var(--c-bg);
      border: 1px solid var(--c-border);
      border-radius: var(--radius);
      padding: 16px;
      box-shadow: 0 18px 40px rgba(0,0,0,.30);
    }

    main{
      grid-area: main;
      background: var(--c-bg);
      border: 1px solid var(--c-border);
      border-radius: var(--radius);
      padding: 18px;
      box-shadow: 0 18px 40px rgba(0,0,0,.30);
      text-align:center;
    }

    aside{
      grid-area: aside;
      background: var(--c-bg);
      border: 1px solid var(--c-border);
      border-radius: var(--radius);
      padding: 16px;
      box-shadow: 0 18px 40px rgba(0,0,0,.30);
      text-align:left;
    }

    footer{
      grid-area: footer;
      background: var(--c-bg);
      border: 1px solid var(--c-border);
      border-radius: var(--radius);
      padding: 14px 16px;
      text-align:center;
      box-shadow: 0 18px 40px rgba(0,0,0,.25);
    }

    /*
        Header
      */
    .h-title{
      margin: 0 0 6px;
      font-size: 1.7rem;
      letter-spacing:.2px;
    }
    .h-sub{
      margin:0;
      opacity:.95;
      font-size: 1rem;
    }

    /* 
        Nav 
       */
    .nav-title{
      margin:0 0 10px;
      font-size: 1.1rem;
      text-align:center;
    }
    .nav-links{
      display:flex;
      flex-direction:column;
      gap:10px;
    }
    .nav-links a{
      display:block;
      text-decoration:none;
      color: var(--c-accent);
      border: 1px solid rgba(127,209,255,.45);
      background: rgba(0,0,0,.20);
      padding: 10px 12px;
      border-radius: 12px;
      transition:.2s;
      text-align:center;
      font-weight:bold;
    }
    .nav-links a:hover{
      background: rgba(127,209,255,.12);
      transform: translateY(-1px);
    }

    /* 
        Secțiuni din main
       */
    .section-title{
      margin: 8px 0 10px;
      font-size: 1.25rem;
    }

    .intro{
      margin: 0 auto 18px;
      max-width: 900px;
      opacity: .95;
      line-height: 1.6;
    }

    /* 
        CARDURI Produse (Flexbox cerut)
       */
    .cards-flex{
      display:flex;
      justify-content:center;
      align-items:stretch;
      flex-wrap: wrap;
      gap: 32px;
      margin: 12px 0 26px;
    }

    /* Masini: fix 3 coloane  */
    .cars-grid{
      display: grid;
      grid-template-columns: repeat(3, 270px);
      gap: 32px;
      justify-content: center;
      align-items: stretch;
    }

    /* Parcuri: fix 3 coloane */
    .parks-grid{
      display: grid;
      grid-template-columns: repeat(3, 270px);
      gap: 32px;
      justify-content: center;
      align-items: stretch;
    }

    .card{
      width: 270px;
      background: var(--c-card);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: 16px;
      padding: 12px;
      transition: transform .25s, background .25s;
      text-align:center;
      position: relative;
    }
    .card:hover{
      transform: scale(1.04);
      background: rgba(255,255,255,.16);
      z-index: 2;
    }

    .card img{
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 12px;
      display:block;
    }

    .card h3, .card h4{
      margin: 10px 0 10px;
    }

    .btn{
      border:none;
      padding: 10px 14px;
      border-radius: 10px;
      cursor:pointer;
      font-weight:bold;
      transition:.2s;
    }

    .btn.blue{
      background: var(--c-blue);
      color:#fff;
    }
    .btn.blue:hover{ background: var(--c-blue2); }

    /* parcurile */
    .park-link{
      text-decoration:none;
      color: inherit;
      display:block;
    }

    /* 
        Aside - Programare
        */
    .aside-title{
      margin: 0 0 10px;
      font-size: 1.15rem;
      text-align:center;
    }

    .msg{
      margin: 10px 0 12px;
      padding: 10px;
      border-radius: 12px;
      font-size: .95rem;
      text-align:center;
    }
    .msg.success{
      background: rgba(22,163,74,.22);
      border: 1px solid rgba(74,222,128,.75);
    }
    .msg.error{
      background: rgba(220,38,38,.22);
      border: 1px solid rgba(248,113,113,.75);
    }

    .appointment-form{
      display:flex;
      flex-direction:column;
      gap: 10px;
    }
    .appointment-form label{
      font-size: .9rem;
      opacity: .95;
      margin-bottom: 4px;
      display:block;
    }
    .appointment-form select,
    .appointment-form input,
    .appointment-form textarea{
      width:100%;
      padding: 10px 10px;
      border-radius: 12px;
      border: none;
      box-sizing: border-box;
    }
    .appointment-form textarea{
      min-height: 80px;
      resize: vertical;
    }
    .appointment-form button{
      background: var(--c-green);
      color:#fff;
      font-size: 1rem;
      font-weight:bold;
      padding: 11px 12px;
      border:none;
      border-radius: 12px;
      cursor:pointer;
      transition:.2s;
      margin-top: 4px;
    }
    .appointment-form button:hover{ background: var(--c-green2); }

    /* 
        Footer links
       */
    .footer-links a{
      color: var(--c-accent);
      text-decoration:none;
      font-weight:bold;
    }
    .footer-links a:hover{ text-decoration: underline; }

    /*
        Light mode (păstrat)
        */
    .light-mode{
      background: #f3f3f3 !important;
      color:#111 !important;
    }
    .light-mode header,
    .light-mode nav,
    .light-mode main,
    .light-mode aside,
    .light-mode footer{
      background: rgba(255,255,255,.85) !important;
      color:#111 !important;
      border-color: rgba(0,0,0,.10) !important;
    }
    .light-mode .card{
      background: rgba(0,0,0,.04) !important;
      border-color: rgba(0,0,0,.08) !important;
      color:#111 !important;
    }
    .light-mode .nav-links a{
      background: rgba(0,0,0,.04);
      border-color: rgba(0,0,0,.12);
      color:#005bbb;
    }

    /* Buton schimbare temă */
    #themeToggle{
      position: fixed;
      top: 18px;
      right: 18px;
      padding: 8px 14px;
      border-radius: 12px;
      border:none;
      cursor:pointer;
      background: var(--c-accent);
      color:#000;
      font-weight:bold;
      box-shadow: 0 0 10px rgba(0,0,0,.25);
      z-index: 99999;
      transition:.2s;
    }
    #themeToggle:hover{ transform: scale(1.05); }

    /* 
        RESPONSIVE: 900px
      */
    @media (max-width: 900px){
      body{ font-size: .95rem; }

      .page{
        grid-template-columns: 1fr;
        grid-template-areas:
          "header"
          "nav"
          "main"
          "aside"
          "footer";
      }

      /* cardurile devin mai late  */
      .card{ width: min(360px, 100%); margin: 0 auto; }

      .cars-grid{
        grid-template-columns: 1fr;
      }

      .parks-grid{
        grid-template-columns: 1fr;
      }

      /* imaginile cerință: max-width 100% + height auto pe mobil */
      .card img{
        max-width: 100%;
        height: auto;
      }

      header{ text-align:center; }
      .h-title{ font-size: 1.45rem; }
      .h-sub{ font-size: 1rem; }

      .intro{ font-size: 1rem; line-height: 1.65; }
    }
  </style>
</head>

<body>

  <!-- Buton Dark/Light -->
  <button id="themeToggle">Light Mode</button>

  <div class="page">

    <!-- HEADER -->
    <header id="top">
      <h1 class="h-title">Bun venit în parcul auto!</h1>
      <p class="h-sub">Pentru o ofertă mai amplă de mașini, accesează parcurile noastre auto partenere.</p>
    </header>

    <!-- NAV -->
    <nav>
      <div class="nav-title">Meniu</div>
      <div class="nav-links">
        <a href="profil.php">Profil</a>
        <a href="#masini">Mașini</a>
        <a href="#parcuri">Parcuri partenere</a>
        <a href="#programare">Programare</a>
        <a href="logout.php">Ieși din cont</a>
      </div>
    </nav>

    <!-- MAIN -->
    <main>
      <!-- PRODUSE (masini) - FLEXBOX cerut -->
      <h2 class="section-title" id="masini">Mașini disponibile</h2>

      <div class="cards-flex cars-grid">
        <div class="card">
          <img src="audi.jpg" alt="Audi RS6">
          <h3>Audi RS6</h3>
          <button class="btn blue" onclick="window.location.href='detalii_audi.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="bmw.jpg" alt="BMW M4 Competition">
          <h3>BMW M4 Competition</h3>
          <button class="btn blue" onclick="window.location.href='detalii_bmw.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="bugatti.jpg" alt="Bugatti Chiron" onerror="this.onerror=null;this.src='bugatti_chiron.svg';">
          <h3>Bugatti Chiron</h3>
          <button class="btn blue" onclick="window.location.href='detalii_bugatti.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="mercedes.jpg" alt="Mercedes-AMG GT">
          <h3>Mercedes-AMG GT</h3>
          <button class="btn blue" onclick="window.location.href='detalii_mercedes.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="porsche.jpg" alt="Porsche 911 Turbo S">
          <h3>Porsche 911 Turbo S</h3>
          <button class="btn blue" onclick="window.location.href='detalii_porsche.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="laferrari.jpg" alt="Ferrari LaFerrari" onerror="this.onerror=null;this.src='ferrari_laferrari.svg';">
          <h3>Ferrari LaFerrari</h3>
          <button class="btn blue" onclick="window.location.href='detalii_laferrari.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="lamborghini.jpg" alt="Lamborghini Huracán">
          <h3>Lamborghini Huracán</h3>
          <button class="btn blue" onclick="window.location.href='detalii_lamborghini.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="ferrari.jpg" alt="Ferrari F8 Tributo">
          <h3>Ferrari F8 Tributo</h3>
          <button class="btn blue" onclick="window.location.href='detalii_ferrari.php'">Vezi detalii</button>
        </div>

        <div class="card">
          <img src="audir8.jpg" alt="Audi R8" onerror="this.onerror=null;this.src='audi_r8.svg';">
          <h3>Audi R8</h3>
          <button class="btn blue" onclick="window.location.href='detalii_audi_r8.php'">Vezi detalii</button>
        </div>
      </div>

      <!-- PARCURI - tot flexbox -->
      <h2 class="section-title" id="parcuri">Parcuri auto partenere</h2>

      <div class="cards-flex parks-grid">
        <a class="park-link" href="parc_titan.php">
          <div class="card">
            <img src="parc_titan.jpg" alt="AutoPark Titan">
            <h4>AutoPark Titan</h4>
          </div>
        </a>

        <a class="park-link" href="parc_baneasa.php">
          <div class="card">
            <img src="parc_baneasa.jpg" alt="AutoPark Băneasa">
            <h4>AutoPark Băneasa</h4>
          </div>
        </a>

        <a class="park-link" href="parc_militari.php">
          <div class="card">
            <img src="parc_militari.jpg" alt="AutoPark Militari">
            <h4>AutoPark Militari</h4>
          </div>
        </a>

        <a class="park-link" href="parc_otopeni.php">
          <div class="card">
            <img src="parc_otopeni.jpg" alt="AutoPark Otopeni">
            <h4>AutoPark Otopeni</h4>
          </div>
        </a>

        <a class="park-link" href="parc_pipera.php">
          <div class="card">
            <img src="parc_pipera.jpg" alt="AutoPark Pipera">
            <h4>AutoPark Pipera</h4>
          </div>
        </a>

        <a class="park-link" href="parc_constanta.php">
          <div class="card">
            <img src="parc_constanta.jpg" alt="AutoPark Constanța">
            <h4>AutoPark Constanța</h4>
          </div>
        </a>
      </div>
    </main>

    <!-- ASIDE (Programare) -->
    <aside id="programare">
      <h2 class="aside-title">Programează o întâlnire</h2>

      <?php if ($mesaj_succes): ?>
        <div class="msg success"><?= htmlspecialchars($mesaj_succes) ?></div>
      <?php endif; ?>

      <?php if ($mesaj_eroare): ?>
        <div class="msg error"><?= htmlspecialchars($mesaj_eroare) ?></div>
      <?php endif; ?>

      <?php if (!empty($parcuri)): ?>
        <form method="post" class="appointment-form">
          <input type="hidden" name="programare_form" value="1">

          <div>
            <label for="id_parc">Alege parcul *</label>
            <select name="id_parc" id="id_parc" required>
              <option value="">-- Selectează un parc --</option>
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

          <input type="hidden" name="id_manager" id="manager_id_hidden">

          <div>
            <label for="data_programare">Data întâlnirii *</label>
            <input type="date" name="data_programare" id="data_programare" required>
          </div>

          <div>
            <label for="ora_programare">Ora întâlnirii *</label>
            <input type="time" name="ora_programare" id="ora_programare" required>
          </div>

          <div>
            <label for="observatii">Observații</label>
            <textarea name="observatii" id="observatii" placeholder="Ex: doresc test drive, prefer dimineața..."></textarea>
          </div>

          <button type="submit">Trimite programarea</button>
        </form>
      <?php else: ?>
        <p>Momentan nu există parcuri definite în baza de date.</p>
      <?php endif; ?>
    </aside>

    <!-- FOOTER -->
    <footer>
      <div class="footer-links">
        <a href="profil.php">Profil</a> •
        <a href="manager_login.php">Intrare Manager</a> •
        <a href="logout.php">Logout</a>
      </div>
      <div style="opacity:.9;margin-top:6px;font-size:.95rem;">
        © <?= date('Y') ?> Parc Auto • Proiect TW
      </div>
    </footer>

  </div>

  <!-- DARK / LIGHT MODE -->
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

  <!-- Autocomplete manager în funcție de parc -->
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
      updateManagerFields();
    }
  </script>

  <!-- Buton Top (JS) -->
  <script src="gotop.js"></script>
</body>
</html>
