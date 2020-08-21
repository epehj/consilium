Mise à jour
===========

Après l'envoi automatique des fichiers sur le serveur de production, lancer les commandes suivantes :

```bash
npm install
node_modules/grunt/bin/grunt
bin/composer.phar install
bin/composer.phar dump-autoload --optimize
bin/console assets:install
bin/console aw:doctrine:schema:update --dump-sql
bin/console doctrine:migrations:migrate
```

Taches CRON
===========

```
# crontab -u www-data -e

00 6 * * * php /var/www/bin/console aw:plans:alert --env=prod

# Du lundi au vendredi de 7H à 20H, mise à jour stats tous les 15 minutes
*/15 7-20 * * mon,tue,wed,thu,fri php /var/www/bin/console aw:plans:stats:update --env=prod

# Idem, tous les 5 minutes
*/5 7-20 * * mon,tue,wed,thu,fri php /var/www/bin/console aw:plans:geocoding --env=prod

00 00 * * * php /var/www/bin/console aw:plans:print:clean --env=prod
```
