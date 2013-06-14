<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files extends CI_Controller {

   public function __construct() {
      parent::__construct();

      $this->template->set_template('help');
      $this->load->helper('privilege');

      if (!$this->access_control->logged_in()) {
         redirect('auth');
      }
      else {
         $this->load->helper('download');
      }
   }

   /**
    * Serve files out of `files/`
    */
   public function Download() {
      if (($name = $this->uri->segment(2)) !== FALSE
       && file_exists('files/' .$name)) {
         $data = file_get_contents('files/' . $name);
         force_download($name, $data);
      }
      else {
         show_404();
      }
   }
}

?>
