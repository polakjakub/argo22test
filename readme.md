Argo 22 test
=================

Instalace
---------

```
git clone git@github.com:polakjakub/argo22test.git
cd argo22test/
docker-compose up -d
```

V browseru otevřte URL http://localhost:8089/ server: **mysql**, user: **dev**, heslo: **devxxx** 
a do databáze wordpress nahrajde wordpress.sql

Pak si otevřete http://localhost:8088/wp-login.php a přihlaste se jako uživatel **test**, heslo **test**
a nainstalujte a aktivujte plugin **Advanced Custom Fields**.

Je vytvořena jedna stránka, která je nastavena jako výchozí, která používá shortcode. Shortcode může být ve formátu:

```[testargo22 date_format="Y-m-d" format="table" from_date="2023-01-01" to_date="2023-06-10"]```