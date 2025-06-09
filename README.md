# Objectif
Ce projet a pour but permettre l'utilisation de la fonction de suivi online de OsmAnd. Il comprend donc deux fonctions :  
1. un "répondeur" **set.php** à configurer dans osmAnd pour enregistrer tous les points envoyés par OsmAnd
2. un "visualisateur" **suivre.php** permettant aux tiers de suivre la position actuelle de l'utilisateur OsmAnd, avec visualisation sur OpenStreetMap de l'ensemble du trajet depuis le début de l'enregistrement.  
# mise en oeuvre
## installation
Il faut disposer d'un serveur WEB sur lequel on dépose les fichoers du projet (deux php et quatre png). Ce serveur doit accepter le php et la création de fichiers dans le répertoire des php par ceux-ci (pour stocker les fichiers de suivi)
## utilisation
### l'enregistrement des données de suivi émises par OsmAnd
Dans les paramètres du profil, rubrique "Enregistrement d'itinéraire", sous-rubrique "suivi en ligne" :  
* activer le suivi en ligne (sinon rien ne sera envoyé)
* mettre dans le paramètre "adresse WEB" :
> *url_de_votre_site*/set.php?id=xxxxxx&cmd=add&lat={0}&lon={1}&tim={2}&alt={4}&vit={5}
Plusieurs points à noter :
* l'identité (id=xxxxx, par exemple le prénom de l'utilisateur) est ce qui permet de relier les données enregistrées avec la fonction de suivi. Vous pouvez avoir plusiurs téléphones avec OsmAnd, chacun paramétré avec in identifiant différent et cet identifiant permettra de suivre le OsmAnd que l'on veut.
* l'identité doit être composée des 52 caractères ASCII majuscules / minuscules plus les chiffre 0-9.
* le fichier qui sera utilisé sur le serveur WEB dans le répertoire de set.php s'appellera AAAA-MM-JJ-xxxxx et donc sera journalier. Il n'y a donc pas besoin de modifier le paramétrage OsmAnd d'un jour à l'autre. En revanche, une marche le matin et une marche l'après-midi seront enregistrées l'une à la suite de l'autre (la pause entre les deux sera clairement visible sur le suivi). Si vous voulez éviter cette concaténation, deux solutions :
  *   modifier le paramètre "adresse WEB" l'après midi pour utiliser un identifiant différent pour les deux suivis du même jour
  *   réinitialiser le fichier de suivi avant de commencer le suivi de l'après-midi. Cela fera perdre les informations de suivi collectées jusque là dans la journée. Pour cela, utiliser un navigateur WEB et utiliser l'url :
> *url_de_votre_site*/set.php?id=xxxxxx&cmd=new
  
*   
# limites et problèmes
## confidentialité
## lissage des points GPS anormaux
## lissage des altitudes
## profil altimétrique
## changement de jour  
## adaptation écran smartphone

