# SAÉ Web PhP - Musics 2024

## Notre équipe

### Développeur 1

- Nom : BOURREAU
- Prénom : QUENTIN
- Identifiant Github : [BOURREAUQuentin](https://github.com/BOURREAUQuentin)

### Développeur 2

- Nom : BABA
- Prénom : Ahmet
- Identifiant Github : [BabaAhmet](https://github.com/ahmet40)

### Développeur 3

- Nom : PREVOST
- Prénom : Maverick
- Identifiant Github : [MaverickPrevost](https://github.com/MaverickPrevost)

## Introduction

Cette SAÉ s'inscrit dans le cours de Web et de PhP, en se reposant sur les ressources suivantes :
- R3.01 : Développement Web
- R4.10 : Complément web

Le sujet repose principalement sur le développement d'une application présentant le contenu d'une base d'albums de musique. En partant d’un fichier fixtures.zip donné avec le sujet, l’objectif est donc de modéliser et implémenter une base de données pour une application PHP de gestion d'albums de musique. La base de données devrait inclure des tables pour les artistes, les albums, les utilisateurs (pour la fonctionnalité de login), les playlists par utilisateur, et les notations d'albums.

## Nos choix

### Le respect des bonnes pratiques

Pour respecter les bonnes pratiques de programmation et de gestion de projet vu à l’IUT, nous avons tout d'abord crée un référentiel GitHub commun. En effet, celui-ci a permis de faciliter la collaboration, la gestion du code source, la communication au sein de l'équipe, mais également de suivre notre avancée sur cette SAÉ.

Lien du GitHub : [Lien vers le GitHub](https://github.com/BOURREAUQuentin/SAE-Web-PhP)

## Les lancements

### Le lancement de notre base de données
Lors de la récupération de notre dépot sur GitHub, notre base de données est déjà remplie avec nos insertions ajoutées et celles données dans le sujet (YML). Néanmoins, nous avons choisi d'utiliser un cli pour gérer automatiquement et simplement notre base de données. Effectivement, nous avons donc les commandes suivantes possibles (à utiliser dans la racine du projet) :

- Pour la création de la base de données
```bash
php Cli/sqlite.php sqlite db
```

- Pour la création des tables dans la base de données
```bash
php Cli/sqlite.php sqlite create
```

- Pour l'ajout des insertions dans la base de données
```bash
php Cli/sqlite.php sqlite load
```

- Pour la suppression des tables dans la base de données
```bash
php Cli/sqlite.php sqlite delete
```

### Le lancement de l'application
Pour lancer notre application, il suffit uniquement d'entrer la commande suivante (à utiliser dans la racine du projet) et d'aller vers l'url "localhost:8000" :

```bash
php -S localhost:8000
```

De plus, nous vous fournissons les identifiants d'un adminisateur pour tester la partie administrateur :

- Nom d'utilisateur : quentin
- Mot de passe : Test123!

## L'explication du site 

### Page Accueil

Une fois arrivé sur notre site, vous pouvez remarquez un header et un nav présent sur la gauche qui seront présents sur toutes les pages de notre site. Dans le nav en dessous de notre logo distinctif, vous pouvez retrouver divers outils à votre disposition. 

Premièrement, un bouton rechercher faisant apparaître une barre de recherche dans le header en haut de votre écran, cette barre de recherche vous permet de rechercher l'album, la musique ou encore l'artiste de votre choix. 

Ensuite, vous trouverez, en dessous de ce bouton rechercher, un bouton accueil vous permettant de revenir à l'accueil à tout moment. Après vous pouvez accéder à vos playlist ainsi qu'à vos titres likés en appuyant sur le bouton playlist. 

Enfin, tout en bas de ce nav, vous avez accès à un bouton paramètre ouvrant une pop-up. Cette pop-up vous permet d'accéder à la connexion ou la création de votre compte, et si vous êtes déjà connecté vous aurez diverses options telles que l'accès à votre profil ou encore à la déconnexion (à noter que si vous faites partie des admins, vous aurez donc accès à la partie admin du site).

Maintenant, vous pouvez voir au milieu de votre écran tout d'abord plusieurs années (70, 80, 90, etc...), mais aussi des genres (Rap, Pop, Rock, etc...), vous donnant accès aux albums, musiques et artistes correspondant à l'intitulé.

### Page d'un genre/année/recherche

Une fois arrivé sur cette page avec des albums, musiques et artistes spécifiques, vous pouvez consulter les albums (et les noter) ou encore les artistes, mais aussi liker les musiques, et les ajouter à une playlist.

### Page d'un artiste

Sur la page d'un artiste, vous pouvez consulter sa discographie avec ses albums (les noter) et liker les musiques, et les ajouter à une playlist.

### Page d'un album

Sur la page d'un album, vous pouvez consulter la liste des musiques de l'album, vous pouvez également les ajouter à une playlist, les liker ou encore les écouter. Vous trouverez également en haut de la page la note moyenne de l'album donnée par les utilisateurs.

### Page des playlists

Dans la page des playlists, vous pouvez consulter la playlist titres likés, mais aussi les playlists que vous avez créez. Vous pouvez également ajouter, supprimer ou modifier vos playlists.

### Page d'une playlist

Dans la page d'une playlist, vous avez accès à toutes les musiques mises dedans et donc de les écouter ou de les supprimer de la playlist.

### Page Connexion/Inscription

Sur cette page, vous pouvez vous connecter ou vous inscrire selon votre besoin (vous pouvez alterner en les deux avec seulement un bouton.). Vous aurez besoin de rentrer votre pseudo, votre email ainsi que votre mot de passe. Tout ceci respectant une syntaxe bien précise assurant la sécurité de votre compte. Nous vérifions également que vos informations ne soient pas déjà utilisées.

### Page Mon Profil

La page mon profil vous donne accès à vos informations personnelles avec votre pseudo, votre email et votre mot de passe. Cette page vous permet également de modifier ces mêmes informations personnelles tout en vérifiant que vos nouvelles informations ne sont pas déjà utilisées par un autre utilisateur.

### Partie Admin

La partie admin, n'étant seulement accessible qu'aux admins, permet de gérer les musiques, les albums, les genres ou encore les artistes. C'est-à-dire de les modifier, de les ajouter, ou de les supprimer.

#

BOURREAU Quentin / BABA Ahmet / PREVOST Maverick - BUT Informatique 2.3