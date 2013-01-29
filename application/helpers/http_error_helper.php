<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HTTP_Error {
   public static final function E401() {
      return array(
         "type" => "401",
         "simple_name" => "Unauthorized",
         "fine_print" => "Unauthorized access due to bad credentials.",
         "simple_desc" => "Your account type is not authorized to change " .
           "the database contents.",
         "solution" => "Ask the owner to give you the privileges to edit " .
           "the database."
      );
   }
}

?>
