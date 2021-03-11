## Install
### Rename files
1. <code>backend/.env.example</code> to <code>backend/.env</code><br>
2. <code>client/.env.example</code> to <code>client/.env</code><br>
3. <code>.env.example</code> to <code>.env</code>

### Add to hosts file:
<code>127.0.0.150 vitamin-test.loc</code>

### Build & Run Docker using docker-compose
1. <code>docker-compose build</code> - may take up to 10 minutes
2. <code>docker-compose up -d</code>

### Update database (MySql 8 feature) (run inside mysql)
1. <code>FLUSH PRIVILEGES;</code>
2. <code>ALTER USER 'root' IDENTIFIED WITH mysql_native_password BY 'pass';</code>


### Artisan (run inside php_fpm)
<code>php artisan migrate --seed</code>
<code>php artisan storage:link</code>

## Auth (~\backend\database\seeders\UserSeeder.php)
<b>User 1 (main):</b><br>
<b>login:</b> alex@gmail.com<br>
<b>pass:</b> pass

<b>User 2:</b><br>
<b>login:</b> jane@gmail.com<br>
<b>pass:</b> pass

<b>User 3:</b><br>
<b>login:</b> rick@gmail.com<br>
<b>pass:</b> pass

<b>User 4:</b><br>
<b>login:</b> morty@gmail.com<br>
<b>pass:</b> pass
