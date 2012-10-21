<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

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
      // Default way.
		//$this->load->view('welcome_message');
      
      // Using Template library.
      $this->_initPage();
      // Just keep adding views. Make sure each view is in a tab-pane div
      // with the proper link in the header.
      $this->template->write_view('content', 'home.php');
      $this->template->add_versioned_js('js/mainPage.js');
      $this->_renderPage('Home');
	}

   public function Upload() {
      $this->_initPage();
      $this->template->write_view('content', 'upload.php');
      $this->template->add_versioned_js('js/upload.js');
      $this->_renderPage('Upload');

   }

   public function ExperimentHierarchy() {
      $this->_initPage();
      $showHidden = $this->input->get('showHidden');

      $showHidden = $showHidden ? 'checked' : '';

      $this->template->write_view('content', 'expHierarchy.php', array(
         'showHidden' => $showHidden   
      ));
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->template->add_versioned_js('js/experimentHierarchy.js');
      $this->template->add_versioned_js('js/scripture.js');
      $this->_renderPage('Experiment Hierarchy');
   }

  public function TFSearch() {
      $this->_initPage();
      $showHidden = $this->input->get('showHidden');

      $showHidden = $showHidden ? 'checked' : '';

      $this->template->write_view('content', 'tfSearch.php', array(
         'showHidden' => $showHidden   
      ));
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->template->add_versioned_js('js/tfSearch.js');
      $this->template->add_versioned_js('js/scripture.js');
      $this->_renderPage('TF Search');
   }

   public function GeneSummary() {
      $this->_initPage();
      $this->template->write_view('content', 'geneSumm.php');
      $this->template->add_versioned_js('js/geneSummary.js');
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->_renderPage('Gene Summary');
   }
   
   public function TranscriptionFactorSummary() {
      $this->_initPage();
      $this->template->write_view('content', 'tfSumm.php');
      $this->template->add_versioned_js('js/tfSummary.js');
      $this->template->add_versioned_js('js/jquery.tokeninput.js');
      $this->template->add_versioned_js('js/tokeninput-loader.js');
      $this->_renderPage('Transcription Factor Summary');
   }

   public function GeneSearch() {
	$this->_initPage();
	$this->template->write_view('content', 'geneSearch.php');
	$this->template->add_versioned_js('js/geneSearch.js');
         $this->template->add_versioned_js('js/jquery.tokeninput.js');
         $this->template->add_versioned_js('js/tokeninput-loader.js');
	$this->_renderPage('Gene Search');
   }
   
   public function TfPop() {
      $this->_initPage();
      $this->template->write_view('content', 'tfPop.php');
      $this->template->add_versioned_js('js/tfPop.js');
      $this->_renderPage('TF Popularity');
   }
   
   /**
    * _initPage is a function I wrote to simplify initializing a page.
    * See its use in index().
    * @author Sterling Hirsh
    */
   private function _initPage() {
      $this->template->add_versioned_css('css/mainstyles.css');
      $this->template->add_versioned_css('css/jquery.dataTables.css');
      $this->template->add_versioned_css('bootstrap/css/bootstrap.min.css');
      $this->template->add_versioned_css('css/DT_bootstrap.css');
      $this->template->add_versioned_css('css/token-input-facebook.css');
      $this->template->add_versioned_css('css/token-input.css');
      $this->template->add_versioned_js('js/jquery-1.7.1.min.js');
      $this->template->add_versioned_js('bootstrap/js/bootstrap.js');
      $this->template->add_versioned_js('js/jquery.dataTables.min.js');
      $this->template->add_versioned_js('js/DT_bootstrap.js');
      $this->template->add_versioned_js('js/swfobject.js');
      $this->template->add_versioned_js('js/jquery.uploadify.v2.1.4.min.js');
      $this->template->add_versioned_js('js/common.js');
   }

   /**
    * _renderPage is a function I wrote to simplify rendering a page. 
    * See its use in index().
    * @author Sterling Hirsh
    */
   private function _renderPage($activeTab) {
      $tabs = array(
         './' => 'Home',
         'Upload' => 'Upload',
         'ExperimentHierarchy' => 'Experiment Hierarchy',
         'TFSearch' => 'TF Search',
         'GeneSummary' => 'Gene Summary',
         'TranscriptionFactorSummary' => 'Transcription Factor Summary',
   	   'GeneSearch' => 'Gene Search',
         'TFPop' => 'TF Popularity'
      );
      $data = array(
         'siteName' => 'Team Brovine!',
         'curtime' => time(),
         'tabs' => $tabs,
         'activeTab' => $activeTab
      );
      $this->template->write_view('header', 'header.php', $data);
      $this->template->write_view('footer', 'footer.php', $data);
      $this->template->render();
   }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
