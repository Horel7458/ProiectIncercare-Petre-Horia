<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Catalog de Studenți</title>
  <style>
    body{margin:0;font-family:Arial,sans-serif;background:linear-gradient(135deg,#0f0f10,#20232a);color:#fff;min-height:100vh;padding:30px;}
    .wrap{max-width:900px;margin:0 auto;}
    .card{background:rgba(255,255,255,.08);border-radius:16px;padding:18px;margin-bottom:16px;}
    h1{margin:0 0 14px;}
    label{display:block;font-size:13px;margin:10px 0 4px;}
    input,select{width:100%;padding:10px;border:none;border-radius:10px;box-sizing:border-box;}
    button{margin-top:12px;width:100%;padding:10px;border:none;border-radius:10px;cursor:pointer;background:#10b981;color:#fff;font-weight:bold;}
    button:hover{background:#059669;}
    table{width:100%;border-collapse:collapse;overflow:hidden;border-radius:12px;}
    th,td{padding:10px;text-align:left;border-bottom:1px solid rgba(255,255,255,.12);}
    th{background:rgba(0,0,0,.35);}
    .msg{margin-top:10px;padding:10px;border-radius:10px;font-size:14px;display:none;}
    .msg.ok{background:rgba(22,163,74,.25);border:1px solid #4ade80;}
    .msg.err{background:rgba(220,38,38,.25);border:1px solid #f87171;}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    @media(max-width:700px){.row{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Catalog de Studenți</h1>

      <form id="formStudent">
        <label>Nume student</label>
        <input type="text" id="nume" required placeholder="Ex: Popescu Andrei">

        <div class="row">
          <div>
            <label>An de studiu (1–4)</label>
            <select id="an_studiu" required>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </div>
          <div>
            <label>Media (0–10)</label>
            <input type="number" id="media" step="0.01" min="0" max="10" required placeholder="Ex: 9.35">
          </div>
        </div>

        <button type="submit">Adaugă student</button>
        <div id="msg" class="msg"></div>
      </form>
    </div>

    <div class="card">
      <h2 style="margin:0 0 10px;">Lista de studenți</h2>
      <table>
        <thead>
          <tr>
            <th>Nume</th>
            <th>An</th>
            <th>Media</th>
          </tr>
        </thead>
        <tbody id="tbodyStudenti">
          <tr><td colspan="3">Se încarcă...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

<script>
const apiUrl = "api_studenti.php";

const tbody = document.getElementById("tbodyStudenti");
const form  = document.getElementById("formStudent");
const msgEl = document.getElementById("msg");

function showMsg(text, ok=true){
  msgEl.style.display = "block";
  msgEl.className = "msg " + (ok ? "ok" : "err");
  msgEl.textContent = text;
  setTimeout(() => { msgEl.style.display = "none"; }, 2500);
}

async function incarcaStudenti() {
  const res = await fetch(apiUrl);
  const json = await res.json();

  if (!json.success) {
    tbody.innerHTML = `<tr><td colspan="3">Eroare la încărcare.</td></tr>`;
    return;
  }

  const studenti = json.data;
  if (studenti.length === 0) {
    tbody.innerHTML = `<tr><td colspan="3">Nu există studenți încă.</td></tr>`;
    return;
  }

  tbody.innerHTML = studenti.map(s => `
    <tr>
      <td>${escapeHtml(s.nume)}</td>
      <td>${escapeHtml(String(s.an_studiu))}</td>
      <td>${escapeHtml(String(s.media))}</td>
    </tr>
  `).join("");
}

function escapeHtml(str){
  return str.replace(/[&<>"']/g, m => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
  }[m]));
}

form.addEventListener("submit", async (e) => {
  e.preventDefault(); // fără refresh

  const payload = {
    nume: document.getElementById("nume").value.trim(),
    an_studiu: parseInt(document.getElementById("an_studiu").value, 10),
    media: parseFloat(document.getElementById("media").value)
  };

  const res = await fetch(apiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });

  const json = await res.json();

  if (json.success) {
    showMsg("Student adăugat cu succes!", true);
    form.reset();
    document.getElementById("an_studiu").value = "1";
    await incarcaStudenti(); // actualizare listă în timp real
  } else {
    showMsg(json.message || "Eroare.", false);
  }
});

// la încărcarea paginii
incarcaStudenti();
</script>
</body>
</html>
