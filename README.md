# Shopix
Shopix est un site internet proposant des services et des produits
## Installation
Shopix a besoin de [PHP](https://wampserver.aviatechno.net/) 7.4 > v < 8, [Wampserver](https://wampserver.aviatechno.net/) v3+, [node.js](https://nodejs.org/fr/download/), [Symfony CLI](https://symfony.com/download) et de [Composer](https://getcomposer.org/download/) pour fonctionner correctement.
Iniatialiser l'application
```sh
git clone https://github.com/AuditActionPlus/Shopix.git
cd Shopix
composer install
php bin/console doctrine:database:create
php bin/console make:migration -n
php bin/console doctrine:migrations:migrate -n
npm install @symfony/webpack-encore --save-dev
npm run build
php bin/console doctrine:fixtures:load -n
```

Lancer le serveur
```sh
symfony serve
```

Créer une nouvelle branche
```sh
git checkout -b your_branch
git push -u origin your_branch
```

Mettre à jour les dépendances
```sh
composer install
npm run build
```

Mettre à jour la base de données
```sh
php bin/console make:migration -n
php bin/console doctrine:migrations:migrate -n
```
