<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Form_validation Class
 *
 * Extends Form_Validation library
 *
 * Allows for custom error messages to be added to the error array
 */
class MY_Form_validation extends CI_Form_validation {

   public function set_error($name = '', $error = '') {
      if (empty($error) || empty($name)) {
         return false;
      }

      $CI =& get_instance();
      $CI->form_validation->_error_array[$name] = $error;
      return true;
   }
}

?>
