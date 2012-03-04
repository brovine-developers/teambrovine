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
        COUNT(g.geneid) as numExps, geneid
       FROM genes g INNER JOIN experiments e USING (experimentid)
       GROUP BY genename
EOT;

      $query = $this->db->query($sql, array());
      
      echo json_encode($query->result());
   }

   public function getLongGene() {
      $this->load->database();
      $geneid = $this->input->get('geneid');
      $sql = <<<EOT
       SELECT geneabbrev, genename, chromosome, start, end, ABS(start - end) as length, sequence
       FROM genes INNER JOIN promoter_sequences USING (geneid)
       WHERE genename = ? 
EOT;

      $query = $this->db->query($sql, array($geneid));
      
      echo json_encode($query->result());
   }

   public function getExpsPerGene() {
      $this->load->database();
      $geneid = $this->input->get('geneid');
      $sql = <<<EOT
       sELECT     label, regulation   FROM genes INNER JOIN
                    experiments
USING (         experimentid            ) Where genename = ?
EOT;

      $query = $this->db->query($sql, array($geneid));
      
      echo json_encode($query->result());
   }

   public function getTFSummary() {
      $this->load->database();
      // This query is slow when written the obvious way:
      /*
      $sql = <<<EOT
       SELECT f.transfac,
        COUNT(DISTINCT f.study) as numStudies,
        COUNT(DISTINCT r.geneid) as numGenes,
        COUNT(*) as numOccs
       FROM factor_matches f INNER JOIN regulatory_sequences r USING (seqid)
       GROUP BY f.transfac
EOT;
       */

      /* This one is wayyyy faster. During my testing, it was around .3 seconds.
       * It could be faster, probably. We could do 3 separate queries and merge
       * the results in PHP. The numOccs query and numStudies query are about .01
       * to .02 seconds each. The numGenes is a little longer at .1 to .2.
       * For simplicity's sake, I'm doing the work in the db, though.
       */
      $sql = <<<EOT
       SELECT *
       FROM 
          (SELECT transfac, count(*) as numOccs
          FROM factor_matches
          GROUP BY transfac) a
        INNER JOIN 
          (SELECT transfac, count(*) as numGenes
          FROM 
             (SELECT DISTINCT transfac, geneid
             FROM factor_matches INNER JOIN regulatory_sequences r USING (seqid)) f1
          GROUP BY transfac) b USING (transfac)
        INNER JOIN
          (SELECT transfac, count(*) as numStudies
          FROM 
             (SELECT DISTINCT transfac, study
             FROM factor_matches) f1
          GROUP BY transfac) c USING (transfac)
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

      $result = $query->result_array();
      foreach ($result as &$row) {
         $row['studyPretty'] = str_replace('/', ' /<br>', $row['study']);
         $row['allRow'] = 0;
      }

      // Get All Count
      $sql = <<<EOT
       SELECT COUNT(seqid) as numTimes
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
       WHERE geneid = ?
EOT;
      $query = $this->db->query($sql, array($geneid));
      $countInfo = $query->row();

      // Add "All" Row.
      $result[] = array(
         'study' => '-',
         'studyPretty' => '-',
         'transfac' => 'All',
         'numTimes' => $countInfo->numTimes,
         'allRow' => 1
      );


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

      $transfacFilter = '';
      $params = array($geneid);

      if ($transfac != 'All' && $study != '-') {
         $transfacFilter = 'AND transfac = ? AND study = ?';
         $params[] = $transfac;
         $params[] = $study;
      }

      $sql = <<<EOT
       SELECT *
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
       WHERE geneid = ? $transfacFilter
EOT;
      $query = $this->db->query($sql, $params);

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
      $seqid = $this->input->post('seqid');
      $sql = <<<EOT
       SELECT regulatory_sequences.*,
        genes.geneabbrev,
        experiments.label,
        comparison_types.species,
        comparison_types.celltype,
       GROUP_CONCAT(transfac SEPARATOR '/') as transfacs,
       GROUP_CONCAT(study SEPARATOR '/') as studies
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
        INNER JOIN genes USING (geneid)
        INNER JOIN experiments USING (experimentid)
        INNER JOIN comparison_types USING (comparisontypeid)
       WHERE seqid = ?
EOT;
      $query = $this->db->query($sql, array($seqid));
      $sequenceInfo = $query->row_array();

      $promoter = $this->fetchPromoter($sequenceInfo['geneid']);
      $this->calculateSingleSequenceFromPromoter($sequenceInfo, $promoter);

      $sequenceInfo['transfacs'] = array_unique(explode('/', $sequenceInfo['transfacs']));
      $sequenceInfo['studies'] = array_unique(explode('/', $sequenceInfo['studies']));

      natcasesort($sequenceInfo['transfacs']);
      natcasesort($sequenceInfo['studies']);

      $sequenceInfo['similar'] = $this->getSimilarSequenceInfo($sequenceInfo);

      $this->load->view('sequenceInfo', array(
         'sequenceInfo' => $sequenceInfo
      ));
   }
   
}

