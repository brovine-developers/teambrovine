<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privilege {
   const Read = 0;
   const Write = 10;
   const Admin = 20;

   public static function isAllowed($has, $requests) {
      return $has >= $requests;
   }
}

?>
