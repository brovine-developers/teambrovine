<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

   function __construct() {

   }

   function verify($user, $pass) {
      $this->load->database();

      $q = $this->db
         -> where('username', $user)
         -> where('password', sha1($pass))
         -> limit(1)
         -> get('users');

      if ($q->num_rows > 0) {
         return $q->row();
      }

      return false;
   }

   function update($user, $curPass, $pass, $display_name) {
      $this->load->helper('copy_handler');
      $newData = array();

      if (isset($pass) && $pass !== '') {
         $newData['password'] = sha1($pass);
      }

      if (isset($display_name) && $display_name !== '') {
         $newData['display_name'] = $display_name;
      }

      if ($this->verify($user, $curPass) !== false) {
         $q = $this->db
            -> where('username', $user)
            -> where('password', sha1($curPass))
            -> limit(1)
            -> update('users', $newData);

         if ($this->db->affected_rows() <= 1) {
            return true;
         }

         return 'An error has occurred. Please ' .
           Copy_handler::contact_admin('contact the administrator',
           'User was verified but nothing was updated') .
           ' and let them know there was an issue.';
      }

      return "Current Password was incorrect.";
   }
}

?>
