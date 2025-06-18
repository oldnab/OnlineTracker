<?php
// ---------------------------------------------------------------------
// Script PHP pour afficher une trace OsmAnd sur une carte Leaflet
// ---------------------------------------------------------------------
// Format attendu :
//    ?id=identifiant [ &dat=YYYY-MM-DD ]
// ---------------------------------------------------------------------

$DirSuivi = './';

// Vérifier que le répertoire de suivi existe (peut-être maj)
if (!is_dir($DirSuivi)) {
    die('Répertoire de stockage des fichiers de suivi inexistant');
}

// Nettoyer et récupérer les paramètres
$id = isset($_GET['id']) ? strtolower(preg_replace('/[^A-Za-z0-9]/', '', $_GET['id'])) : 'default';
$dat = isset($_GET['dat']) ? preg_replace('/[^0-9\-]/', '', $_GET['dat']) : date('Y-m-d');

// Définir le chemin du fichier de suivi
$FicSuivi = "$DirSuivi$dat-$id.txt";

// Vérifier l'existence du fichier
if (!file_exists($FicSuivi)) {
    touch($FicSuivi);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="600"/>
    <title>Suivez-moi sur OpenStreetMap</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="suivre.css"/>
</head>
<body>
    <div id="map"></div>

    <div id="filename"><strong><?= htmlspecialchars("$id ($dat)") ?></strong></div>

    <div id="gotoLastButton" title="Recentrer sur la dernière position">
        <img src="cible.png" alt="Centrer" width="100%"> 
    </div>

    <div id="seeAltButton" title="altitudes">
        <img src="mountain.png" alt="Altitudes" width="100%"> 
    </div>

    <div id="altGraph">
        <span id="altGraphClose">✖</span>
        <canvas id="altChart"></canvas>
    </div>

    <div id="credits">
        Icônes :<br>
        <a href="https://www.flaticon.com/fr/icones-gratuites/epingle" target="_blank">Pixel perfect</a>,
        <a href="https://www.flaticon.com/fr/icones-gratuites/point-de-depart" target="_blank">Creative Stall Premium</a>,
        <a href="https://www.flaticon.com/fr/icones-gratuites/broche-en-papier" target="_blank">rsetiawan</a>,
        <a href="https://www.flaticon.com/free-icons/target" target="_blank">Freepik</a>
        <a href="https://www.flaticon.com/free-icons/mountain" target="_blank">deemakdaksina - Flaticon</a>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
// constantes à ajuster le cas échéant
        const pauseMin = 5;
        const vitMin = 1;
        const distMin = 15; 
        const altLissage = 200;   // lissage sur xxx m de part et d'autre)
        const distCoeff = 112300;

// lire le fichier et fabriquer les données lissées
        const data = [
            <?php include($FicSuivi); ?>
        // laisser cette ligne entre le include et le crochet final
            ];

        if (data.length === 0) {
            data.push([48.853, 2.349, 34, Date.now()]);  // Notre-Dame de Paris :)
        }

// Fabriquer les données lissées
        let points = [data[0].slice(0,2)];
        let km = [0];
        let kmLabel=['km 0.00'];
        let dPlus = [0];
        let tsp = [data[0][3]];
        let alt = [data[0][2]];
        let nbPoints = 1;

        for (let i = 1; i < data.length; i++) {
            const [lat, lon, altitude, timestamp] = data[i];
            const prev = points[nbPoints - 1];
            const dLat = lat - prev[0];
            const dLon = lon - prev[1];
            const cosLat = Math.cos(Math.PI * lat / 180) * Math.cos(Math.PI * prev[0] / 180);
            const dist = Math.sqrt(dLat*dLat + dLon*dLon * cosLat) * distCoeff;
            const vit = 1000 * dist / (timestamp - tsp[nbPoints - 1]);
            let nbLissage = 0, kmmax=km[nbPoints-1]-altLissage/1000, totAlt=altitude;
            for (let j=nbPoints-1;j>=0, km[j]>kmmax; j--) {
                totAlt += alt[j];
                nbLissage++;
            }
            for (let j=Math.min (i+nbLissage,data.length-1) ; j>i;  j-- ) {
                totAlt += data[j][2];
                nbLissage++;
            }

            const altitudeLissée = totAlt / (nbLissage + 1);
            const dAlt = altitudeLissée - alt[nbPoints - 1];

            if (vit > vitMin || dist > distMin) {
            // ce point est retenu
                points.push([lat, lon]);
                km.push(km[nbPoints - 1] + Math.sqrt(dist*dist + dAlt*dAlt) / 1000);
                kmLabel.push('km '+km[nbPoints].toFixed(2));
                tsp.push(timestamp);
                alt.push(altitudeLissée);
                dPlus.push (dPlus[nbPoints-1]+Math.max(0, dAlt));
                nbPoints++;
            }
        }

// fabriquer le graphe d'altitudes
      const ctx = document.getElementById('altChart').getContext('2d');

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: kmLabel,
          datasets: [{
            label: 'Altitudes (D+ : '+dPlus[nbPoints-1].toFixed(1)+'m)',
            data: alt,
            type: 'line',
            borderColor: 'blue',
            borderWidth: 1,
            pointRadius: 1,
            backgroundColor: 'rgba(0, 0, 128, 0.2)',
            tension: 0,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: false
            }
          }
        }
      });

// Afficher la carte et le parcours lissé et centrer sur celui-ci
        const map = L.map('map').setView(points[0], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const polyline = L.polyline(points, {color: 'blue'}).addTo(map);
        map.fitBounds(polyline.getBounds());

// poser les épingles de départ, de pauses et de point actuel
        const iconDepart = L.icon({iconUrl:'depart.png', iconSize:[50,50], iconAnchor:[18,50], popupAnchor:[0,-50]});
        const iconPause  = L.icon({iconUrl:'vert.png',   iconSize:[30,30], iconAnchor:[0,30], popupAnchor:[20,-30]});
        const iconActuel = L.icon({iconUrl:'rouge.png',  iconSize:[50,50], iconAnchor:[0,50], popupAnchor:[30,-50]});

        const markerDepart = L.marker(points[0], {icon: iconDepart});
        markerDepart.bindPopup(
            "Départ : " + new Date(tsp[0]).toLocaleTimeString([] , { hour: '2-digit', minute: '2-digit'}),
            {className: 'myLeafletPopup'} 
        );
        markerDepart.addTo(map);

        for (let i = 1; i < nbPoints; i++) {
            const pause = tsp[i] - tsp[i - 1];
            if (pause > pauseMin * 60000) {
                const msg = i > 1 ? "Pause d'environ " : "Départ réel différé de ";
                const markerPause = L.marker(points[i - 1], {icon: iconPause});
                markerPause.bindPopup(
                    `${new Date(tsp[i - 1]).toLocaleTimeString([] , { hour: '2-digit', minute: '2-digit'}) }`
                    + ` - ${new Date(tsp[i]).toLocaleTimeString([] , { hour: '2-digit', minute: '2-digit'}) }<br>` 
                    + `(${msg} ${Math.floor(pause / 60000)} min) au km ${km[i - 1].toFixed(2)}`,
                    {className: 'myLeafletPopup'}
                );
                markerPause.addTo(map);
            }
        }

        const markerActuel = L.marker(points[nbPoints - 1], {icon: iconActuel});
        markerActuel.bindPopup(
                `Dernier point : ${new Date(tsp[nbPoints - 1]).toLocaleTimeString([] , { hour: '2-digit', minute: '2-digit'}) }<br>` 
                +`km ${km[nbPoints - 1].toFixed(2)}<br>` + `D+ ${dPlus[nbPoints - 1].toFixed(0)} m`,
                {className: 'myLeafletPopup'}
        );
        markerActuel.addTo(map);

// se mettre à l'écoute des clicks sur les boutons
        document.getElementById('gotoLastButton').addEventListener('click', () => {
            map.setView(points[nbPoints - 1], 18);
        });
        document.getElementById('altGraphClose').addEventListener('click', () => {
            document.getElementById('altGraph').style.display = 'none';
        });
        document.getElementById('seeAltButton').addEventListener('click', () => {
            if (document.getElementById('altGraph').style.display === 'block') 
                 { document.getElementById('altGraph').style.display = 'none';} 
            else { document.getElementById('altGraph').style.display = 'block';}
        });
 
    </script>
</body>
</html>

