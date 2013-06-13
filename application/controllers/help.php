<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends CI_Controller {

   public function __construct() {
      parent::__construct();

      $this->template->set_template('help');
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
   public function ViewDescriptions() { Help::renderPage('view-desc.php'); }

   private function renderPage($pageLocation) {

      if ($pageLocation == 'view-desc.php') {
         $this->template->set_template('default');
         $this->config->load('glossary');
         $this->render->initPage();
         $data['defs'] = $this->config->item('glossary');
      }
      else {
         $this->config->load('glossary');
         $this->render->initPage();
         $data['defs'] = $this->config->item('glossary');
         $this->template->write_view('help_nav', 'help/nav.php', array());
      }

      $this->template->write_view('content', 'help/' . $pageLocation,
        array('defs' => $this->config->item('glossary')));

      $this->render->renderPage('Help');
   }
}

?>
