#Codeigniter mail plugin powered by PHPMailer

###A simple CodeIgniter mail class to connect your application through the powerful PHPMailer library.

[![Latest Stable Version](https://poser.pugx.org/adrianorsouza/codeigniter-phpmailer/v/stable.svg)](https://packagist.org/packages/adrianorsouza/codeigniter-phpmailer) [![Total Downloads](https://poser.pugx.org/adrianorsouza/codeigniter-phpmailer/downloads.svg)](https://packagist.org/packages/adrianorsouza/codeigniter-phpmailer) [![Latest Unstable Version](https://poser.pugx.org/adrianorsouza/codeigniter-phpmailer/v/unstable.svg)](https://packagist.org/packages/adrianorsouza/codeigniter-phpmailer) [![License](https://poser.pugx.org/adrianorsouza/codeigniter-phpmailer/license.svg)](https://packagist.org/packages/adrianorsouza/codeigniter-phpmailer)

Tested over CodeIgniter v2.1.4 / v3.0-dev and PHPMailer Version 5.2.7

##Install via Composer

###To get this plugin via composer is the quick start.

This plugin utilizes Composer for its installation and PHPMailer dependency. If you haven't already, start by installing [Composer](http://getcomposer.org/doc/00-intro.md).

And are available via [Composer/Packagist](https://packagist.org/packages/adrianorsouza/codeigniter-phpmailer). Once you have Composer configured in your environment run the command line:
```CLI
  $ composer require "adrianorsouza/codeigniter-phpmailer:0.1.*"
```
This command will write into composer.json beyond download and place this project files and PHPMailer dependencies into your ``vendor`` folder.

If you have not included the composer autoload you must do it at the very top of your index.php page or whatever you are running this plugin.
```PHP
require_once __DIR__.'/vendor/autoload.php';
```
that's all. Your able to send e-mail anywhere inside your CodeIgniter application.

To create an instance of `Mail()` class:
```PHP
 $mail = new Mail();
```

##Alternatively manual installation

If you don't speak Composer, follow this instructions:

- Download this zip files and uncompress it within of your `application/` directory.

- Download [PHPMailer files](https://github.com/Synchro/PHPMailer) dependencies *because is not include in this package*.
  - PHPMailerAutoload.php
  - class.smtp.php
  - class.phpmailer.php

place them into your `third_party` folder or `what/you/want` as long as you include PHPMailer autoloader where you call the class `Mail()`.

To load class `Mail()` use the same Codeigniter super-object:
```PHP
$this->load->library('mail');
```
That's all.

##Configuration

After get this plugin you have to setup `mail_config.php` file that contains all mail server configuration that must be placed in your `application/config/` folder.

In order to be able to send emails from your local development either production server you must provide a valid SMTP account authentication.

So in this config file, you setup your smtp server and password, login, mail from and etc...

To set up a Gmail smtp account to send your emails, you must set the config `mail_smtp_secure` to `TLS`.

To send any message with a HTML template file place it into your views folder. The default folder is `views/templates/email` if you want to change it, set this in `mail_config.php` as long as it remains under views folder.

###Sample mail_config.php
```PHP
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Mailer Configuration
$config['mail_mailer']          = 'PHPMailer';
$config['mail_debug']           = 0; // default: 0, debugging: 2, 'local'
$config['mail_debug_output']    = 'html';
$config['mail_smtp_auth']       = true;
$config['mail_smtp_secure']     = ''; // default: '' | tls | ssl |
$config['mail_charset']         = 'utf-8';

// Templates Path and optional config
$config['mail_template_folder'] = 'templates/email';
$config['mail_template_options'] = array(
                                       'SITE_NAME' => 'Codeigniter Mail Plugin',
                                       'SITE_LOGO' => 'http://localhost/images/logo.jpg',
                                       'BASE_URL'  => 'http://localhost',
                                    );
// Server Configuration
$config['mail_smtp']            = 'smtp.example.com';
$config['mail_port']            = 587; // for gmail default 587 with tls
$config['mail_user']            = 'someone@example.com';
$config['mail_pass']            = 'secret';

// Mailer config Sender/Receiver Addresses
$config['mail_admin_mail']      = 'someone@example.com';
$config['mail_admin_name']      = 'WebMaster';

$config['mail_from_mail']       = 'someone@example.com';
$config['mail_from_name']       = 'Site Name';

$config['mail_replyto_mail']    = 'someone@example.com';
$config['mail_replyto_name']    = 'Reply to Name';

// BCC and CC Email Addresses
$config['mail_bcc']             = 'someone@example.com';
$config['mail_cc']              = 'someone@example.com';

// BCC and CC enable config, default disabled;
$config['mail_setBcc']          = false;
$config['mail_setCc']           = false;


/* End of file mail_config.php */
/* Location: ./application/config/mail_config.php */

```

##Usage

###Send a basic email message using a string as HTML body.
```PHP
$data = '<h2>Sample Basic</h2>
         <hr>
         <p>This is a simple basic mail message in <strong>HTML</strong> string format</p>
         <p>Lorem ipsum dolor sit amharum<br /> quod deserunt id dolores.</p>';

$mail = new Mail();
$mail->setMailBody($data);
$mail->sendMail('Awesome Subject', 'someone@example.com');
```

###Send email message as an external HTML template
```PHP
$data = null;
$template_html = 'sample-1.html'; //views/templates/mail/

$mail = new Mail();
$mail->setMailBody($data, $template_html);
$mail->sendMail('Awesome Subject', 'someone@example.com');
```

###Send email message as an associative array with external HTML template
Create a HTML template file and name it as `sample-2.html`.

```HTML
<table>
  <tr>
    <td style="width:640px; padding:20px;">
      <h2><img src="{SITE_LOGO}" width="49" height="48" alt="{SITE_NAME}" /> {SITE_NAME}</h2>
    <h3>Hello {NAME}</h3>
      <h3>Your email address is: {EMAIL}</h3>
      <p>
        {MESSAGE}
      </p>
      <hr>
      <span>
        {DATE}
      </span>
      <hr>
      <p>
        {SMALL_TEXT}
      </p>
    </td>
  </tr>
</table>
```

Then set up a new mail message.
```PHP
$data = array(
        "NAME"       => 'Juliet & Romeo',
        "EMAIL"      => 'some_email@example.com',
        "MESSAGE"    => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        "DATE"       => date('Y-m-d H:i:s'),
        "SMALL_TEXT" => 'Sapiente, fugiat consequatur illo eaque ipsam expedita sint itaque',
        );

$template_html = 'sample-2.html'; //views/templates/mail/

$mail = new Mail();
$mail->setMailBody($data, $template_html);
$mail->sendMail('Awesome Subject', 'someone@example.com');
```

More examples you can find in file `Sample_controller.php` at `application/controllers` folder

This class is capable to send email message by passing an array as body content, external template HTML/TEXT file or as string.

If you don't pass an email address as a parameter `$to` to  `sendMail($Subject, $to);` methods by default `$to` will be defined with your config `mail_replyto_mail` value.

You can pass any config from the `mail_config.php` into class constructor as an array of values:
```PHP
$config = array(
            'mail_bcc'=>'my_email@example.com',
            'mail_setBcc'=>true
          );

$mail = new Mail($config);
```
this will enable BCC e send a copy as BCC to address my_email@example.com

####Notes
1. If you are not in Composer instead of create new instance class ie: `$mail = new Mail();` just load class normally with:
`$this->load->library('mail');` and use the super object `$this->mail->sendMail();` instead.

2. By default the PHPMailer sends email messages in HTML format and an alternative Plain-Text for those Mail Clients that do not support HTML.
But the plain-text content is by default extract from your HTML body and stripped to a text. Sometimes depending on your HTML structure the format of your plain text
can be unreadable, So if you want to have a specific template for text-plain format just create a .txt file of your HTML template as the same name
ending with .txt extension write it only text with your needs and the Mail plugin will find it and use its contents.

2. Fell free to custom this plugin with your needs.

###Bugs
Have you found a bug? Please open a [new issue](https://github.com/adrianorsouza/codeigniter-phpmailer/issues).

###Author
Adriano Rosa
  - https://twitter.com/adrianorosa

###Further reading about PHPMailer
https://github.com/Synchro/PHPMailer

###Further reading about CodeIgniter
http://ellislab.com/codeigniter
