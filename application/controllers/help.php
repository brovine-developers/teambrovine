<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends CI_Controller {

   public function __construct() {
      parent::__construct();

      $this->load->library('render');
   }

   /**
    * Help Pages.
    */
   public function index() { Help::renderHelpPage('index.php'); }
   public function ProjectGoals() { Help::renderHelpPage('project-goals.php'); }
   public function Glossary() { Help::renderHelpPage('glossary.php'); }

   private function renderHelpPage($pageLocation) {
      $this->config->load('glossary');
      $this->render->initPage();
      $data['defs'] = $this->config->item('glossary');
      $this->template->write_view('content', 'help/' . $pageLocation,
        array('defs' => $this->config->item('glossary')));
      $this->render->renderPage('Help');
   }
}

?>
