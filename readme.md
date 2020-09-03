# KuKuPad

A Mardown "Wiki"

```
composer install
yarn
yarn dev

bin/console doctrine:fixtures:load

OR

cat backup.sql | docker exec -i kukupad_database_1 /usr/bin/mysql -u main --password=main main
```
