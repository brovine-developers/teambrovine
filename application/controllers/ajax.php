<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {
   public function getSpeciesList() {
      $this->load->database();
      $sql =<<<EOT
      SELECT DISTINCT species 
      FROM comparison_types
      ORDER BY species
EOT;
      $query = $this->db->query($sql);
      $result = $query->result();
      $out = array();
      foreach ($result as $row) {
         $out[] = array(
            'speciesPretty' => htmlentities(ucfirst($row->species)),
            'species' => htmlentities($row->species)
         );
      }
      echo json_encode($out);
   }

   public function getComparisonList() {
      $this->load->database();
      $curSpecies = $this->input->get('species');
      $sql = <<<EOT
      SELECT *
      FROM comparison_types
      WHERE species = ?
      ORDER BY species, celltype
EOT;
      $query = $this->db->query($sql, array($curSpecies));
      $result = $query->result();
      $out = array();
      foreach ($result as $row) {
         $out[] = array(
            'comparisontypeid' => $row->comparisontypeid,
            'comparison' => ucfirst($row->species) . ": {$row->celltype}"
         );
      }
      echo json_encode($out);
   }

   public function getExperimentList() {
      $this->load->database();
      $comparisonTypeId = $this->input->get('comparisontypeid');
      $sql = <<<EOT
      SELECT experimentid, label, 
       (SELECT COUNT(*)
         FROM genes
         WHERE genes.experimentid = experiments.experimentid
      ) as genecount_all,
       (SELECT COUNT(*)
         FROM genes
         WHERE genes.experimentid = experiments.experimentid
         AND genes.regulation = 'up'
      ) as genecount_up,
       (SELECT COUNT(*)
         FROM genes
         WHERE genes.experimentid = experiments.experimentid
         AND genes.regulation = 'down'
      ) as genecount_down
      FROM experiments
      WHERE comparisontypeid = ?
      ORDER BY label
EOT;
      $query = $this->db->query($sql, array($comparisonTypeId));
      $result = $query->result();

      echo json_encode($query->result());
   }

   public function getGeneList() {
      $this->load->database();
      $experimentid = $this->input->get('experimentid');
      $sql = <<<EOT
       SELECT geneid, geneabbrev, chromosome, start, end, regulation,
        (SELECT COUNT(DISTINCT transfac)
         FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
         WHERE regulatory_sequences.geneid = genes.geneid
        ) as numFactors
       FROM genes
       WHERE experimentid = ?
EOT;
      $query = $this->db->query($sql, array($experimentid));

      echo json_encode($query->result());
   }

   public function getGeneSummary() {
      $this->load->database();
      $sql = <<<EOT
       SELECT geneabbrev, genename, chromosome, start, end,
        count(e.comparisontypeid) as numComps, count(g.geneid) as numExps
       FROM genes g, experiments e where g.experimentid = e.experimentid
       GROUP BY genename
EOT;

      $query = $this->db->query($sql, array());
      
      echo json_encode($query->result());
   }

   public function getTFSummary() {
      $this->load->database();
      $sql = <<<EOT
       SELECT f.transfac, count(DISTINCT f.study) as numStudies,
        count(DISTINCT r.geneid) as numGenes, count(f.transfac) as numOccs
       FROM factor_matches f, regulatory_sequences r, study_pages s
       WHERE f.seqid = r.seqid and f.seqid = s.seqid
       GROUP BY f.transfac
EOT;

      $query = $this->db->query($sql, array());

      echo json_encode($query->result());
   }

   public function getFactorList() {
      $this->load->database();
      $geneid = $this->input->get('geneid');
      $sql = <<<EOT
       SELECT study, transfac, COUNT(seqid) as numTimes
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
       WHERE geneid = ?
       GROUP BY study, transfac
EOT;
      $query = $this->db->query($sql, array($geneid));

      $result = $query->result();
      foreach ($result as &$row) {
         $row->study = str_replace('/', ' /<br>', $row->study);
      }

      echo json_encode($result);
   }
   
   public function getSequenceList() {
      $this->load->database();
      $geneid = $this->input->get('geneid');
      $transfac = $this->input->get('transfac');
      $study = $this->input->get('study');
      $sql = <<<EOT
       SELECT *
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
       WHERE geneid = ? AND transfac = ? and study = ?
EOT;
      $query = $this->db->query($sql, array($geneid, $transfac, $study));

      $result = $query->result_array();

      $sql = <<<EOT
       SELECT sequence
       FROM promoter_sequences 
       WHERE geneid = ?
EOT;
      $query = $this->db->query($sql, array($geneid));
      $sequence = $query->row()->sequence;

      // We don't store the individual sequences in the db, so we calculate them here.
      foreach ($result as &$row) {
         $row['sequence'] = substr($sequence, $row['beginning'], $row['length']);
      }

      echo json_encode($result);
   }
}

