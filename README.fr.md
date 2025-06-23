Pour une version en anglais, voit [README.en.md](README.en.md).
# 1.Objectif
Ce projet a pour but permettre l'utilisation de la fonction de suivi online de OsmAnd. Il comprend donc deux fonctions :  
1. un "enregistreur" **set.php** à configurer dans osmAnd pour enregistrer tous les points envoyés par OsmAnd  
2. un "visualisateur" **suivre.php** permettant aux tiers de suivre la position actuelle de l'utilisateur OsmAnd, avec visualisation sur OpenStreetMap de l'ensemble du trajet depuis le début de l'enregistrement ainsi que deu profil altimétrique.  
# 2. Licenses
Projet opensource license MIT.
Les icônes présentes dans le projet sont libres de droit et d'origine flaticon avec obligation de créditer les créateurs. La page  suivre.php respecte cette obligation. Toute copie de ces fichiers icônes doit respecter cette obligation de crédit.
# 3. mise en oeuvre
## 3.1 installation
Il faut disposer d'un serveur WEB sur lequel on dépose les fichiers du projet (deux php, un css et cinq png). Ce serveur doit accepter le php et la création de fichiers dans le répertoire des php par ceux-ci (pour stocker les fichiers de suivi)
## 3.2 utilisation
### a) l'enregistrement des données de suivi émises par OsmAnd
Dans les paramètres du profil osmAnd, rubrique "Enregistrement d'itinéraire", sous-rubrique "suivi en ligne" :   

* activer le suivi en ligne (sinon rien ne sera envoyé)   
* mettre dans le paramètre "adresse WEB" :  
> *url_de_votre_site*/set.php?id=xxxxxx&cmd=add&lat={0}&lon={1}&tim={2}&alt={4}

Plusieurs points à noter :   

* l'identité (id=xxxxx, par exemple le prénom de l'utilisateur) est ce qui permet de relier les données enregistrées avec la fonction de suivi. Vous pouvez avoir plusiurs téléphones avec OsmAnd, chacun paramétré avec un identifiant différent et cet identifiant permettra de suivre le OsmAnd que l'on veut.
* l'identité ne peut être composée que des 52 caractères ASCII majuscules / minuscules plus les chiffre 0-9.
* le fichier qui sera utilisé sur le serveur WEB dans le répertoire de set.php s'appellera AAAA-MM-JJ-xxxxx et donc sera journalier. Il n'y a donc pas besoin de modifier le paramétrage OsmAnd d'un jour à l'autre. En revanche, une marche le matin et une marche l'après-midi seront enregistrées l'une à la suite de l'autre (la pause entre les deux sera clairement visible sur le suivi). Si vous voulez éviter cette concaténation, deux solutions :   

    *   modifier le paramètre "adresse WEB" l'après midi pour utiliser un identifiant différent pour les deux suivis du même jour
    *   réinitialiser le fichier de suivi avant de commencer le suivi de l'après-midi. Cela fera perdre les informations de suivi collectées jusque là dans la journée. Pour cela, utiliser un navigateur WEB et utiliser l'url :
> *url_de_votre_site*/set.php?id=xxxxxx&**cmd=new**

* les autres paramètres proposés par OsmAnd (hdop, vitesse, cap, ..) ne sont pas enregistrés par set.php
### b) Le suivi en temps réel
Les personnes qui veulent vous suivre doivent utiliser dans un navigateur WEB l'URL :
> *url_de_votre_site*/suivre.php?id=xxxxxx

Cette fonction affiche le trajet effectué depuis le premier enregistrement de la journée sur un fond de carte OpenStreetMap. Elle :  

* permet de zoomer sur les parties de la carte souhaitées
* comprend une icône départ (petit drapeau) qui donne l'heure de départ (cliquer sur l'icône)
* comprend une icône dernier point (grosse épingle rouge) qui donne (cliquer sur l'icône) l'heure de ce dernier point, le kilométrage parcouru depuis le début et le D+
* peut comprendre des icônes (petites épingles vertes) marquant des pauses identifiées
* contient une icône (petite cible en bas à droite) qui est un bouton permettant de se repositionner en zoom sur le dernier point
* contient une icône (petites montagns en bas à droite) qui est un bouton permettant l'apparition d'un cadre contenant le profil altimétrique du déplacement jusqu'au dernier point enregistré. Pour faire disparaître ce cadre, soit cliquer sur la petite croix en haut à droite du cadre, soit re-cliquer sur le bouton "montagnes"
* est raffraichie automatiquement toutes les 10 minutes (mais peut être raffraichie explicitement à la demande via les fonctions standard du navigateur

Une forme particulière permet de revoir le suivi d'une journée précédente, la situation par défaut étant de suivre la trace en cours (objectif premier du suivi online de OsmAnd)
> *url_de_votre_site*/suivre.php?id=xxxxxx,dat=AAAA-MM-JJ

# limites et problèmes (à développer)
## confidentialité
Toute personne connaissant l'url et l'identifiant peut suivre, voire ajouter des points.  
Ceci pourra être réglé dans une prochaine version par adjonction d'un mot de passe.'
## lissage des points GPS anormaux
Un lissage interne des positions est effectué par suivre.php. Il peut être paramétré (peut-être dans une version à venir rendre ce paramètre accessible dans l'URL de suivi. Il est utile pour les traces pédestres pour lesquelles la précision GPS assez hasardeuse donne parfois des trajets bizarres. Il a moins d'impact pour un suivi d'utilisateur motorisé)
## lissage des altitudes
Un lissage interne des altitudes est effectué par suivre.php. Il peut être paramétré (peut-être dans une version à venir rendre ce paramètre accessible dans l'URL de suivi). Ce lissage est très important pour avoir un D+ un peu significatif.
## changement de jour  
Une randonnée qui passe minuit (heure du serveur) provoque le changement de fichier de suivi ...
## fuseaux horaires
Je ne suis pas sûr que les horaires affichés soient corrects si le serveur et les utilisateurs de set.php et suivre.php sont dans des fuseaux horaires différents

