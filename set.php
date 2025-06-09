<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement de données de trace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 18px;
            background-color: #f4f4f4;
            margin: 0;
            padding: 2rem;
            color: #333;
        }
        .container {
            background-color: #fff;
            max-width: 700px;
            margin: auto;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 1.6rem;
            color: #006699;
            margin-bottom: 1rem;
        }
        .success {
            color: green;
            background-color: #e6f9e6;
            padding: 1rem;
            border-left: 5px solid green;
            margin-bottom: 1rem;
        }
        .error {
            color: #a94442;
            background-color: #f8d7da;
            padding: 1rem;
            border-left: 5px solid #a94442;
            margin-bottom: 1rem;
        }
        code {
            background: #eee;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Traitement des données de géolocalisation</h1>
    <?php
    $message = '';
    $messageClass = '';

    try {
        // Récupération des paramètres
        $id = isset($_GET['id']) ? strtolower(preg_replace('/[^A-Za-z0-9]/', '', $_GET['id'])) : 'default';
        $cmd = isset($_GET['cmd']) ? $_GET['cmd'] : 'add';
        $cmd = ($cmd !== 'add') ? 'new' : 'add';

        // Répertoire et fichier
        $DirSuivi = './';
        if (!is_dir($DirSuivi)) throw new Exception('Répertoire de stockage inexistant.');
        $FicSuivi = $DirSuivi . date('Y-m-d-') . $id . '.txt';

        if (!file_exists($FicSuivi)) {
            touch($FicSuivi);
        }

        if ($cmd === 'new' || filesize($FicSuivi) === 0) {
            file_put_contents($FicSuivi, '//  ');
        }

        if ($cmd === 'new') {
            $message = 'Fichier <code>' . htmlspecialchars($FicSuivi) . '</code> réinitialisé avec succès.';
            $messageClass = 'success';
        } else {
            // Ajout de point
            if (!isset($_GET['lon']) || !isset($_GET['lat'])) {
                throw new Exception('Latitude et longitude sont obligatoires.');
            }
            $lon = $_GET['lon'];
            $lat = $_GET['lat'];
            $alt = $_GET['alt'] ?? '0';
            $vit = $_GET['vit'] ?? '0';
            $tim = $_GET['tim'] ?? '0';


            $fic = fopen($FicSuivi, 'a');
            if ($fic) {
                fwrite($fic, ",\n[$lat, $lon, $alt, $tim]");
                fclose($fic);
                $message = 'Données ajoutées à <code>' . htmlspecialchars($FicSuivi) . '</code>.';
                $messageClass = 'success';
            } else {
                throw new Exception("Erreur à l'ouverture du fichier <code>$FicSuivi</code>.");
            }
        }
    } catch (Exception $e) {
        $message = '<strong>Erreur :</strong> ' . htmlspecialchars($e->getMessage());
        $messageClass = 'error';
    }

    if ($message !== '') {
        echo '<div class="' . $messageClass . '">' . $message . '</div>';
    }
    ?>
</div>
</body>
</html>

