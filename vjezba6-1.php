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
      "godina": 2018,
      "registriran": true,
      "cijena":100000
    },
    {
      "marka": "BMW",
      "model": "X1",
      "godina": 2020,
      "registriran": true,
      "cijena":80000
    },
    {
      "marka": "Fiat",
      "model": "500",
      "godina": 2015,
      "registriran": false,
      "cijena":100000
    },
    {
      "marka": "Ferrari",
      "model": "911",
      "godina": 2025,
      "registriran": false,
      "cijena":200000
    },
    {
      "marka": "Porsche",
      "model": "Taycan",
      "godina": 2019,
      "registriran": true,
      "cijena":120000
    }
  ]
}';

$podatci = json_decode($json, true);
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON automobili</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1f2937;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .naslov {
            text-align: center;
            margin-bottom: 35px;
        }

        .naslov h1 {
            margin: 0;
            font-size: 36px;
            color: #111827;
        }

        .naslov p {
            margin-top: 10px;
            font-size: 18px;
            color: #6b7280;
        }

        .osoba {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-left: 8px solid #2563eb;
        }

        .osoba h2 {
            margin-top: 0;
            font-size: 28px;
            color: #1d4ed8;
        }

        .osoba-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 20px;
        }

        .podatak {
            background: #eff6ff;
            padding: 16px;
            border-radius: 12px;
        }

        .podatak span {
            display: block;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .podatak strong {
            font-size: 20px;
            color: #0f172a;
        }

        .sekcija {
            margin-top: 35px;
        }

        .sekcija h2 {
            font-size: 30px;
            margin-bottom: 20px;
        }

        .auti {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .auto-kartica {
            background: #ffffff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-top: 6px solid #94a3b8;
        }

        .auto-kartica h3 {
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 24px;
            color: #111827;
        }

        .auto-kartica p {
            margin: 10px 0;
            font-size: 17px;
        }

        .oznaka {
            display: inline-block;
            margin-top: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: bold;
        }

        .registriran {
            border-top-color: #16a34a;
        }

        .registriran .oznaka {
            background: #dcfce7;
            color: #166534;
        }

        .nije-registriran {
            border-top-color: #dc2626;
        }

        .nije-registriran .oznaka {
            background: #fee2e2;
            color: #991b1b;
        }

	.skupi-auto {
    	    border: 10px solid gold;
   	    background-color: #fff8dc;
	}

        @media (max-width: 900px) {
            body {
                padding: 24px;
            }

            .osoba-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .auti {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 16px;
            }

            .naslov h1 {
                font-size: 30px;
            }

            .naslov p {
                font-size: 17px;
            }

            .osoba {
                padding: 22px;
            }

            .osoba-grid {
                grid-template-columns: 1fr;
            }

            .auti {
                grid-template-columns: 1fr;
            }

            .auto-kartica {
                padding: 22px;
            }

            .podatak strong {
                font-size: 22px;
            }

            .auto-kartica p {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="naslov">
        <h1>JSON podaci o automobilima</h1>
        <p>Primjer čitanja JSON objekta i JSON polja pomoću PHP-a</p>
    </div>

    <div class="osoba">
        <h2>Podaci o osobi</h2>

        <div class="osoba-grid">
            <div class="podatak">
                <span>Ime i prezime</span>
                <strong><?php echo $podatci["ime"] . " " . $podatci["prezime"]; ?></strong>
            </div>

            <div class="podatak">
                <span>Starost</span>
                <strong><?php echo $podatci["starost"]; ?> godina</strong>
            </div>

            <div class="podatak">
                <span>Grad</span>
                <strong><?php echo $podatci["grad"]; ?></strong>
            </div>

            <div class="podatak">
                <span>Status</span>
                <strong>
                    <?php
                    if ($podatci["zaposlen"] == true) {
                        echo "Zaposlen";
                    } else {
                        echo "Nije zaposlen";
                    }
                    ?>
                </strong>
            </div>
        </div>
    </div>

    <div class="sekcija">
        <h2>Popis automobila</h2>

        <div class="auti">
            <?php foreach ($podatci["auti"] as $auto): ?>

                <?php
                if ($auto["registriran"] == true) {
                    $klasa = "registriran";
                    $status = "Registriran";
                } else {
                    $klasa = "nije-registriran";
                    $status = "Nije registriran";
                }
		
		if ($auto["cijena"] > 20000) {
   		    $klasa .= " skupi-auto";
		}
                ?>

                <div class="auto-kartica <?php echo $klasa; ?>">
                    <h3><?php echo $auto["marka"] . " " . $auto["model"]; ?></h3>

                    <p><strong>Marka:</strong> <?php echo $auto["marka"]; ?></p>
                    <p><strong>Model:</strong> <?php echo $auto["model"]; ?></p>
                    <p><strong>Godina:</strong> <?php echo $auto["godina"]; ?></p>
                    <p><strong>Boja:</strong> <?php echo $auto["boja"]; ?></p>
		    <p><strong>Cijena:</strong> <?php echo $auto["cijena"]; ?> €</p>

                    <span class="oznaka"><?php echo $status; ?></span>
                </div>

            <?php endforeach; ?>
        </div>
    </div>

</div>

</body>
</html>
