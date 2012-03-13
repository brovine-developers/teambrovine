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
      SELECT *, FROM_UNIXTIME(date_edited) as date_edited_pretty
      FROM comparison_types
      WHERE 
EOT;

      for ($i = 0; $i < count($curSpecies); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "species = ?";
      }

      $sql .= " ORDER BY species, celltype";
      
      $query = $this->db->query($sql, $curSpecies);
      $result = $query->result();
      foreach ($result as $row) {
         $row->comparison =  ucfirst($row->species) . ": {$row->celltype}";
         if ($row->date_edited == 0) {
            $row->date_edited_pretty = 'Never';
         }
      }
      echo json_encode($result);
   }

   public function getExperimentList() {
      $this->load->database();
      $comparisonTypeId = $this->input->post('comparisontypeid');
      if (!$comparisonTypeId) {
         $comparisonTypeId = $this->input->get('comparisontypeid');
      }

      if ($comparisonTypeId) {
         $comparisonTypeId = (array) $comparisonTypeId;
      } else {
         $comparisonTypeId = array();
      }

      $sql = <<<EOT
      SELECT experimentid, label, hidden, date_edited,
       FROM_UNIXTIME(date_edited) as date_edited_pretty,
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
      WHERE 

EOT;

      for ($i = 0; $i < count($comparisonTypeId); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "comparisontypeid = ?";
      }
      
      $sql .= " ORDER BY label";
      
      $query = $this->db->query($sql, $comparisonTypeId);
      $result = $query->result();

      foreach ($result as $row) {
         if ($row->date_edited == 0) {
            $row->date_edited_pretty = 'Never';
         }
      }

      echo json_encode($result);
   }

   public function getGeneList($asArray = false, $experimentid = false) {
      $this->load->database();
      $experimentid = $experimentid ?: $this->input->get('experimentid');
      // Count(DISTINCT) is slow, but it works well here.
      $sql = <<<EOT
       SELECT geneid, genes.date_edited, FROM_UNIXTIME(genes.date_edited) as date_edited_pretty,
        genename, geneabbrev, chromosome, 
        start, end, regulation, experimentid,
        COUNT(DISTINCT transfac, study) as numFactors
       FROM genes
         INNER JOIN regulatory_sequences USING (geneid)
         INNER JOIN factor_matches USING(seqid)
       WHERE experimentid = ?
       GROUP BY geneid
EOT;
      $query = $this->db->query($sql, array($experimentid));
      $results = $query->result();

      foreach ($results as $result) {
         if ($result->date_edited == 0) {
            $result->date_edited_pretty = 'Never';
         }
      }

      if ($asArray) {
         return $results;
      } else {
         echo json_encode($results);
      }
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
       SELECT geneabbrev, genename, chromosome, start, end, ABS(start - end)+1 as length, sequence
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
   
   public function getTFDrillSummary() {
      $this->load->database();
      $experimentid = $this->input->get('experimentid');
      $la = $this->input->get('la');
      $la_s = $this->input->get('la_s');
      $lq = $this->input->get('lq');
      $ld = $this->input->get('ld');
      
      $sql = <<<EOT
       SELECT transfac, numOccs, numGenes, numStudies
       FROM 
          (SELECT transfac, seqid, count(*) as numOccs
          FROM factor_matches
          WHERE la > ? AND la_slash > ? AND lq > ? AND ld <= ?
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
             FROM factor_matches
               WHERE la > ? AND la_slash > ? AND lq > ? AND ld <= ?) f1
          GROUP BY transfac) c USING (transfac)
        INNER JOIN regulatory_sequences using (seqid)
        INNER JOIN genes using (geneid)
       WHERE 
EOT;

      for ($i = 0; $i < count($experimentid); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "experimentid = ?";
      }

      $query = $this->db->query($sql, array_merge(array($la, $la_s, $lq, $ld, $la, $la_s, $lq, $ld), $experimentid));

      echo json_encode($query->result());
   }

   public function getTFOccur() {
      $this->load->database();
      
      $tfName = $this->input->get('tf');
      $sql = <<<EOT
       SELECT celltype, species, label, genename, geneabbrev, study, beginning, length, sense
       FROM factor_matches
          inner join regulatory_sequences using (seqid)
          inner join genes using (geneid)
          inner join experiments using (experimentid)
          inner join comparison_types using (comparisontypeid)
       where transfac = ?
EOT;

      $query = $this->db->query($sql, array($tfName));
      $result = $query->result();
      $out = array();
      foreach ($result as $row) {
         $out[] = array(
            'celltype' => $row->celltype,
            'speciesPretty' => ucfirst($row->species),
            'species' => $row->species,
            'label' => $row->label,
            'genename' => $row->genename,
            'geneabbrev' => $row->geneabbrev,
            'study' => $row->study,
            'beginning' => $row->beginning,
            'length' => $row->length,
            'sense' => $row->sense
         );
      }
      
      echo json_encode($out);
   }

   public function getFactorList($asArray = false, $geneid = false) {
      $this->load->database();
      $geneid = $geneid ?: $this->input->get('geneid');
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


      if ($asArray) {
         return $result;
      } else {
         echo json_encode($result);
      }
   }

   //RyanTestFunction
   public function getDistinctFactorList() {
      $this->load->database();

      $minLa = $this->input->get('minLa');
      $minLaSlash = $this->input->get('minLaSlash');
      $minLq = $this->input->get('minLq');
      $maxLd = $this->input->get('maxLd');

      $species = $this->input->get('species');
      $comparisontypeid = $this->input->get('comparisontypeid');
      $experiment = $this->input->get('experiment');

      $sql = <<<EOT
       SELECT DISTINCT study, transfac, COUNT(seqid) as numTimes
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid) INNER JOIN genes USING(geneid) 
EOT;
 
      if($experiment){
          $sql .= " INNER JOIN experiments USING(experimentid)
                   WHERE label = '$experiment' AND
                   factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      elseif($comparisontypeid){
          $sql .= " INNER JOIN experiments USING(experimentid) INNER JOIN comparison_types USING(comparisontypeid)
                   WHERE factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      elseif($species){
          $sql .= " INNER JOIN experiments USING(experimentid) INNER JOIN comparison_types USING(comparisontypeid)
                   WHERE species = '$species' AND
                   factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      else{
         $sql .= " WHERE factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      
      $sql .= " GROUP BY study, transfac";

      $query = $this->db->query($sql, array($minLa, $minLaSlash, $minLq, $maxLd));

      $result = $query->result_array();
      foreach ($result as &$row) {
         $row['studyPretty'] = str_replace('/', ' /<br>', $row['study']);
         $row['allRow'] = 0;
      }

      // Get All Count
      $sql = <<<EOT
       SELECT COUNT(seqid) as numTimes
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid) INNER JOIN genes USING(geneid)
EOT;

      if($experiment){
          $sql .= " INNER JOIN experiments USING(experimentid)
                   WHERE label = '$experiment' AND
                   factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      elseif($comparisontypeid){
          $sql .= " INNER JOIN experiments USING(experimentid) INNER JOIN comparison_types USING(comparisontypeid)
                   WHERE factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      elseif($species){
          $sql .= " INNER JOIN experiments USING(experimentid) INNER JOIN comparison_types USING(comparisontypeid)
                   WHERE species = '$species' AND
                   factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }
      else{
         $sql .= " WHERE factor_matches.la >= ? AND
                   factor_matches.la_slash >= ? AND
                   factor_matches.lq >= ? AND
                   factor_matches.ld <= ?";
      }


      $query = $this->db->query($sql, array($minLa, $minLaSlash, $minLq, $maxLd));
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

public function getComparisonFromGeneList() {
    $this->load->database();
    $genename = $this->input->get('genename');
    $sql = <<<EOT
    SELECT comparison_types.species, comparison_types.celltype, experiments.label
    FROM comparison_types, experiments, genes
    WHERE genes.experimentid = experiments.experimentid AND
          experiments.comparisontypeid = comparison_types.comparisontypeid AND
          genes.genename = ?;
EOT;
    $query = $this->db->query($sql, $genename);
    $result = $query->result();
    $out = array();
    foreach ($result as $row) {
       $out[] = array(
                 'comparison' => ucfirst($row->species) . ": {$row->celltype}",
                 'label' => $row->label
       );
     }
     echo json_encode($out);
  }

public function getGeneFoundListFromDB() {
      $this->load->database();
      $transFacs = $this->input->get('transFacs');
      $studies = $this->input->get('studies');
      
      $isAll = false;

      $sql = <<<EOT
      SELECT DISTINCT genes.genename, genes.regulation
      FROM genes, regulatory_sequences, factor_matches, experiments, comparison_types
      WHERE genes.geneid = regulatory_sequences.geneid AND
            factor_matches.seqid = regulatory_sequences.seqid AND (
EOT;
      for($i = 0; $i < count($transFacs); $i++){
         if($transFacs[$i] == 'All'){
            $isAll = true;
         }
      }
      
      for($i = 0; $i < count($transFacs); $i++){
        if($isAll){
           $sql = str_replace(" AND (", "", $sql);
           break;
        }
        else{
           $sql .= "(factor_matches.transFac = '$transFacs[$i]' AND
                    factor_matches.study = '$studies[$i]') OR ";
        }
      }
     
      if(!$isAll){
         $sql = substr($sql, 0, -4);
         $sql .= ")";
      }
 
      $query = $this->db->query($sql);

      $result = $query->result();
      $out = array();
      foreach ($result as $row) {
        $out[] = array(
                 'genename' => $row->genename,
                 'regulation' => $row->regulation
        );
      }
      echo json_encode($out);
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

   public function getSequenceList($asArray = false, $geneid = false, 
    $transfac = false, $study = false, $allRowSelected = false) {
      $this->load->database();
      $geneid = $geneid ?: $this->input->get('geneid');
      $transfac = $transfac ?: $this->input->get('transfac');
      $study = $study ?: $this->input->get('study');

      $transfacFilter = '';
      $params = array($geneid);

      // If $allRowSelected, don't apply the filter.
      if ($transfac != 'All' && $study != '-' && !$allRowSelected) {
         $transfacFilter = 'AND transfac = ? AND study = ?';
         $params[] = $transfac;
         $params[] = $study;
      }

      $sql = <<<EOT
      SELECT *, regulatory_sequences.date_edited,
        FROM_UNIXTIME(regulatory_sequences.date_edited) as date_edited_pretty
       FROM regulatory_sequences INNER JOIN factor_matches USING(seqid)
       WHERE geneid = ? $transfacFilter
EOT;
      $query = $this->db->query($sql, $params);

      $sequences = $query->result_array();

      foreach ($sequences as &$row) {
         $row['studyPretty'] = str_replace('/', ' /<br>', $row['study']);
         if ($row['date_edited'] == 0) {
            $row['date_edited_pretty'] = 'Never';
         }
      }

      $promoter = $this->fetchPromoter($geneid);
      $this->calculateSequencesFromPromoter($sequences, $promoter);
      if ($asArray) {
         return $sequences;
      } else {
         echo json_encode($sequences);
      }
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
       AND length IN (?, ?, ?)
       -- GROUP BY seqid
EOT;

      $params = array(
         $sequenceInfo['geneid'],
         $sequenceInfo['seqid'],
         $sequenceInfo['beginning'],
         $sequenceInfo['sense'],
         $sequenceInfo['length'] - 1,
         $sequenceInfo['length'],
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
      $seqid = $this->input->get('seqid');
      if (!$seqid) {
         $seqid = $this->input->post('seqid');
      }
      $sql = <<<EOT
       SELECT regulatory_sequences.*,
        genes.geneabbrev,
        genes.genename,
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

      $sql = <<<EOT
       SELECT *, FROM_UNIXTIME(date_edited) as date_edited_pretty
       FROM factor_matches
       WHERE seqid = ?
EOT;
   
      $query = $this->db->query($sql, array($seqid));
      $factorMatchInfo = $query->result_array();
      

      $promoter = $this->fetchPromoter($sequenceInfo['geneid']);
      $this->calculateSingleSequenceFromPromoter($sequenceInfo, $promoter);

      $sequenceInfo['transfacs'] = array_unique(explode('/', $sequenceInfo['transfacs']));
      $sequenceInfo['studies'] = array_unique(explode('/', $sequenceInfo['studies']));

      natcasesort($sequenceInfo['transfacs']);
      natcasesort($sequenceInfo['studies']);

      $sequenceInfo['similar'] = $this->getSimilarSequenceInfo($sequenceInfo);

      foreach ($factorMatchInfo as &$row) {
         $row['studyPretty'] = str_replace('/', ' /<br>', $row['study']);
         if ($row['date_edited'] == 0) {
            $row['date_edited_pretty'] = 'Never';
         }
      }

      $allData = array(
         'sequenceInfo' => $sequenceInfo,
         'factorMatchInfo' => $factorMatchInfo
      );

      echo json_encode($allData);
   }

   public function updateGene() {
      // These are in the order of the params.
      $fields = array('genename', 'geneabbrev', 'chromosome', 
         'start', 'end', 'regulation');

      $geneData = array();
      foreach ($fields as $field) {
         $geneData[] = $this->input->post($field);
      }

      $geneData[] = time();
      $geneData[] = $this->input->post('geneid');

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
      
      // Update experiment list at the end.
      $this->getExperimentList();
   }

   public function updateComparison() {
      $this->load->database();
      $this->db->trans_start();

      $celltype = $this->input->post('celltype');
      $species = $this->input->post('species');
      $compid = $this->input->post('comparisontypeid');

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

      $this->getSpeciesList();
      $this->db->trans_complete();
   }

   public function updateExperiment() {
      $this->load->database();
      $this->db->trans_start();

      $label = $this->input->post('label');
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
   
   public function updateSequence() {
      $this->load->database();
      $this->db->trans_start();

      $beginning = $this->input->post('beginning');
      $length = $this->input->post('length');
      $sense = $this->input->post('sense');
      $seqid = $this->input->post('seqid');

      $sql = <<<EOT
       UPDATE regulatory_sequences SET
        length = ?,
        sense = ?,
        beginning = ?,
        date_edited = ?
       WHERE
        seqid = ?
EOT;
      $query = $this->db->query($sql, 
       array($length, $sense, $beginning, time(), $seqid));
      $this->db->trans_complete();
      $this->getSequenceInfo();
   }
   
   public function updateMatch() {
      $this->load->database();
      $this->db->trans_start();
      $matchid = $this->input->post('matchid');

      $fields = array('study', 'transfac', 'la', 'la_slash',
       'lq', 'ld', 'lpv', 'sc', 'sm', 'spv', 'ppv');

      $updateData = array();
      $sqlParts = '';
      foreach ($fields as $name) {
         $updateData[] = $this->input->post($name);
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

      $sql = <<<EOT
       SELECT genes.experimentid, genes.geneid, factor_matches.seqid
EOT;

      $returnData = array(
         'geneData' => $this->getGeneList(true, $this->input->post('selectedExperimentid')),
         'sequenceData' => $this->getSequenceList(true,
            $this->input->post('selectedGeneid'),
            // Use the new transfac and study.
            $this->input->post('transfac'), 
            $this->input->post('study'),
            $this->input->post('allRowSelected')
         ),
         'factorData' => $this->getFactorList(true, $this->input->post('selectedGeneid'))
      );
      echo json_encode($returnData);
   }
}

