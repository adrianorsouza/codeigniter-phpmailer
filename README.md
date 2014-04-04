# Codeigniter mail plugin powered by PHPMailer

A simple CodeIgniter Mail class to connect your application through PHPMailer library.

Version: 0.1.2

Tested over CodeIgniter v2.1.4 / v3.0-dev and PHPMailer Version 5.2.7

## Install via Composer

### Get this plugin via composer is the easiast way.
This plugin is available via [Composer/Packagist](https://packagist.org/packages/adrianorsouza/codeigniter-phpmailer). add this project to your composer.json

```JSON
"require" : {
    "adrianorsouza/codeigniter-phpmailer": "v0.1.2"
  }
```
then into your project run the command line
```CLI
composer.phar install
```

## Configuration
Once you speak composer you might have your autoload included in somewhere in your project if you don't just copy this single line in the very top of your index.php page
```PHP
require_once dirname(__DIR__) .'/vendor/autoload.php';
```
after that you are able to instance the class Mail() anywhere in your project:
```PHP
$mail = new Mail();
```

## Alternatively manual installation
If you don't speak composer, you can download by clicking in zip this files and uncompress it within of your ``application/`` directory.
or just cloning it to your desktop and move the files ``Mail.php`` to your ``application/li``

To get your mail class up and running with PHPMailer you basically need to do the following steps:
- copy class Mail.php to your ``application/libraries`` folder
- copy file ``mail_config.php`` to your ``application/config`` folder
- copy folder ``views/templates/email`` to your ``application/views`` folder

## Configuration

@TODO

## Composer
@TODO
