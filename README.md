Guide d'installation et d'utilisation du back-end
Ce guide vous aidera à installer et à utiliser le back-end de l'application.

Installation
Clonez ce dépôt sur votre machine locale :

bash
Copy code
git clone <url_du_dépôt>
Accédez au répertoire du projet :

bash
Copy code
cd nom_du_projet
Installez les dépendances PHP en exécutant la commande suivante :

bash
Copy code
composer install
Créez la base de données :

bash
Copy code
php bin/console doctrine:database:create
Effectuez les migrations pour créer les tables de la base de données :

bash
Copy code
php bin/console doctrine:migrations:migrate
Chargez les fixtures pour peupler la base de données avec des données de test :

bash
Copy code
php bin/console doctrine:fixtures:load
Utilisation
Après avoir exécuté les fixtures, accédez à Postman pour tester les fonctionnalités de l'API.

Pour ajouter des images de jeux, utilisez l'endpoint suivant avec la méthode POST :

http
Copy code
POST http://localhost:8000/api/picture
Importez les images depuis le répertoire /public/images/games.

Pour associer une image à un jeu spécifique, utilisez l'endpoint suivant avec la méthode PUT :

http
Copy code
PUT http://localhost:8000/api/picture/game/{idPicture}
Remplacez {idPicture} par l'identifiant de l'image et ajoutez l'ID du jeu dans le corps de la requête :

json
Copy code
{
    "game_id" : {idGame}
}
Pour associer une image de profil à un utilisateur, utilisez l'endpoint suivant avec la méthode PUT :

http
Copy code
PUT http://localhost:8000/api/picture/user/{idPicture}
Remplacez {idPicture} par l'identifiant de l'image et ajoutez l'ID de l'utilisateur dans le corps de la requête :

json
Copy code
{
    "user_id" : {idUser}
}
Vous pouvez maintenant utiliser l'API pour gérer les images de jeux et les images de profil des utilisateurs.

