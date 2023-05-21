## Bushi.Do - Site E-commerce

Bushi.Do est un site e-commerce construit avec le framework Symfony


## ⚙️ Installation

    Copiez le fichier .env et renommez-le .env.local
    Modifiez les variables d'environnement dans le fichier .env.local en fonction de votre configuration

    Installez les dépendances avec Composer composer install
    Créez la base de données php bin/console doctrine:database:create
    Générez un fichier de migration  php bin/console make:migration
    Effectuez les migrations php bin/console doctrine:migrations:migrate
    Chargez les fixtures php bin/console doctrine:fixtures:load


## Utilisation

    Lancez le serveur Symfony symfony server:start
    Rendez-vous sur http://localhost:8000


## Fonctionnalités

    Inscription et connexion des utilisateurs
    Ajout et suppression de produits dans le panier
    Passer une commande
    Paiement Stripe
    Dashboard compte utilisateur
    Interface pour l'administration du site