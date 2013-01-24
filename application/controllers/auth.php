<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

   public function __construct() {
      parent::__construct();

      $this->load->helper('privilege');
      $this->load->library('render');
      $this->load->library('form_validation');
   }

   /**
    * Index Page for this controller.
    *
    * Maps to the following URL
    * http://example.com/index.php/welcome
    */
   public function index() {
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');

      if ($this->form_validation->run() !== false) {
         $user = $this->input->post('username');
         $pass = $this->input->post('password');
         $this->load->model('user_model');

         $passed = $this->user_model->verify($user, $pass);

         if ($passed !== false) {
            if ($this->access_control->login($passed)) {
               redirect('ExperimentHierarchy');
            }
            else {
               $this->form_validation->set_error('unknown', 'Please ' .
                 Copy_handler::contact_admin('contact the site administrator',
                 'Login Error: Session not set.') . '; an error has occurred.');
            }
         }
         else {
            $this->form_validation->set_error('credentials',
              'Incorrect username or password.');
         }
      }

      if (!$this->access_control->logged_in()) {
         $this->render->initPage();
         $this->template->write_view('content', 'login.php');
         $this->render->renderPage('Login');
      }
      else {
         redirect('ExperimentHierarchy');
      }
   }

   public function login() {
      redirect('auth/index');
   }

   public function logout() {
      $this->access_control->logout();

      $this->render->initPage();
      $this->template->write_view('content', 'login.php');
      $this->render->renderPage('Login');
   }

   public function settings() {
      if (!$this->access_control->logged_in()) {
         redirect('auth');
      }
      else {
         $this->form_validation->set_rules(array(
            array(
               'field' => 'curpass', 'label' => 'Current Password',
               'rules' => 'required'
            ),
            array(
               'field' => 'password', 'label' => 'Password',
               'rules' => 'min-length[10]|matches[passconf]'
            ),
            array(
               'field' => 'passconf', 'label' => 'Password Confirmation',
               'rules' => 'min-length[10]'
            ),
            array(
               'field' => 'display_name', 'label' => 'Display Name',
               'rules' => 'min-length[5]|max-length[60]'
            )
         ));

         $user = $this->access_control->user_view();

         if ($this->form_validation->run() !== false) {
            $pass = $this->input->post('password');
            $name = $this->input->post('display_name');
            $curPass = $this->input->post('curpass');
            $passConf = $this->input->post('passconf');

            $this->load->model('user_model');

            $update = $this->user_model
              ->update($user['username'], $curPass, $pass, $name);

            if ($update === true) {
               redirect('main');
            }
            else {
               $this->form_validation->set_error('unknown', $update);
            }
         }

         $this->render->initPage();
         $this->template->write_view('content', 'settings.php',
           array('user' => $user));
         $this->render->renderPage('Settings');
      }
   }
}

?>
