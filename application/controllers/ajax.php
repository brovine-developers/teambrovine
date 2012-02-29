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
        COUNT(DISTINCT e.comparisontypeid) as numComps,
        COUNT(g.geneid) as numExps
       FROM genes g INNER JOIN experiments e USING (experimentid)
       GROUP BY genename
EOT;

      $query = $this->db->query($sql, array());
      
      echo json_encode($query->result());
   }

   public function getTFSummary() {
      $this->load->database();
      $sql = <<<EOT
       SELECT f.transfac,
        COUNT(DISTINCT f.study) as numStudies,
        COUNT(DISTINCT r.geneid) as numGenes
       FROM factor_matches f INNER JOIN regulatory_sequences r USING (seqid)
       GROUP BY f.transfac
EOT;

      $query = $this->db->query($sql);

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

   /**
    * This fetches the promoter sequence for a given gene. We use this
    * when we present regulatory sequence information, since we don't store
    * the actual string in the db. Doing it this way prevents data inconsistencies,
    * but it's slower than storing it.
    *
    */
   private function fetchPromoter($geneid) {
      $sql = <<<EOT
       SELECT sequence
       FROM promoter_sequences 
       WHERE geneid = ?
EOT;
      $query = $this->db->query($sql, array($geneid));
      return $query->row()->sequence;
   }

   private function calculateSingleSequenceFromPromoter(&$row, $promoter) {
      $row['sequence'] = substr($promoter, $row['beginning'], $row['length']);
   }

   /**
    * This takes the info in regulatory_sequences (begin, length) and 
    * gets the data out of sequenceData. We might want to store this instead of
    * calculating it, but it does save space to not duplicate it. Maybe.
    * If we have to store the promoter anyway, it does. I doubt that we'll
    * ever hit a point where this is the slow part, especially because we will
    * likely never do any serious analysis on the sequence while it's in the DB
    * and it should take very little time to calculate.
    */
   private function calculateSequencesFromPromoter(&$sequenceData, $promoter) {
      // We don't store the individual sequences in the db, so we calculate them here.
      foreach ($sequenceData as &$row) {
         $this->calculateSingleSequenceFromPromoter($row, $promoter);
      }
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

      $sequences = $query->result_array();

      $promoter = $this->fetchPromoter($geneid);
      $this->calculateSequencesFromPromoter($sequences, $promoter);
      echo json_encode($sequences);
   }

   /**
    * Given sequence info, give info of similar sequences.
    */
   private function getSimilarSequenceInfo($sequenceInfo) {
      $sql = <<<EOT
       SELECT regulatory_sequences.*
         -- ,       GROUP_CONCAT(DISTINCT transfac ORDER BY transfac SEPARATOR '/') as transfacs,
       -- GROUP_CONCAT(DISTINCT study ORDER BY study SEPARATOR '/') as studies
       FROM regulatory_sequences
      -- INNER JOIN factor_matches USING(seqid)
       WHERE geneid = ?
       AND seqid != ?
       AND beginning = ?
       AND sense = ?
       AND length IN (?, ?)
       -- GROUP BY seqid
EOT;

      $params = array(
         $sequenceInfo['geneid'],
         $sequenceInfo['seqid'],
         $sequenceInfo['beginning'],
         $sequenceInfo['sense'],
         $sequenceInfo['length'] - 1,
         $sequenceInfo['length'] + 1
      );

      $query = $this->db->query($sql, $params);
      $sequences = $query->result_array();
      $promoter = $this->fetchPromoter($sequenceInfo['geneid']);
      $this->calculateSequencesFromPromoter($sequences, $promoter);

      /*
      foreach ($sequences as &$sequence) {
         $sequences['transfacs'] = array_unique(explode('/', $sequences['transfacs']));
         $sequences['studies'] = array_unique(explode('/', $sequences['studies']));
      }
       */
         
      return $sequences;
   }
   
   /**
    * Given a seqid, return the data about a specific regulatory_sequence
    * and its associated factors.
    * This is used for UC-8.
    * We're going to go ahead and search for similar regulatory elements here.
    */
   public function getSequenceInfo() {
      $this->load->database();
      $seqid = $this->input->get('seqid');
      $sql = <<<EOT
       SELECT regulatory_sequences.*,
       GROUP_CONCAT(DISTINCT transfac ORDER BY transfac SEPARATOR '/') as transfacs,
       GROUP_CONCAT(DISTINCT study ORDER BY study SEPARATOR '/') as studies
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
       WHERE seqid = ?
EOT;
      $query = $this->db->query($sql, array($seqid));
      $sequenceInfo = $query->row_array();

      $promoter = $this->fetchPromoter($sequenceInfo['geneid']);
      $this->calculateSingleSequenceFromPromoter($sequenceInfo, $promoter);

      $sequenceInfo['transfacs'] = array_unique(explode('/', $sequenceInfo['transfacs']));
      $sequenceInfo['studies'] = array_unique(explode('/', $sequenceInfo['studies']));

      $sequenceInfo['similar'] = $this->getSimilarSequenceInfo($sequenceInfo);

      echo json_encode($sequenceInfo);
   }
   
}

