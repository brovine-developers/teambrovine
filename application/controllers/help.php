<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends CI_Controller {

   public function __construct() {
      parent::__construct();

      $this->load->library('render');
   }

   /**
    * Help Pages.
    */
   public function index() { Help::renderPage('index.php'); }
   public function ProjectGoals() { Help::renderPage('project-goals.php'); }
   public function Glossary() { Help::renderPage('glossary.php'); }
   public function Technical() { Help::renderPage('brovine-technical.php'); }
   public function DevSetup() { Help::renderPage('dev-setup.php'); }
   public function SQLSchema() { Help::renderPage('sql-schema.php'); }
   public function FreqItemsetGen() { Help::renderPage('freq-itemset-gen.php'); }
   public function UsingBrovine() { Help::renderPage('using-brovine.php'); }

   private function renderPage($pageLocation) {
      $this->config->load('glossary');
      $this->render->initPage();
      $data['defs'] = $this->config->item('glossary');
      $this->template->write_view('content', 'help/' . $pageLocation,
        array('defs' => $this->config->item('glossary')));
      $this->render->renderPage('Help');
   }
}

?>
