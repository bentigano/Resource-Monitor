RM - Web-based resource monitor
=======================

Introduction
------------
This is a simple resource monitor that will allow you to
configure monitoring plans and alert subscriptions to keep
an eye on SOAP services, DB connections, and query results.
It is built using Zend Framework 2 for PHP, with the UI based 
heavily on Bootstrap.

Requirements
------------
- PHP v5.3.3+ (must have support for Zend Framework v2.0)
- Zend Framework v2.2.3+ [(available here)](http://framework.zend.com/downloads/latest#ZF2)
- MySQL v5.0.2+ (must have support for triggers)

Installation
------------
To install, you need to perform the following tasks:
- Download and extract all files in this project to your web server.
- Extract the ZF2 library into the `vendor` directory so that the structure is `\vendor\ZF2\library\Zend\Authentication...`
- Create a MySQL database and user with priveleges to that database
- Execute the install.sql file on that database
- Modify the settings in the `/config/autoload/global.php` file to match your environment (specifically, the `db` and `alerts` settings except those that say "set in local.php")
- Rename `/config/autoload/local.php.dist` to `local.php` and update the settings within the new file
- Create a cron job (or scheduled task) that will hit `http://[server-name]/cron` once a minute. For example, if you installed this at the root of your web server and are setting up a local cron job on the same machine, your URL would be `http://localhost/cron`

Sample .htaccess file (on Xampp in a subdirectory)
------------
    RewriteEngine On
    # The following rule tells Apache that if the requested filename
    # exists, simply serve it.
    RewriteCond %{REQUEST_FILENAME} -s [OR]
    RewriteCond %{REQUEST_FILENAME} -l [OR]
    RewriteCond %{REQUEST_FILENAME} -d [OR]
    RewriteRule ^(xampp|phpmyadmin|favicon.ico|robots.txt) - [L]
    RewriteRule ^.*$ - [NC]
    RewriteRule ^(?!rm/public/).*$ rm/public%{REQUEST_URI} [L,NC]
