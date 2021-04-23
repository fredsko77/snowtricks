# Snowtricks

Installation du projet 

1. Clôner le projet
> git clone https://github.com/fredsko77/snowtricks.git <répertoire>

2. Modifier le fichier de configuration .env
> MAILER_DSN=gmail+smtp://:@default   
> DATABASE_URL="mysql://db_user:db_pass@db_host/db_name?serverVersion=mariadb-10.4.11"

3. Installer les dépendances 
> composer install 

4. Générer un fichier autoload 
> composer dump-autoload

5. Créer la base de données 
```php 
php bin/console doctrine:database:create
```

6. Générer les fichiers de migrations 
```php
php bin/console make:migration
``` 
En cas d'erreur, executer la commande **`mkdir migrations`** puis relancer la commande **`php bin/console make:migration`**

7. Executer les fichiers de migrations 
```php 
php bin/console doctrine:migrations:migrate
```

8. Executer les fixtures 
```php 
php bin/console doctrine:fixtures:load
```
