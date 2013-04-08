<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contains all AJAX functions that require write privileges on
 * the database. So Hide/Show row and editing each type of record,
 * including experiment, species, etc.
 */
class Edit extends CI_Controller {

   /**
    * Check that the user has write privileges
    */
   public function __construct() {
      parent::__construct();

      $this->load->helper('privilege');

      if (!$this->access_control->check(Privilege::Write)) {
         redirect('auth');
      }
      else {
         $this->load->library('render');
      }
   }

   public function gene() {
      // These are in the order of the params.
      $fields = array('genename', 'geneabbrev', 'chromosome', 
         'start', 'end', 'regulation');

      $geneData = array();
      foreach ($fields as $field) {
         $geneData[] = strip_tags($this->input->post($field, true));
      }

      $geneData[] = time();
      $geneData[] = $geneid = $this->input->post('geneid');

      $promoter = strip_tags($this->input->post('promoter', true));

      $sql = <<<EOT
       UPDATE genes SET
        genename = ?,
        geneabbrev= ?,
        chromosome = ?,
        start = ?,
        end = ?,
        regulation = ?,
        date_edited = ?
       WHERE
        geneid = ?
EOT;

      $this->load->database();
      $query = $this->db->query($sql, $geneData);

      $sql = <<<EOT
       UPDATE promoter_sequences SET
        sequence = ?
       WHERE
        geneid = ?
EOT;

      $this->db->query($sql, array($promoter, $geneid));
   }

   public function comparison() {
      $this->load->database();
      $this->db->trans_start();

      $celltype = $this->input->post('celltype', true);
      $species = $this->input->post('species', true);
      $compid = $this->input->post('comparisontypeid');

      $celltype = strip_tags($celltype);
      $species = strip_tags($species);

      $comparisonData = array($celltype, $species, time(), $compid);

      // Get if one exists.
      $sql = <<<EOT
       SELECT comparisontypeid
       FROM comparison_types
       WHERE celltype = ?
       AND species = ?
       AND comparisontypeid != ?
EOT;
      $query = $this->db->query($sql, array($celltype, $species, $compid));
      $row = $query->row();

      $existingCompId = false;
      if ($row) {
         $existingCompId = $row->comparisontypeid;
      }

      if (!$existingCompId) {
         // If there's no existing comparison with that celltype and species, 
         // update the existing row.
         $sql = <<<EOT
          UPDATE comparison_types SET
           celltype = ?,
           species = ?,
           date_edited = ?
          WHERE
           comparisontypeid = ?
EOT;

         $query = $this->db->query($sql, $comparisonData);
      } else {
         // If there's an existing comparison, merge the two.
         // Update everything pointing at the old one to the new one.
         $sql = <<<EOT
          UPDATE experiments SET
           comparisontypeid = ?
          WHERE comparisontypeid = ?
EOT;
         $query = $this->db->query($sql, array(
            $existingCompId, $compid)
         );

         // Delete the old one.
         $sql = <<<EOT
          DELETE FROM comparison_types WHERE
           comparisontypeid = ?
EOT;
         $query = $this->db->query($sql, array($compid));
      }

      $this->db->trans_complete();
   }

   public function experiment() {
      $this->load->database();
      $this->db->trans_start();

      $label = strip_tags($this->input->post('label', true));
      $expid = $this->input->post('experimentid');

      $sql = <<<EOT
       UPDATE experiments SET
        label = ?,
        date_edited = ?
       WHERE
        experimentid = ?
EOT;
      $query = $this->db->query($sql, array($label, time(), $expid));
      $this->db->trans_complete();
   }
   
   public function sequence() {
      $this->load->database();
      $this->db->trans_start();

      $seqData = array();
      $fields = array('beginning', 'length', 'sense', 'seqid');

      foreach ($fields as $field) {
         $seqData[] = strip_tags($this->input->post($field, true));
      }

      // Splice in the edited time
      array_splice($seqData, count($seqData - 1), 0, array(time()));

      $sql = <<<EOT
       UPDATE regulatory_sequences SET
        beginning = ?,
        length = ?,
        sense = ?,
        date_edited = ?
       WHERE
        seqid = ?
EOT;
      $query = $this->db->query($sql, $seqData);
      $this->db->trans_complete();
   }
   
   public function match() {
      $this->load->database();
      $this->db->trans_start();
      $matchid = $this->input->post('matchid');

      $fields = array('study', 'transfac', 'la', 'la_slash',
       'lq', 'ld', 'lpv', 'sc', 'sm', 'spv', 'ppv');

      $updateData = array();
      $sqlParts = '';
      foreach ($fields as $name) {
         $updateData[] = strip_tags($this->input->post($name, true));
         $sqlParts .= "$name = ?,\n";
      }

      $updateData[] = time();
      $updateData[] = $matchid;

      $sql = <<<EOT
       UPDATE factor_matches SET
        $sqlParts
        date_edited = ?
       WHERE
        matchid = ?
EOT;
      $query = $this->db->query($sql, $updateData);
      $this->db->trans_complete();
   }

   public function toggleRow() {
      $this->load->database();
      $this->db->trans_start();

      $field = $this->input->post('field');
      $tablesByField = array(
         'comparisontypeid' => 'comparison_types',
         'experimentid' => 'experiments',
         'geneid' => 'genes',
         'seqid' => 'regulatory_sequences',
         'matchid' => 'factor_matches'
      );

      if (!isset($tablesByField[$field])) {
         echo "Invalid field: $field";
         return;
      }

      $table = $tablesByField[$field];
      $isHidden = $this->input->post('isHidden');
      $newHidden = $isHidden ? 0 : 1;

      $sql = <<<EOT
       UPDATE {$table}
       SET hidden = ?
       WHERE {$field} = ?
EOT;

      $this->db->query($sql, array($newHidden,
        $this->input->post('value', true)));
      $this->db->trans_complete();
   }
}

?>
