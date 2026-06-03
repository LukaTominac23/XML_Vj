const api = {
  async get(putanja, accept = "application/json") {
    const odgovor = await fetch(putanja, { headers: { Accept: accept } });
    if (!odgovor.ok) throw new Error(await odgovor.text());
    return accept.includes("xml") ? odgovor.text() : odgovor.json();
  },
  async posalji(putanja, metoda, podaci) {
    const odgovor = await fetch(putanja, {
      method: metoda,
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify(podaci)
    });
    if (!odgovor.ok) throw new Error(await odgovor.text());
    return odgovor.json();
  }
};

let trenutniIgrac = null;
let favoriti = [];

const stranice = document.querySelectorAll(".stranica");
const gumbiNavigacije = document.querySelectorAll(".gumb-navigacija");
const formaPretrage = document.querySelector("#formaPretrage");
const unosIgraca = document.querySelector("#unosIgraca");
const rezultatIgraca = document.querySelector("#rezultatIgraca");
const predlozakIgraca = document.querySelector("#predlozakIgraca");
const listaFavorita = document.querySelector("#listaFavorita");
const listaTimova = document.querySelector("#listaTimova");
const detaljiTima = document.querySelector("#detaljiTima");
const xmlPrikaz = document.querySelector("#xmlPrikaz");

function prikaziStranicu(id) {
  stranice.forEach(stranica => stranica.classList.toggle("active", stranica.id === id));
  gumbiNavigacije.forEach(gumb => gumb.classList.toggle("active", gumb.dataset.view === id));
  if (id === "timovi") ucitajTimove();
  if (id === "pocetna") ucitajDashboard();
}

function prikaziIgraca(igrac) {
  const kartica = predlozakIgraca.content.cloneNode(true);
  kartica.querySelector(".ime-js").textContent = igrac.fullName;
  kartica.querySelector(".tim-js").textContent = igrac.team;
  kartica.querySelector(".slika-js").src = `https://cdn.nba.com/headshots/nba/latest/1040x760/${igrac.id}.png`;
  kartica.querySelector(".slika-js").alt = igrac.fullName;
  kartica.querySelector(".pozicija-js").textContent = igrac.position;
  kartica.querySelector(".ppg-js").textContent = igrac.ppg;
  kartica.querySelector(".apg-js").textContent = igrac.apg;
  kartica.querySelector(".rpg-js").textContent = igrac.rpg;
  kartica.querySelector(".fg-js").textContent = `${igrac.fgPct}%`;
  kartica.querySelector(".trice-js").textContent = `${igrac.threePct}%`;

  kartica.querySelector(".dodaj-favorita").addEventListener("click", async event => {
    await api.posalji("/api/favorites", "POST", {
      playerName: igrac.fullName,
      team: igrac.team,
      position: igrac.position,
      notes: ""
    });
    event.target.textContent = "Dodano";
    await ucitajDashboard();
  });

  rezultatIgraca.innerHTML = "";
  rezultatIgraca.className = "kartica-igraca";
  rezultatIgraca.appendChild(kartica);
}

async function ucitajFavorite() {
  favoriti = await api.get("/api/favorites");
  if (!favoriti.length) {
    listaFavorita.innerHTML = "<div class='favorit-kartica'>Jos nema spremljenih igraca.</div>";
    return;
  }

  listaFavorita.innerHTML = favoriti.map(favorit => `
    <article class="favorit-kartica" data-id="${favorit.id}">
      <div class="d-flex justify-content-between favorit-red">
        <div>
          <h5>${esc(favorit.playerName)}</h5>
          <p>${esc(favorit.team)} - ${esc(favorit.position)}</p>
        </div>
        <div class="gumbi-favorita">
          <button class="btn btn-primary uredi-gumb">Uredi</button>
          <button class="btn btn-danger obrisi-gumb">Obrisi</button>
        </div>
      </div>
    </article>
  `).join("");
}

function prikaziFormuZaUredi(kartica, favorit) {
  kartica.insertAdjacentHTML("beforeend", `
    <form class="forma-uredi">
      <input class="form-control" name="playerName" value="${escAttr(favorit.playerName)}" required>
      <input class="form-control" name="team" value="${escAttr(favorit.team)}" required>
      <input class="form-control" name="position" value="${escAttr(favorit.position)}" required>
      <input type="hidden" name="notes" value="">
      <button class="btn btn-warning" type="submit">Spremi</button>
    </form>
  `);
}

async function ucitajTimove() {
  const timovi = await api.get("/api/teams");
  listaTimova.innerHTML = timovi.map(tim => `
    <button class="gumb-tim" data-id="${tim.id}">${esc(tim.name)}</button>
  `).join("");
}

function prikaziTim(tim) {
  detaljiTima.innerHTML = `
    <h2>${esc(tim.name)}</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-6"><div class="statistika"><h2>${esc(tim.city)}</h2><p>Grad</p></div></div>
      <div class="col-md-6"><div class="statistika"><h2>${esc(tim.conference)}</h2><p>Konferencija</p></div></div>
      <div class="col-md-6"><div class="statistika"><h2>${esc(tim.division)}</h2><p>Divizija</p></div></div>
      <div class="col-md-6"><div class="statistika"><h2>${esc(tim.arena)}</h2><p>Arena</p></div></div>
    </div>
  `;
}

async function ucitajDashboard() {
  const dashboard = await api.get("/api/dashboard");
  document.querySelector("#brojFavorita").textContent = dashboard.favoritePlayers;
  document.querySelector("#brojTimova").textContent = dashboard.favoriteTeams;
  document.querySelector("#prosjekPpg").textContent = dashboard.averagePpg;
  await ucitajFavorite();
}

function esc(vrijednost) {
  return String(vrijednost).replace(/[&<>"']/g, znak => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;"
  })[znak]);
}

function escAttr(vrijednost) {
  return esc(vrijednost).replace(/`/g, "&#96;");
}

gumbiNavigacije.forEach(gumb => gumb.addEventListener("click", () => prikaziStranicu(gumb.dataset.view)));

formaPretrage.addEventListener("submit", async event => {
  event.preventDefault();
  rezultatIgraca.className = "kartica-igraca";
  rezultatIgraca.textContent = "Dohvacam NBA podatke...";
  try {
    trenutniIgrac = await api.get(`/api/search?name=${encodeURIComponent(unosIgraca.value)}`);
    prikaziIgraca(trenutniIgrac);
  } catch {
    rezultatIgraca.textContent = "Igrac nije pronaden. Probaj Luka Doncic, Stephen Curry ili Paul George.";
  }
});

listaFavorita.addEventListener("click", async event => {
  const kartica = event.target.closest(".favorit-kartica");
  if (!kartica) return;
  const favorit = favoriti.find(item => item.id === Number(kartica.dataset.id));
  if (!favorit) return;

  if (event.target.classList.contains("obrisi-gumb")) {
    await api.posalji(`/api/favorites/${favorit.id}`, "DELETE", {});
    await ucitajDashboard();
  }

  if (event.target.classList.contains("uredi-gumb") && !kartica.querySelector(".forma-uredi")) {
    prikaziFormuZaUredi(kartica, favorit);
  }
});

listaFavorita.addEventListener("submit", async event => {
  event.preventDefault();
  const forma = event.target;
  const kartica = forma.closest(".favorit-kartica");
  const podaci = Object.fromEntries(new FormData(forma).entries());
  await api.posalji(`/api/favorites/${kartica.dataset.id}`, "PUT", podaci);
  await ucitajDashboard();
});

listaTimova.addEventListener("click", async event => {
  const gumb = event.target.closest(".gumb-tim");
  if (!gumb) return;
  document.querySelectorAll(".gumb-tim").forEach(item => item.classList.toggle("active", item === gumb));
  prikaziTim(await api.get(`/api/teams/${gumb.dataset.id}`));
});

document.querySelector("#xmlGumb").addEventListener("click", async () => {
  xmlPrikaz.textContent = await api.get("/api/favorites", "application/xml");
  xmlPrikaz.classList.toggle("sakriveno");
});

document.querySelector(".dodaj-favorita").addEventListener("click", async event => {
  await api.posalji("/api/favorites", "POST", {
    playerName: "Luka Doncic",
    team: "Los Angeles Lakers",
    position: "PG",
    notes: ""
  });
  event.target.textContent = "Dodano";
  await ucitajDashboard();
});

ucitajDashboard();
