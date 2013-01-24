<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Copy_handler {

   const OwnerEmail = 'dpeterson@calpoly.edu';
   const AdminEmail = 'tcirwin@calpoly.edu';

   public static function contact($email, $text, $subject) {
      return "<a href=\"mailto:$email?" .
             'subject=' . rawurlencode($subject) . '">' .
             $text . '</a>';
   }

   public static function contact_owner($text, $subject)  {
      return self::contact(self::OwnerEmail, $text, $subject);
   }

   public static function contact_admin($text, $subject)  {
      return self::contact(self::AdminEmail, $text, $subject);
   }
}

?>
