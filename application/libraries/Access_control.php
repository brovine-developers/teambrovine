<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Security class - checks whether a user is logged in and has the correct
 * credentials
 */
class Access_control {

   public function __construct() {
      session_start();
      $CI =& get_instance();

      $CI->load->helper('privilege');
   }

   public function check($priv) {
      $sess = $this->vars_set();
      $my_priv = intval($sess['privileges']);

      return ($sess !== false &&
        Privilege::isAllowed($my_priv, $priv));
   }

   /**
    * Just checks to make sure the user is logged in.
    */
   public function logged_in() {
      return $this->check(Privilege::Read);
   }

   private function vars_set() {
      if (isset($_SESSION['username']) &&
          isset($_SESSION['display_name']) &&
          isset($_SESSION['privileges'])) {
         return $_SESSION;
      }

      return false;
   }

   public function login($data) {
      if (isset($data) && isset($data->username) &&
        isset($data->display_name) && isset($data->privileges)) {
         $_SESSION['username'] = $data->username;
         $_SESSION['display_name'] = $data->display_name;
         $_SESSION['privileges'] = $data->privileges;
         return true;
      }

      return false;
   }

   public function logout() {
      session_unset();
      session_destroy();
   }

   public function user_view() {
      if ($this->logged_in()) {
         return array(
           'username' => $_SESSION['username'],
           'display_name' => $_SESSION['display_name'],
           'can_write' => Privilege::isAllowed($_SESSION['privileges'],
             Privilege::Write),
           'can_admin' => Privilege::isAllowed($_SESSION['privileges'],
             Privilege::Admin)
         );
      }

      return false;
   }
}

?>
