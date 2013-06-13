<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Render {

   /**
    * initPage is a function I wrote to simplify initializing a page.
    * See its use in index().
    * @author Sterling Hirsh
    */
   public function initPage() {
      $CI =& get_instance();
      $CI->template->add_versioned_css('css/mainstyles.css');
      $CI->template->add_versioned_css('css/jquery.dataTables.css');
      $CI->template->add_versioned_css('bootstrap/css/bootstrap.min.css');
      $CI->template->add_versioned_css('css/DT_bootstrap.css');
      $CI->template->add_versioned_css('css/token-input-facebook.css');
      $CI->template->add_versioned_css('css/token-input.css');
      $CI->template->add_versioned_js('js/jquery-1.7.1.min.js');
      $CI->template->add_versioned_js('bootstrap/js/bootstrap.js');
      $CI->template->add_versioned_js('js/jquery.dataTables.min.js');
      $CI->template->add_versioned_js('js/DT_bootstrap.js');
      $CI->template->add_versioned_js('js/swfobject.js');
      $CI->template->add_versioned_js('js/jquery.uploadify.v2.1.4.min.js');
      $CI->template->add_versioned_js('js/common.js');
   }

   /**
    * Simplify rendering a page. 
    * See its use in Main::index().
    * @author Sterling Hirsh
    */
   public function renderPage($activeTab) {
      $CI =& get_instance();

      $tabs = array(
         '/ExperimentHierarchy' => 'Exp. Hierarchy',
         '/TFSearch' => 'TF Search',
         '/TFSub' => 'TF Subtract',
         '/GeneSummary' => 'Gene Summary',
         '/TranscriptionFactorSummary' => 'TF Summary',
         '/GeneSearch' => 'Gene Search',
         '/TFPop' => 'TF Popularity',
         '/FreqTransfacs' => 'Frequent TFs'
      );

      $data = array(
         'siteName' => 'Team Brovine!',
         'curtime' => time(),
         'tabs' => $tabs,
         'activeTab' => $activeTab,
         'user' => $CI->access_control->user_view()
      );

      $CI->template->write_view('header', 'header.php', $data);
      $CI->template->write_view('footer', 'footer.php', $data);
      $CI->template->render();
   }
}
?>
