<?php
/**
 * CodeIgniter Mail Plugin Powered by PHPMailer Library
 *
 * @date 2014-04-01 22:42
 *
 * @package Libraries
 * @author  Adriano Rosa (http://adrianorosa.com)
 * @license The MIT License (MIT), http://opensource.org/licenses/MIT
 * @link    https://github.com/adrianorsouza/codeigniter-phpmailer
 * @version 0.1.2
 **/
class Mail extends PHPMailer
{
   private $htmlBody = '';
   private $textBody = '';
   private $isHTML = true;

   public $TemplateFolder;
   public $TemplateOptions;

   public $admin_mail;
   public $admin_name;

   public $from_mail;
   public $from_name;

   public $replyto_mail;
   public $replyto_name;

   public $mail_bcc;
   public $mail_cc;

   public $setBcc = false;
   public $setCc  = false;

   protected $CI;

   function __construct($config = array())
   {
      $this->CI =& get_instance();
      $this->CI->config->load('mail_config');

      $default_config = array(
                              # Mailer config
                              'XMailer'     => 'mail_mailer',
                              'SMTPDebug'   => 'mail_debug',
                              'Debugoutput' => 'mail_debug_output',
                              'SMTPAuth'    => 'mail_smtp_auth',
                              'SMTPSecure'  => 'mail_smtp_secure',
                              'CharSet'     => 'mail_charset',

                              # Template folder & Template Options
                              'TemplateFolder'  => 'mail_template_folder',
                              'TemplateOptions' => 'mail_template_options',

                              # Mailer server settings
                              'Host'     => 'mail_smtp',
                              'Port'     => 'mail_port',
                              'Username' => 'mail_user',
                              'Password' => 'mail_pass',

                              # Mailer config Sender/Receiver Addresses
                              'admin_mail' => 'mail_admin_mail',
                              'admin_name' => 'mail_admin_name',

                              'from_mail'  => 'mail_from_mail',
                              'from_name'  => 'mail_from_name',

                              'replyto_mail' => 'mail_replyto_mail',
                              'replyto_name' => 'mail_replyto_name',

                              # BCC and CC Email Addresses
                              'mail_bcc' => 'mail_bcc',
                              'mail_cc'  => 'mail_cc',

                              # BCC and CC enabling config
                              'setBcc'   => 'mail_setBcc',
                              'setCc'    => 'mail_setCc',
                        );

      $this->isSMTP();
      foreach ($default_config as $key => $value) {
         $this->$key = ( isset($config[$value]) ) ? $config[$value] : $this->CI->config->item($value);
      }
   }

   /**
   * Set Mail Body configuration
   *
   * Format email message Body, this can be an external template html file with a copy
   * of a plain-text like template.txt or HTML/plain-text string.
   * This method can be used by passing a template file HTML name and an associative array
   * with the values that can be parsed into the file HTML by the key KEY_NAME found in your
   * array to your HTML {KEY_NAME}.
   * Other optional ways to format the mail body is available like instead of a template the
   * param $data can be set as an array or string, but param $template_html must be equal to null
   *
   * @update 2014-04-01 01:46
   * @author Adriano Rosa (http://adrianorosa.com)
   * @param mixed  $data [array|string] that contain the values to be parsed in mail body
   * @param string $template_html the external html template filename , OR the message as HMTL string
   * @param string $format [HTML|TEXT]
   * @return string
   */
   public function setMailBody($data, $template_html = null, $format = 'HTML')
   {
      if ( !is_array($data) && $template_html == null ) {

         if ( $format == 'TEXT' ) {
            $this->isHTML = false;
            return $this->textBody = $data;
         }
         return $this->htmlBody = $data;

      } elseif ( is_array($data) && $template_html == null ) {

         return $this->htmlBody = implode('<br>  ', $data);

      } else {

         $templatePath = ($this->TemplateFolder)
               ? $this->TemplateFolder . '/' . $template_html
               : $template_html;
         // Support load different path to views available in CI v3.0
         if ( defined('VIEWPATH') ) {
            $views_path = VIEWPATH;
         } else {
            $views_path = APPPATH .'views/';
         }

         if ( !file_exists( $views_path . $templatePath ) ) {

            log_message('error','setEmailBody() HTML template file not found: ' . $template_html);
            return $this->htmlBody = 'Template ' . ($template_html) . ' not found.'; //'none template message found in: ' .$template_html;

         } else {

            $this->htmlBody = $this->CI->load->view($templatePath, '', true);

            if ( preg_match('/\.txt$/', $template_html) ) {

               $this->textBody = $this->htmlBody;

            } else {

               $templateTextPath = preg_replace('/\.[html|php|htm]+$/', '.txt', $templatePath);

               if ( file_exists( $views_path . $templateTextPath ) ) {

                  $this->textBody = $this->CI->load->view($templateTextPath, '', true);
               }
            }

         }

         $data = (is_array($data)) ? $data : array($data);
         $data = array_merge($data, $this->TemplateOptions);

         if ( $format == 'HTML' ) {

            foreach ($data as $key => $value) {
               $this->htmlBody = str_replace("{".$key."}", "".$value."", $this->htmlBody);
               $this->textBody = str_replace("{".$key."}", "".$value."", $this->textBody);
            }

         } elseif ( $format == 'TEXT' ) {

            $this->isHTML = false;
            $this->textBody = @vsprintf($this->textBody, $data);

         }
      }
   }

   /**
    * sendMail with PHPMailer Library
    *
    * @see PHPMailer
    * @see SMTP
    * @param string $subject set a string to be a email message subject
    * @param mixed  $mailto [array|string] the mail address or both Mail and Name as an array
    *        if its not defined the config replyTo will be used instead
    * @param string $mailtoName can be defined followed by $mailto as long as it is not an array
    * @return bool
    */
   public function sendMail($subject = null, $mailto = null, $mailtoName = null)
   {
      # mailer send FROM
      $this->setFrom($this->from_mail, $this->from_name);

      # mailer Reply TO
      $this->addReplyTo($this->replyto_mail, $this->replyto_name);

      # mailer set BCC
      ( $this->setBcc ) ? $this->addBcc($this->mail_bcc) : false;

      # mailer set CC
      ( $this->setCc ) ? $this->addCc($this->mail_cc) : false;

      # mailer send TO --> If $mailto is null by default the replyTo config mail value will be used instead
      if ( is_null($mailto) ) {

         $this->addAddress($this->replyto_mail, $this->replyto_name);

      } else {
         ( is_array($mailto) ) ? $this->addAddress($mailto[0], $mailto[1]) : $this->addAddress($mailto, $mailtoName);
      }

      # mailer message subject
      $this->Subject = $subject;
      # mailer line wrap for text/plain format: see RFC 2646;
      $this->WordWrap = 72;

      if ( !$this->isHTML ) {
         # format only plain/text format
         $this->isHTML(false);
         $this->Body = $this->textBody;

      } else {
         # format message as HTML and alternatively text/plain for
         # those Email Client that do not support HTML format such as Eudora
         $this->msgHTML($this->htmlBody);
         # If it is set a template.txt file, its contents will be parsed instead
         # of strip html tags form the content HTML body
         # the use of plain-text as template might be better than just let the html be stripped to
         # a text format wich can get some break lines and undesirable message body
         if ( $this->textBody ) {

            $this->AltBody = $this->formatHtml2Text($this->textBody);

         } else {

            $this->AltBody = $this->formatHtml2Text($this->AltBody);

         }
      }

      # to debug the message body in browser set
      # config mail_debug to 'local' in mail_config file
      $this->local_debug();

      if (!$this->send()) {
          log_message('error','PHPMailer Error : mail address --> ' . $mailto);
         return false;
      } else {
         return true;
      }
   }

   /**
    * Strips extra whitespace, breaklines.
    *
    * This is a workaround to format correctly plain text body messages.
    * if we use the function AltBody to auto format our html from the
    * template the text message can get multiple break lines and spaces.
    *
    * @param string $str
    * @return string
    */
   private function formatHtml2Text($str) {

      return preg_replace('/\s{2,}/u',  "\n\r", $str) ;
   }

   /**
   * print mail message html and text in the browser
   * @access public
   * @return void
   */
   public function local_debug()
   {
      if ( $this->SMTPDebug === 'local' ) {
         $html = "
<div style=\"margin:0 20px; clear:both; word-wrap:break-word;\">
<pre>
<strong>From: {$this->from_name} &lt;{$this->from_mail}&gt</strong>
To: {$this->to[0][1]} &lt;{$this->to[0][0]}&gt;
<span style=\"color:#999\">Reply-To: {$this->replyto_name} &lt;{$this->replyto_mail}&gt;</span>
Subject: {$this->Subject}
</pre>
</div>
<div style=\"margin:20px; float:left; width:45%\">
<h2>Html Format:</h2>
<hr>
{$this->Body}
</div>
<div style=\"margin:20px; float:right; width:45%; word-wrap:break-word;\">
<h2>Plain/text Format:</h2>
<hr>
<pre>{$this->AltBody}</pre>
</div>";

         print_r($html); exit();
      }
   }
}
