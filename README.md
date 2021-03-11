## Install
### Add to hosts file:
<code>127.0.0.150 vitamin-test.loc</code>

### Build & Run Docker using docker-compose
1. <code>docker-compose build</code> - may take up to 10 minutes
2. <code>docker-compose up -d</code>

### Update database (MySql 8 feature) (run inside mysql)
1. <code>FLUSH PRIVILEGES;</code>
2. <code>ALTER USER 'root' IDENTIFIED WITH mysql_native_password BY 'pass';</code>


### Artisan (run inside php_fpm)
<code>php artisan migrate</code>
<code>php artisan storage:link</code>

