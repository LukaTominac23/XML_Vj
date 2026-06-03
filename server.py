from http.server import ThreadingHTTPServer, SimpleHTTPRequestHandler
from urllib.parse import parse_qs, urlparse
import json
import os
import sqlite3
import xml.etree.ElementTree as ET


MAPA_PROJEKTA = os.path.dirname(os.path.abspath(__file__))
BAZA = os.path.join(MAPA_PROJEKTA, "nba_tracker.db")


IGRACI = {
    "luka doncic": {"id": 1629029, "fullName": "Luka Doncic", "team": "Los Angeles Lakers", "position": "PG", "ppg": 28.2, "apg": 8.2, "rpg": 8.2, "fgPct": 45.0, "threePct": 36.8},
    "stephen curry": {"id": 201939, "fullName": "Stephen Curry", "team": "Golden State Warriors", "position": "PG", "ppg": 24.5, "apg": 6.0, "rpg": 4.4, "fgPct": 44.8, "threePct": 40.8},
    "nikola jokic": {"id": 203999, "fullName": "Nikola Jokic", "team": "Denver Nuggets", "position": "C", "ppg": 29.6, "apg": 10.2, "rpg": 12.7, "fgPct": 57.6, "threePct": 41.7},
    "giannis antetokounmpo": {"id": 203507, "fullName": "Giannis Antetokounmpo", "team": "Milwaukee Bucks", "position": "PF", "ppg": 30.4, "apg": 6.5, "rpg": 11.9, "fgPct": 60.1, "threePct": 22.2},
    "lebron james": {"id": 2544, "fullName": "LeBron James", "team": "Los Angeles Lakers", "position": "SF", "ppg": 24.4, "apg": 8.2, "rpg": 7.8, "fgPct": 51.3, "threePct": 37.6},
    "jayson tatum": {"id": 1628369, "fullName": "Jayson Tatum", "team": "Boston Celtics", "position": "SF", "ppg": 26.8, "apg": 6.0, "rpg": 8.7, "fgPct": 45.2, "threePct": 34.3},
    "paul george": {"id": 202331, "fullName": "Paul George", "team": "Philadelphia 76ers", "position": "SF", "ppg": 17.3, "apg": 3.6, "rpg": 5.3, "fgPct": 43.0, "threePct": 38.0},
    "tyrese maxey": {"id": 1630178, "fullName": "Tyrese Maxey", "team": "Philadelphia 76ers", "position": "PG", "ppg": 28.3, "apg": 6.6, "rpg": 4.1, "fgPct": 46.0, "threePct": 38.0},
    "kevin durant": {"id": 201142, "fullName": "Kevin Durant", "team": "Houston Rockets", "position": "PF", "ppg": 26.6, "apg": 4.2, "rpg": 6.0, "fgPct": 52.0, "threePct": 41.0},
    "anthony edwards": {"id": 1630162, "fullName": "Anthony Edwards", "team": "Minnesota Timberwolves", "position": "SG", "ppg": 27.6, "apg": 4.5, "rpg": 5.7, "fgPct": 45.0, "threePct": 39.0},
    "shai gilgeous-alexander": {"id": 1628983, "fullName": "Shai Gilgeous-Alexander", "team": "Oklahoma City Thunder", "position": "PG", "ppg": 32.7, "apg": 6.4, "rpg": 5.0, "fgPct": 52.0, "threePct": 37.0},
    "joel embiid": {"id": 203954, "fullName": "Joel Embiid", "team": "Philadelphia 76ers", "position": "C", "ppg": 23.8, "apg": 4.5, "rpg": 8.2, "fgPct": 45.0, "threePct": 30.0},
    "devin booker": {"id": 1626164, "fullName": "Devin Booker", "team": "Phoenix Suns", "position": "SG", "ppg": 25.6, "apg": 7.1, "rpg": 4.1, "fgPct": 46.0, "threePct": 36.0},
    "ja morant": {"id": 1629630, "fullName": "Ja Morant", "team": "Memphis Grizzlies", "position": "PG", "ppg": 23.2, "apg": 7.3, "rpg": 4.1, "fgPct": 45.0, "threePct": 31.0},
    "victor wembanyama": {"id": 1641705, "fullName": "Victor Wembanyama", "team": "San Antonio Spurs", "position": "C", "ppg": 24.3, "apg": 3.7, "rpg": 11.0, "fgPct": 47.0, "threePct": 35.0},
    "donovan mitchell": {"id": 1628378, "fullName": "Donovan Mitchell", "team": "Cleveland Cavaliers", "position": "SG", "ppg": 24.0, "apg": 5.0, "rpg": 4.5, "fgPct": 44.0, "threePct": 37.0},
}


TIMOVI = [
    {"id": 1610612737, "name": "Atlanta Hawks", "city": "Atlanta", "conference": "East", "division": "Southeast", "arena": "State Farm Arena"},
    {"id": 1610612738, "name": "Boston Celtics", "city": "Boston", "conference": "East", "division": "Atlantic", "arena": "TD Garden"},
    {"id": 1610612751, "name": "Brooklyn Nets", "city": "Brooklyn", "conference": "East", "division": "Atlantic", "arena": "Barclays Center"},
    {"id": 1610612766, "name": "Charlotte Hornets", "city": "Charlotte", "conference": "East", "division": "Southeast", "arena": "Spectrum Center"},
    {"id": 1610612741, "name": "Chicago Bulls", "city": "Chicago", "conference": "East", "division": "Central", "arena": "United Center"},
    {"id": 1610612739, "name": "Cleveland Cavaliers", "city": "Cleveland", "conference": "East", "division": "Central", "arena": "Rocket Arena"},
    {"id": 1610612742, "name": "Dallas Mavericks", "city": "Dallas", "conference": "West", "division": "Southwest", "arena": "American Airlines Center"},
    {"id": 1610612743, "name": "Denver Nuggets", "city": "Denver", "conference": "West", "division": "Northwest", "arena": "Ball Arena"},
    {"id": 1610612765, "name": "Detroit Pistons", "city": "Detroit", "conference": "East", "division": "Central", "arena": "Little Caesars Arena"},
    {"id": 1610612744, "name": "Golden State Warriors", "city": "San Francisco", "conference": "West", "division": "Pacific", "arena": "Chase Center"},
    {"id": 1610612745, "name": "Houston Rockets", "city": "Houston", "conference": "West", "division": "Southwest", "arena": "Toyota Center"},
    {"id": 1610612754, "name": "Indiana Pacers", "city": "Indianapolis", "conference": "East", "division": "Central", "arena": "Gainbridge Fieldhouse"},
    {"id": 1610612746, "name": "LA Clippers", "city": "Inglewood", "conference": "West", "division": "Pacific", "arena": "Intuit Dome"},
    {"id": 1610612747, "name": "Los Angeles Lakers", "city": "Los Angeles", "conference": "West", "division": "Pacific", "arena": "Crypto.com Arena"},
    {"id": 1610612763, "name": "Memphis Grizzlies", "city": "Memphis", "conference": "West", "division": "Southwest", "arena": "FedExForum"},
    {"id": 1610612748, "name": "Miami Heat", "city": "Miami", "conference": "East", "division": "Southeast", "arena": "Kaseya Center"},
    {"id": 1610612749, "name": "Milwaukee Bucks", "city": "Milwaukee", "conference": "East", "division": "Central", "arena": "Fiserv Forum"},
    {"id": 1610612750, "name": "Minnesota Timberwolves", "city": "Minneapolis", "conference": "West", "division": "Northwest", "arena": "Target Center"},
    {"id": 1610612740, "name": "New Orleans Pelicans", "city": "New Orleans", "conference": "West", "division": "Southwest", "arena": "Smoothie King Center"},
    {"id": 1610612752, "name": "New York Knicks", "city": "New York", "conference": "East", "division": "Atlantic", "arena": "Madison Square Garden"},
    {"id": 1610612760, "name": "Oklahoma City Thunder", "city": "Oklahoma City", "conference": "West", "division": "Northwest", "arena": "Paycom Center"},
    {"id": 1610612753, "name": "Orlando Magic", "city": "Orlando", "conference": "East", "division": "Southeast", "arena": "Kia Center"},
    {"id": 1610612755, "name": "Philadelphia 76ers", "city": "Philadelphia", "conference": "East", "division": "Atlantic", "arena": "Wells Fargo Center"},
    {"id": 1610612756, "name": "Phoenix Suns", "city": "Phoenix", "conference": "West", "division": "Pacific", "arena": "Footprint Center"},
    {"id": 1610612757, "name": "Portland Trail Blazers", "city": "Portland", "conference": "West", "division": "Northwest", "arena": "Moda Center"},
    {"id": 1610612758, "name": "Sacramento Kings", "city": "Sacramento", "conference": "West", "division": "Pacific", "arena": "Golden 1 Center"},
    {"id": 1610612759, "name": "San Antonio Spurs", "city": "San Antonio", "conference": "West", "division": "Southwest", "arena": "Frost Bank Center"},
    {"id": 1610612761, "name": "Toronto Raptors", "city": "Toronto", "conference": "East", "division": "Atlantic", "arena": "Scotiabank Arena"},
    {"id": 1610612762, "name": "Utah Jazz", "city": "Salt Lake City", "conference": "West", "division": "Northwest", "arena": "Delta Center"},
    {"id": 1610612764, "name": "Washington Wizards", "city": "Washington", "conference": "East", "division": "Southeast", "arena": "Capital One Arena"},
]


def normaliziraj(tekst):
    return " ".join((tekst or "").lower().strip().split())


def pripremi_bazu():
    with sqlite3.connect(BAZA) as veza:
        veza.execute(
            """
            CREATE TABLE IF NOT EXISTS FavoritePlayers (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                PlayerName TEXT NOT NULL,
                Team TEXT NOT NULL,
                Position TEXT NOT NULL,
                Notes TEXT DEFAULT ''
            )
            """
        )


def favoriti_iz_redova(redovi):
    return [
        {"id": red[0], "playerName": red[1], "team": red[2], "position": red[3], "notes": red[4]}
        for red in redovi
    ]


def xml_element(naziv, podatak):
    element = ET.Element(naziv)
    for kljuc, vrijednost in podatak.items():
        dijete = ET.Element(kljuc[:1].upper() + kljuc[1:])
        dijete.text = "" if vrijednost is None else str(vrijednost)
        element.append(dijete)
    return element


def napravi_xml(podaci, naziv="Response"):
    if isinstance(podaci, list):
        korijen = ET.Element(f"ArrayOf{naziv}")
        for podatak in podaci:
            korijen.append(xml_element(naziv[:-1] if naziv.endswith("s") else "Item", podatak))
    else:
        korijen = xml_element(naziv, podaci)
    return ET.tostring(korijen, encoding="utf-8", xml_declaration=True)


class NbaServer(SimpleHTTPRequestHandler):
    def zapisi_zahtjev(self, metoda):
        print(f"{metoda} {self.path}", flush=True)

    def end_headers(self):
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type, Accept")
        super().end_headers()

    def do_OPTIONS(self):
        self.send_response(204)
        self.end_headers()

    def posalji(self, podaci, status=200, naziv="Response"):
        query = parse_qs(urlparse(self.path).query)
        trazi_xml = "application/xml" in self.headers.get("Accept", "") or query.get("format") == ["xml"]
        tijelo = napravi_xml(podaci, naziv) if trazi_xml else json.dumps(podaci, ensure_ascii=False).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/xml; charset=utf-8" if trazi_xml else "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(tijelo)))
        self.end_headers()
        self.wfile.write(tijelo)

    def procitaj_json(self):
        duljina = int(self.headers.get("Content-Length", "0"))
        if duljina == 0:
            return {}
        return json.loads(self.rfile.read(duljina).decode("utf-8"))

    def do_GET(self):
        self.zapisi_zahtjev("GET")
        parsed = urlparse(self.path)
        putanja = parsed.path
        query = parse_qs(parsed.query)

        if putanja == "/api/search":
            ime = normaliziraj(query.get("name", [""])[0])
            igrac = IGRACI.get(ime)
            if not igrac:
                self.posalji({"message": "Igrac nije pronaden"}, 404)
                return
            self.posalji(igrac, naziv="Player")
            return

        if putanja == "/api/favorites":
            with sqlite3.connect(BAZA) as veza:
                redovi = veza.execute("SELECT Id, PlayerName, Team, Position, Notes FROM FavoritePlayers ORDER BY Id DESC").fetchall()
            self.posalji(favoriti_iz_redova(redovi), naziv="FavoritePlayers")
            return

        if putanja == "/api/teams":
            self.posalji(TIMOVI, naziv="Teams")
            return

        if putanja.startswith("/api/teams/"):
            team_id = int(putanja.split("/")[-1])
            tim = next((tim for tim in TIMOVI if tim["id"] == team_id), None)
            self.posalji(tim if tim else {"message": "Tim nije pronaden"}, 200 if tim else 404, naziv="Team")
            return

        if putanja == "/api/dashboard":
            with sqlite3.connect(BAZA) as veza:
                favoriti = favoriti_iz_redova(veza.execute("SELECT Id, PlayerName, Team, Position, Notes FROM FavoritePlayers").fetchall())
            broj_timova = len({favorit["team"] for favorit in favoriti})
            poeni = [IGRACI.get(normaliziraj(favorit["playerName"]), {}).get("ppg", 0) for favorit in favoriti]
            prosjek = round(sum(poeni) / len(poeni), 1) if poeni else 0
            self.posalji({"favoritePlayers": len(favoriti), "favoriteTeams": broj_timova, "averagePpg": prosjek}, naziv="Dashboard")
            return

        return super().do_GET()

    def do_POST(self):
        self.zapisi_zahtjev("POST")
        if self.path != "/api/favorites":
            self.posalji({"message": "Ruta ne postoji"}, 404)
            return
        podatak = self.procitaj_json()
        with sqlite3.connect(BAZA) as veza:
            cursor = veza.execute(
                "INSERT INTO FavoritePlayers (PlayerName, Team, Position, Notes) VALUES (?, ?, ?, ?)",
                (podatak.get("playerName", ""), podatak.get("team", ""), podatak.get("position", ""), podatak.get("notes", "")),
            )
        podatak["id"] = cursor.lastrowid
        self.posalji(podatak, 201, naziv="FavoritePlayer")

    def do_PUT(self):
        self.zapisi_zahtjev("PUT")
        if not self.path.startswith("/api/favorites/"):
            self.posalji({"message": "Ruta ne postoji"}, 404)
            return
        favorit_id = int(self.path.split("/")[-1])
        podatak = self.procitaj_json()
        with sqlite3.connect(BAZA) as veza:
            veza.execute(
                "UPDATE FavoritePlayers SET PlayerName = ?, Team = ?, Position = ?, Notes = ? WHERE Id = ?",
                (podatak.get("playerName", ""), podatak.get("team", ""), podatak.get("position", ""), podatak.get("notes", ""), favorit_id),
            )
        podatak["id"] = favorit_id
        self.posalji(podatak, naziv="FavoritePlayer")

    def do_DELETE(self):
        self.zapisi_zahtjev("DELETE")
        if not self.path.startswith("/api/favorites/"):
            self.posalji({"message": "Ruta ne postoji"}, 404)
            return
        favorit_id = int(self.path.split("/")[-1])
        with sqlite3.connect(BAZA) as veza:
            veza.execute("DELETE FROM FavoritePlayers WHERE Id = ?", (favorit_id,))
        self.posalji({"deleted": True, "id": favorit_id}, naziv="DeleteResult")


if __name__ == "__main__":
    pripremi_bazu()
    os.chdir(MAPA_PROJEKTA)
    server = ThreadingHTTPServer(("127.0.0.1", 8001), NbaServer)
    print("NBA Stats Tracker radi na http://127.0.0.1:8001")
    server.serve_forever()
