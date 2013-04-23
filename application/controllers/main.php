<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

   public function __construct() {
      parent::__construct();

      $this->load->helper('privilege');

      if (!$this->access_control->logged_in()) {
         redirect('auth');
      }
      else {
         $this->load->library('render');
      }
   }

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()	{
     redirect("ExperimentHierarchy");
	}

   public function Upload() {
      $this->load->helper('http_error');

      if ($this->access_control->check(Privilege::Write)) {
         $this->render->initPage();
         $this->template->write_view('content', 'upload.php');
         $this->template->add_versioned_js('js/upload.js');
         $this->render->renderPage('Upload');
      }
      else {
         $this->render->initPage();
         $this->template->write_view('content', 'error.php', HTTP_Error::E401());
         $this->render->renderPage('error');
      }
   }

   public function ExperimentHierarchy() {
      $this->render->initPage();
      $showHidden = $this->input->get('showHidden');

      $showHidden = $showHidden ? 'checked' : '';

      $this->template->write_view('content', 'expHierarchy.php', array(
         'showHidden' => $showHidden,
         'user' => $this->access_control->user_view()
      ));
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->template->add_versioned_js('js/experimentHierarchy.js');
      $this->template->add_versioned_js('js/scripture.js');
      $this->render->renderPage('Exp. Hierarchy');
   }

  public function TFSearch() {
      $this->render->initPage();
      $showHidden = $this->input->get('showHidden');

      $showHidden = $showHidden ? 'checked' : '';

      $this->template->write_view('content', 'tfSearch.php', array(
         'showHidden' => $showHidden   
      ));
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->template->add_versioned_js('js/tfSearch.js');
      $this->template->add_versioned_js('js/scripture.js');
      $this->render->renderPage('TF Search');
   }

  public function TFSub() {
      $this->render->initPage();
      $showHidden = $this->input->get('showHidden');

      $showHidden = $showHidden ? 'checked' : '';

      $this->template->write_view('content', 'tfSub.php', array(
         'showHidden' => $showHidden   
      ));
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      //$this->template->add_versioned_js('js/tfSub.js');
      //$this->template->add_versioned_js('js/scripture.js');
      //$this->template->add_versioned_js('js/commonjs/bin/brovine.js');
      $this->template->add_versioned_js('js/commonjs/dist/tf-sub.js');
      $this->render->renderPage('TF Subtract');
   }

   public function GeneSummary() {
      $this->render->initPage();
      $this->template->write_view('content', 'geneSumm.php');
      $this->template->add_versioned_js('js/geneSummary.js');
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->render->renderPage('Gene Summary');
   }

   public function TranscriptionFactorSummary() {
      $this->render->initPage();
      $this->template->write_view('content', 'tfSumm.php');
      $this->template->add_versioned_js('js/tfSummary.js');
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->render->renderPage('TF Summary');
   }

   public function GeneSearch() {
      $this->render->initPage();
      $this->template->write_view('content', 'geneSearch.php');
      $this->template->add_versioned_js('js/geneSearch.js');
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->render->renderPage('Gene Search');
   }

   public function TfPop() {
      $this->render->initPage();
      $this->template->write_view('content', 'tfPop.php');
      $this->template->add_versioned_js('js/tfPop.js');
      $this->render->renderPage('TF Popularity');
   }

   public function FreqTransfacs() {
      $this->render->initPage();
      $this->template->write_view('content', 'apSumm.php');
      $this->template->add_js('js/apSumm.js');
      $this->render->renderPage('Frequent TFs');
   }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
