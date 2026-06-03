# NBA Stats Tracker

Ovo je jednostavnija verzija projekta sa 16 odabranih NBA igraca.

## Pokretanje

U ovoj mapi pokreni:

```bash
py server.py
```

Zatim otvori:

```text
http://127.0.0.1:8001
```

## Sto projekt pokazuje

- HTML, CSS i JavaScript frontend
- Python backend
- REST API
- JSON odgovore
- XML odgovore
- SQLite bazu
- CRUD za favorite
- pretragu 16 igraca
- prikaz svih 30 NBA timova
- jednostavan dashboard

## API rute

```http
GET /api/search?name=Luka%20Doncic
GET /api/favorites
POST /api/favorites
PUT /api/favorites/1
DELETE /api/favorites/1
GET /api/teams
GET /api/teams/1610612747
GET /api/dashboard
```

XML primjer:

```text
http://127.0.0.1:8001/api/favorites?format=xml
```
