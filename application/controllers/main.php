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
	public function index()
	{
      // Default way.
		//$this->load->view('welcome_message');
      
      // Using Template library.
      $this->_initPage();
      // Just keep adding views. Make sure each view is in a tab-pane div
      // with the proper link in the header.
      $this->template->write_view('content', 'home.php');
      $this->template->write_view('content', 'testTable.php');
      $this->template->write_view('content', 'upload.php');
      $this->template->write_view('content', 'expHierarchy.php');
      $this->template->add_js('js/mainPage.js');
      $this->_renderPage();
	}

   /**
    * _initPage is a function I wrote to simplify initializing a page.
    * See its use in index().
    * @author Sterling Hirsh
    */
   private function _initPage() {
      $this->template->add_css('css/mainstyles.css');
      $this->template->add_css('css/jquery.dataTables.css');
      $this->template->add_css('bootstrap/css/bootstrap.min.css');
      $this->template->add_css('css/DT_bootstrap.css');
      $this->template->add_js('js/jquery-1.7.1.min.js');
      $this->template->add_js('bootstrap/js/bootstrap.js');
      $this->template->add_js('js/jquery.dataTables.min.js');
      $this->template->add_js('js/DT_bootstrap.js');
      $this->template->add_js('js/swfobject.js');
      $this->template->add_js('js/jquery.uploadify.v2.1.4.min.js');
   }

   /**
    * _renderPage is a function I wrote to simplify rendering a page. 
    * See its use in index().
    * @author Sterling Hirsh
    */
   private function _renderPage() {
      $data = array(
         'siteName' => 'Team Brovine!',
         'curtime' => time()
      );
      $this->template->write_view('header', 'header.php', $data);
      $this->template->write_view('footer', 'footer.php', $data);
      $this->template->render();
   }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
