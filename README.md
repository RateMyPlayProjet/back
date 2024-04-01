# Guide d'installation et d'utilisation du back-end

Ce guide vous aidera à installer et à utiliser le back-end de l'application.

## Installation

1. Installez les dépendances PHP en exécutant la commande suivante :

    ```bash
    composer install
    ```

2. Créez la base de données :

    ```bash
    php bin/console doctrine:database:create
    ```

3. Effectuez les migrations pour créer les tables de la base de données :

    ```bash
    php bin/console doctrine:migrations:migrate
    ```

4. Chargez les fixtures pour peupler la base de données avec des données de test :

    ```bash
    php bin/console doctrine:fixtures:load
    ```

## Utilisation

1. Après avoir exécuté les fixtures, accédez à Postman pour tester les fonctionnalités de l'API.

2. Pour ajouter des images de jeux, utilisez l'endpoint suivant avec la méthode POST :

    ```http
    POST http://localhost:8000/api/picture
    ```

    Importez les images depuis le répertoire `/public/images/games`.

3. Pour associer une image à un jeu spécifique, utilisez l'endpoint suivant avec la méthode PUT :

    ```http
    PUT http://localhost:8000/api/picture/game/{idPicture}
    ```

    Remplacez `{idPicture}` par l'identifiant de l'image et ajoutez l'ID du jeu dans le corps de la requête :

    ```json
    {
        "game_id" : {idGame}
    }
    ```

4. Pour associer une image de profil à un utilisateur, utilisez l'endpoint suivant avec la méthode PUT :

    ```http
    PUT http://localhost:8000/api/picture/user/{idPicture}
    ```

    Remplacez `{idPicture}` par l'identifiant de l'image et ajoutez l'ID de l'utilisateur dans le corps de la requête :

    ```json
    {
        "user_id" : {idUser}
    }
    ```

5. Vous pouvez maintenant utiliser l'API pour gérer les images de jeux et les images de profil des utilisateurs.
