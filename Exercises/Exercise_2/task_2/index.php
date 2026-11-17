<?php

$xmlFile = __DIR__ . '/../LV2.xml';

$xml = simplexml_load_file($xmlFile);
if ($xml === false) 
    die("Unable to load XML file.\n");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>People</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 30px;
        }
        h1 {
            text-align: center;
        }
        .people {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .person {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .person img {
            display: block;
            margin: 0 auto 15px;
            width: 100px;
            height: 100px;
        }
        .person h2 {
            text-align: center;
        }
        .person p {
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <h1>People</h1>
    <div class="people">
        <?php foreach ($xml->record as $person): ?>
            <div class="person">
                <img src="<?= htmlspecialchars($person->slika) ?>" alt="Photo">
                <h2>
                    <?= htmlspecialchars($person->ime . ' ' . $person->prezime) ?>
                </h2>
                <p><?= htmlspecialchars($person->email) ?></p>
                <p><?= htmlspecialchars($person->zivotopis) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>