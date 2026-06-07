<?php
$json = '{
  "ime": "Petar",
  "prezime": "Kovač",
  "starost": 30,
  "grad": "Zagreb",
  "zaposlen": true,
  "auti": [
    {
      "marka": "Ford",
      "model": "Mustang",
      "models": ["Mustang", "Focus", "Fiesta"],
      "godina": 2018,
      "boja":"bijela",
      "registriran": true,
      "cijena": 100000,
      "kilometri": 85000,
      "gorivo": "Benzin",
      "vlasnik_od": 2019
    },
    {
      "marka": "BMW",
      "model": "X1",
      "models": ["320", "X1", "X6"],
      "godina": 2020,
      "boja":"crma",
      "registriran": true,
      "cijena": 80000,
      "kilometri": 45000,
      "gorivo": "Dizel",
      "vlasnik_od": 2020
    },
    {
      "marka": "Fiat",
      "model": "500",
      "models": ["500", "Panda", "Tipo"],
      "godina": 2015,
      "boja":"plava",
      "registriran": false,
      "cijena": 10000,
      "kilometri": 120000,
      "gorivo": "Benzin",
      "vlasnik_od": 2016
    },
    {
      "marka": "Ferrari",
      "model": "911",
      "models": ["911", "F8", "Roma"],
      "godina": 2025,
      "boja":"plava",
      "registriran": false,
      "cijena": 200000,
      "kilometri": 5000,
      "gorivo": "Benzin",
      "vlasnik_od": 2025
    },
    {
      "marka": "Porsche",
      "model": "Taycan",
      "models": ["Taycan", "Cayenne", "Panamera"],
      "godina": 2019,
      "boja":"plava",
      "registriran": true,
      "cijena": 120000,
      "kilometri": 30000,
      "gorivo": "Električni",
      "vlasnik_od": 2020
    }
  ]
}';

$podatci = json_decode($json, true);

$brojPrikazanih = 0;
$autiHtml = "";

foreach ($podatci["auti"] as $auto) {

    if ($auto["registriran"] == true) {

        $brojPrikazanih++;

        $modeliHtml = "";

        foreach ($auto["models"] as $model) {
            $modeliHtml .= "<li>" . $model . "</li>";
        }

	if ($auto["cijena"] > 20000) {
    $oznakaCijene = "Skuplji automobil";
} else {
    $oznakaCijene = "Povoljniji automobil";
}

        $autiHtml .= "
            <div class='car'>
                <h3>" . $auto["marka"] . "</h3>

                <div class='car-data'>
    <div class='data-box'>
        <span>Godina</span>
        <strong>" . $auto["godina"] . "</strong>
    </div>

	<div class='data-box'>
    <span>Boja</span>
    <strong>" . $auto["boja"] . "</strong>
</div>

    <div class='data-box'>
        <span>Cijena</span>
        <strong>" . $auto["cijena"] . " €</strong>
    </div>

    <div class='data-box'>
        <span>Kilometri</span>
        <strong>" . $auto["kilometri"] . " km</strong>
    </div>

    <div class='data-box'>
        <span>Gorivo</span>
        <strong>" . $auto["gorivo"] . "</strong>
    </div>

    <div class='data-box'>
        <span>Vlasnik od</span>
        <strong>" . $auto["vlasnik_od"] . "</strong>
    </div>
</div>

                <div class='status'>Registriran</div>
	
	<div class='status'>" . $oznakaCijene . "</div>

                <div class='models'>
                    <h4>Modeli</h4>
                    <ul>
                        " . $modeliHtml . "
                    </ul>
                </div>
            </div>
        ";
    }
}

if ($brojPrikazanih > 0) {
    $rezultatHtml = "
        <div class='result'>
            Ukupan broj prikazanih automobila: " . $brojPrikazanih . "
        </div>
    ";
} else {
    $rezultatHtml = "
        <div class='empty'>
            Nema automobila koji zadovoljavaju uvjet filtriranja.
        </div>
    ";
}

print "
<!DOCTYPE html>
<html lang='hr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Filtriranje JSON podataka</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Verdana, Arial, sans-serif;
            background: #101820;
            color: #f4f4f4;
            min-height: 100vh;
            padding: 32px;
        }

        .page {
            max-width: 1150px;
            margin: 0 auto;
        }

        .top {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        .intro {
            background: linear-gradient(135deg, #f2aa4c 0%, #d96c06 100%);
            color: #101820;
            padding: 30px;
            border-radius: 8px 34px 8px 34px;
            box-shadow: 8px 8px 0 #000000;
        }

        .intro h1 {
            margin: 0 0 14px 0;
            font-size: 36px;
            letter-spacing: -1px;
        }

        .intro p {
            margin: 0;
            font-size: 18px;
            line-height: 1.6;
        }

        .person {
            background: #1f2933;
            border: 2px solid #f2aa4c;
            padding: 24px;
            border-radius: 24px 8px 24px 8px;
            box-shadow: 8px 8px 0 #000000;
        }

        .person h2 {
            margin: 0 0 18px 0;
            font-size: 24px;
            color: #f2aa4c;
        }

        .person-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px dashed rgba(255,255,255,0.25);
            padding: 10px 0;
        }

        .person-row:last-child {
            border-bottom: none;
        }

        .label {
            color: #a7b0ba;
        }

        .value {
            font-weight: bold;
            color: #ffffff;
        }

        .note {
            background: #16212c;
            border-left: 6px solid #f2aa4c;
            padding: 18px 20px;
            margin-bottom: 28px;
            font-size: 17px;
            line-height: 1.6;
            border-radius: 0 16px 16px 0;
        }

        .cars-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
        }

        .cars-title h2 {
            margin: 0;
            font-size: 28px;
        }

        .filter-badge {
            background: #f2aa4c;
            color: #101820;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .cars {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .car {
            position: relative;
            background: #f4f4f4;
            color: #101820;
            padding: 24px;
            border-radius: 18px;
            box-shadow: 8px 8px 0 #000000;
            overflow: hidden;
        }

        .car:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: repeating-linear-gradient(90deg, #f2aa4c 0 18px, #101820 18px 36px);
        }

        .car h3 {
            margin: 12px 0 18px 0;
            font-size: 30px;
        }

        .car-data {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .data-box {
            background: #e8edf2;
            padding: 12px;
            border-radius: 12px;
        }

        .data-box span {
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #506070;
            margin-bottom: 5px;
        }

        .data-box strong {
            font-size: 18px;
        }

        .status {
            display: inline-block;
            background: #1f7a3f;
            color: #ffffff;
            padding: 8px 13px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .models {
            border-top: 2px dashed #aab3bd;
            padding-top: 16px;
        }

        .models h4 {
            margin: 0 0 12px 0;
            font-size: 18px;
        }

        .models ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .models li {
            background: #101820;
            color: #f2aa4c;
            padding: 8px 11px;
            border-radius: 999px;
            font-size: 14px;
        }

        .result {
            margin-top: 28px;
            background: #f2aa4c;
            color: #101820;
            padding: 18px 22px;
            border-radius: 14px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 6px 6px 0 #000000;
        }

        .empty {
            background: #4b1111;
            border: 2px solid #ff9b9b;
            color: #ffffff;
            padding: 20px;
            border-radius: 14px;
            font-size: 18px;
        }

        @media (max-width: 850px) {
            body {
                padding: 22px;
            }

            .top {
                grid-template-columns: 1fr;
            }

            .cars {
                grid-template-columns: 1fr;
            }

            .intro h1 {
                font-size: 31px;
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 14px;
            }

            .intro {
                padding: 22px;
                border-radius: 8px 24px 8px 24px;
            }

            .person {
                padding: 20px;
            }

            .person-row {
                flex-direction: column;
                gap: 4px;
            }

            .cars-title {
                align-items: flex-start;
                flex-direction: column;
            }

            .car {
                padding: 20px;
            }

            .car h3 {
                font-size: 26px;
            }

            .car-data {
                grid-template-columns: 1fr;
            }

            .models li {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

<div class='page'>

    <div class='top'>

        <div class='intro'>
            <h1>Filtriranje JSON podataka</h1>
            <p>
                Program čita JSON dokument i prikazuje samo automobile koji zadovoljavaju zadani uvjet.
                U ovom primjeru prikazuju se samo registrirani automobili.
            </p>
        </div>

        <div class='person'>
            <h2>Podaci o osobi</h2>

            <div class='person-row'>
                <span class='label'>Ime</span>
                <span class='value'>" . $podatci["ime"] . "</span>
            </div>

            <div class='person-row'>
                <span class='label'>Prezime</span>
                <span class='value'>" . $podatci["prezime"] . "</span>
            </div>

            <div class='person-row'>
                <span class='label'>Starost</span>
                <span class='value'>" . $podatci["starost"] . " godina</span>
            </div>
        </div>

    </div>

    <div class='note'>
        Automobili koji nisu registrirani ne brišu se iz JSON dokumenta.
        Oni i dalje postoje u podacima, ali ih PHP preskače prilikom ispisa.
    </div>

    <div class='cars-title'>
        <h2>Popis automobila</h2>
        <div class='filter-badge'>Uvjet: registriran = true</div>
    </div>

    <div class='cars'>
        " . $autiHtml . "
    </div>

    " . $rezultatHtml . "

</div>

</body>
</html>
";
?>
