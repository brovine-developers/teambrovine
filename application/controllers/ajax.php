<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define("ASCII_A", 97);
define("CSV_PATH", "/home/tcirwin/prj/brovine/genedata-uploads/");

class Ajax extends CI_Controller {
   const FrqItmsetPort = 8100;
   const FrqItmsetHost = 'tcp://localhost';
   const MaxReadLen = 10000;

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

   public function getFrequentItemsets() {
      $min_sup = $this->input->get_post('min_sup');
      $max_sup = $this->input->get_post('max_sup');

      $write_buf = "get $min_sup $max_sup\n";
      $errno = 0;
      $errstr = '';

      // Open a new socket
      $socket = stream_socket_client(self::FrqItmsetHost . ':' . self::FrqItmsetPort, $errno, $errstr);

      // Log any error messages
      if ($errno != 0 || $errstr != '') {
         log_message('error', "stream_socket_client: $errstr ($errno)<br />\n");
      }

      /*// Attempt to connect to the frequent itemset server
      if (!socket_connect($socket, self::FrqItmsetHost, self::FrqItmsetPort)) {
         socket_strerror(socket_last_error());
      }

      // Write the get request to the server
      socket_write($socket, $write_buf);

      // Read the results (the frequent itemsets)
      $data = socket_read($socket, self::MaxReadLen);

      if (socket_read($socket, self::MaxReadLen)) {
         $data = "error: result itemsets too long\n";
      }
*/
      // Write the `get` request and flush
      fwrite($socket, $write_buf);
      fflush($socket);

      // Read and return the result
      echo fread($socket, self::MaxReadLen);
   }

   /**
    * Gets the Apriori data.
    */
   public function getApriori() {
      $this->load->database();
      $this->load->helper("SQLDataSource");
      $this->load->helper("Apriori");

      //$data = new CSVDataSource(CSV_PATH . "factor_baskets_sparse.csv",
      // CSV_PATH . "genes.csv", CSV_PATH . "factors.csv");

      $data = new SQLDataSource($this->db);
      $ap = new Apriori($data, 0.88, 0.95, 0.2);

      $ap->findFrequentItemsets();
      $tmp = $ap->getFrequentNamedItemsets();
      $sets = array();

      foreach ($tmp as $item) {
         $sets[] = array(
            "items" => $item->getSetNames(),
            "count" => $item->getCount(),
            "sup" => $item->sup,
            "numItems" => count($item->getSetNames())
         );
      }

      echo json_encode($sets);
   }

   public function saveFile() {
      $filename = $this->input->get_post('filename');
      $data = $this->input->get_post('data');

      $this->load->helper('download');
      force_download($filename, $data);
   }

   public function getRegHints() {
      $this->load->database();
      $showHidden = $this->showHidden();

      $guess = $this->input->get('q');

      if (!$guess)
         $guess = '';

      $sql = <<<EOT
         SELECT DISTINCT(regulation), MAX(geneid) as gid
         FROM genes
         WHERE regulation LIKE '%$guess%'
           AND hidden <= $showHidden
EOT;

      $query = $this->db->query($sql);
      $ret = $query->result();
      $out = array();
      $i = 0;

      foreach ($ret as $row) {
         $out[] = array(
            'id' => $row->gid,
            'name' => $row->regulation
         );

         $i++;
      }
      
      echo json_encode($out);
   }

   // Returns 1 when we should show hidden, 0 otherwise.
   public function showHidden() {
      return $this->input->get_post('showHidden') ? 1 : 0;
   }
   
   /**
    * Returns a list of high/low values for the metrics specified.
    */
   public function getMetricExtremes() {
      $this->load->database();
      $showHidden = $this->showHidden();
      
      $sql = <<<EOT
         SELECT max(la) as la_max, min(la) as la_min, 
                max(la_slash) as las_max, min(la_slash) as las_min, 
                max(lq) as lq_max, min(lq) as lq_min, 
                max(ld) as ld_max, min(ld) as ld_min
         FROM   factor_matches
         WHERE  hidden <= $showHidden        
EOT;

      $query = $this->db->query($sql);
      $ret = $query->result();
      
      if ($query->num_rows() > 0)
         echo json_encode($ret[0]);
   }

   public function getFactorSubtract() {
      $include = (array) $this->input->get('include', true);
      $exclude = (array) $this->input->get('exclude', true);
      $include_str = "'" . implode("', '", $include) . "'";
      $exclude_str = "'" . implode("', '", $exclude) . "'";

      $this->load->database();
      $restr = $this->getRestrictionSQL();

      $ts_sub = <<<EOT
SELECT distinct p.transfac, COUNT(p.seqid) as numTimes,
        COUNT(DISTINCT p.geneid) as numGenes, 0 as allRow FROM (
  select transfac, seqid, geneid, la, la_slash, lq, ld, beginning, sense
  from `factor_matches`
    inner join `regulatory_sequences` using (seqid)
    inner join `genes` using (geneid)
  where geneid in ($include_str)
) p
left join (
   select transfac from `factor_matches`
     inner join `regulatory_sequences` using (seqid)
     inner join `genes` using (geneid)
   where geneid in ($exclude_str)
) q
on (p.transfac = q.transfac) WHERE
q.transfac is null and $restr
group by p.transfac
order by p.transfac
EOT;


      $query = $this->db->query($ts_sub);
      $result = $query->result();

      echo json_encode($result);
   }

   public function getSpeciesList() {
      $this->load->database();
      $sql =<<<EOT
      SELECT species, MIN(hidden) as hidden, MAX(date_edited) as date_edited
      FROM comparison_types
      WHERE hidden <= ?
      GROUP BY species
      ORDER BY species
EOT;
      $query = $this->db->query($sql, array($this->showHidden()));
      $result = $query->result();
      $out = array();
      foreach ($result as $row) {
         $out[] = array(
            'speciesPretty' => htmlentities(ucfirst($row->species)),
            'species' => htmlentities($row->species),
            'hidden' => $row->hidden,
            'date_edited' => $row->date_edited
         );
      }
      echo json_encode($out);
   }

   public function getComparisonList() {
      $this->load->database();
      $curSpecies = (array) $this->input->get('species');
      
      $sql = <<<EOT
      SELECT *, FROM_UNIXTIME(date_edited) as date_edited_pretty
      FROM comparison_types
      WHERE (
EOT;

      for ($i = 0; $i < count($curSpecies); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "species = ?";
      }

      $sql .= " ) AND hidden <= ?
        ORDER BY species, celltype";
      
      $curSpecies[] = $this->showHidden();

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
      
      $showHidden = $this->showHidden();

      $sql = <<<EOT
      SELECT experimentid, label, hidden, date_edited,
       FROM_UNIXTIME(date_edited) as date_edited_pretty,
       (SELECT COUNT(*)
         FROM genes
         WHERE genes.experimentid = experiments.experimentid
         AND hidden <= $showHidden
      ) as genecount_all
      FROM experiments
      WHERE 
      hidden <= $showHidden AND (

EOT;

      for ($i = 0; $i < count($comparisonTypeId); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "comparisontypeid = ?";
      }
      
      $sql .= ") ORDER BY label";
      
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

      if ($experimentid) {
         $experimentid = (array) $experimentid;
      } else {
         $experimentid = array();
      }

      $showHidden = $this->showHidden();
      // Count(DISTINCT) is slow, but it works well here.
      $sql = <<<EOT
       SELECT geneid, genes.date_edited, FROM_UNIXTIME(genes.date_edited) as date_edited_pretty,
        genename, geneabbrev, chromosome, genes.hidden,
        start, end, regulation, experimentid, species, celltype, label,
        COUNT(DISTINCT transfac, study) as numFactors
       FROM genes
         INNER JOIN regulatory_sequences USING (geneid)
         INNER JOIN factor_matches USING(seqid)
         INNER JOIN experiments USING(experimentid)
         INNER JOIN comparison_types USING(comparisontypeid)
       WHERE (
EOT;

      for ($i = 0; $i < count($experimentid); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "experimentid = ?";
      }
       
      $sql .=  ") AND genes.hidden <= $showHidden 
       AND regulatory_sequences.hidden <= $showHidden 
       AND factor_matches.hidden <= $showHidden 
       GROUP BY geneid";

      $query = $this->db->query($sql, $experimentid);
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
      $showHidden = $this->showHidden();
      $sql = <<<EOT
       SELECT geneabbrev, genename, chromosome, start, end,
        COUNT(DISTINCT e.comparisontypeid) as numComps,
        COUNT(g.geneid) as numExps, geneid
       FROM genes g INNER JOIN experiments e USING (experimentid)
       WHERE g.hidden <= $showHidden AND e.hidden <= $showHidden
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

   public function getPromoter() {
      $this->load->database();
      $geneid = $this->input->get_post('geneid');

      $promoter = $this->fetchPromoter($geneid);

      echo json_encode(array('promoter' => $promoter));
   }

   public function getExpsPerGene() {
      $this->load->database();
      $geneid = $this->input->get('geneid');
      $showHidden = $this->showHidden();
      $sql = <<<EOT
       SELECT label, celltype, species, regulation
       FROM genes INNER JOIN experiments USING (experimentid)
       INNER JOIN comparison_types USING (comparisontypeid)
       WHERE genename = ?
       AND genes.hidden <= $showHidden
       AND experiments.hidden <= $showHidden
EOT;

      $query = $this->db->query($sql, array($geneid));
      
      echo json_encode($query->result());
   }

   public function getTFSummary() {
      $this->load->database();
      $showHidden = $this->showHidden();
      
      $sql = <<<EOT
       SELECT *
       FROM 
          (SELECT transfac, count(*) as numOccs
          FROM factor_matches
          WHERE hidden <= $showHidden
          GROUP BY transfac) a
        INNER JOIN 
          (SELECT transfac, count(*) as numGenes
          FROM 
             (SELECT DISTINCT transfac, geneid
             FROM factor_matches INNER JOIN regulatory_sequences r USING (seqid)
             WHERE factor_matches.hidden <= $showHidden
             AND r.hidden <= $showHidden
            ) f1
          GROUP BY transfac) b USING (transfac)
        INNER JOIN
          (SELECT transfac, count(*) as numStudies
          FROM 
             (SELECT DISTINCT transfac, study
             FROM factor_matches
             WHERE hidden <= $showHidden) f1
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

      $showHidden = $this->showHidden();

      $sql = <<<EOT
       SELECT transfac, numOccs, numGenes, numStudies
       FROM 
          (SELECT transfac, seqid, count(*) as numOccs
          FROM factor_matches
          WHERE la > ? AND la_slash > ? AND lq > ? AND ld <= ?
          AND hidden <= $showHidden
          GROUP BY transfac) a
        INNER JOIN 
          (SELECT transfac, count(*) as numGenes
          FROM 
             (SELECT DISTINCT transfac, geneid
             FROM factor_matches INNER JOIN regulatory_sequences r USING (seqid)
             WHERE factor_matches.hidden <= $showHidden
             AND r.hidden <= $showHidden
            ) f1
          GROUP BY transfac) b USING (transfac)
        INNER JOIN
          (SELECT transfac, count(*) as numStudies
          FROM 
             (SELECT DISTINCT transfac, study
             FROM factor_matches
               WHERE la > ? AND la_slash > ? AND lq > ? AND
                ld <= ? AND hidden <= $showHidden) f1
          GROUP BY transfac) c USING (transfac)
        INNER JOIN regulatory_sequences using (seqid)
        INNER JOIN genes using (geneid)
        INNER JOIN experiments using (experimentid)
        INNER JOIN comparison_types using (comparisontypeid)
       WHERE genes.hidden <= $showHidden
       AND regulatory_sequences.hidden <= $showHidden
EOT;

      for ($i = 0; $i < count($experimentid); $i++) {
         if ($i != 0) {
            $sql .= " OR ";
         } else {
            $sql .= " AND (";
         }
         $sql .= "experimentid = ?";
      }
      if (count($experimentid)) {
         $sql .= ") ";
      }

      $query = $this->db->query($sql, array_merge(array($la, $la_s, $lq, $ld, $la, $la_s, $lq, $ld), $experimentid));


      echo json_encode($query->result());
   }

   public function getTFOccur() {
      $this->load->database();
      
      $showHidden = $this->showHidden();
      $tfName = $this->input->get('tf');
      $expid = $this->input->get('expid');

       $tfName = is_array($tfName) ? $tfName :
           (trim($tfName) != "" ? array($tfName) : array());

       $expid = is_array($expid) ? $expid :
           (trim($expid) != "" ? array($expid) : array());


      $sql = <<<EOT
       SELECT distinct celltype, species, label, genename, geneabbrev, study, beginning, length, sense, regulation
       FROM factor_matches
         inner join regulatory_sequences using (seqid)
         inner join genes using (geneid)
         inner join experiments using (experimentid)
         inner join comparison_types using (comparisontypeid)
EOT;
      
      for ($i = 0; $i < count($tfName); $i++) {
         $sql .= <<<EOT
         inner join (SELECT distinct geneid
                       FROM factor_matches
                         inner join regulatory_sequences using (seqid)
                       where transfac = ?) i
EOT;
        $sql .= $i . <<<EOT
         using (geneid)
EOT;
      }


      $sql .= " WHERE ( ";
      
      for ($i = 0; $i < count($tfName); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "transfac = ?";
      }

      if (count($expid) > 0) $sql .= " ) AND ( ";

        for ($i = 0; $i < count($expid); $i++) {
            if ($i != 0)
               $sql .= " OR ";
            $sql .= "experimentid = ?";
        }

      $sql .= " ) AND ";

      $sql .= <<<EOT
       factor_matches.hidden <= $showHidden
       AND regulatory_sequences.hidden <= $showHidden
       AND genes.hidden <= $showHidden
       AND experiments.hidden <= $showHidden
       AND comparison_types.hidden <= $showHidden
EOT;

      $query = $this->db->query($sql, array_merge($tfName, $tfName, $expid));
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
            'studyPretty' => str_replace('/', ', ', $row->study),
            'beginning' => $row->beginning,
            'length' => $row->length,
            'sense' => $row->sense,
            'regulation' => $row->regulation
         );
      }
      
      echo json_encode($out);
   }

   private function getRestrictionSQL() {
      $minLa = $this->input->get('minla');
      $minLaSlash = $this->input->get('minlaslash');
      $minLq = $this->input->get('minlq');
      $maxLd = $this->input->get('maxld');

      $minBeg = $this->input->get('minbeg');
      $maxBeg = $this->input->get('maxbeg'); 
      $sense = $this->input->get('sense'); 

      if (!isset($minLa) || trim($minLa) == "")
         $minLa = -99999;

      if (!isset($minLaSlash) || trim($minLaSlash) == "")
         $minLaSlash = -99999;
      
      if (!isset($minLq) || trim($minLq) == "")
         $minLq = -99999;
      
      if (!isset($maxLd) || trim($maxLd) == "")
         $maxLd = 99999;

      if (!isset($minBeg) || trim($minBeg) == "")
         $minBeg = -99999;

      if (!isset($maxBeg) || trim($maxBeg) == "")
         $maxBeg = 99999;

      if (!isset($sense) || trim($sense) == "" || trim($sense) == "All"
        || trim($sense) == "all")
         $sense = "%";

      return " la > $minLa AND la_slash > $minLaSlash AND lq > $minLq AND 
        ld <= $maxLd AND beginning > $minBeg AND beginning < $maxBeg
        AND sense LIKE '$sense' ";
   }

   public function getFactorList($asArray = false, $geneid = false) {
      $this->load->database();
      $geneid = $geneid ?: $this->input->get('geneid');
      $expid = $this->input->get('expid');

      if ($expid) {
         $expid = (array) $expid;
      } else {
         $expid = array();
      }

      if ($geneid) {
         $geneid = (array) $geneid;
      } else {
         $geneid = array();
      }

      $showHidden = $this->showHidden();
      $sql = <<<EOT
       SELECT transfac, COUNT(seqid) as numTimes,
        (MIN(factor_matches.hidden) || MIN(regulatory_sequences.hidden)) as hidden,
        COUNT(DISTINCT geneid) as numGenes
       FROM regulatory_sequences
       INNER JOIN genes using(geneid)
       INNER JOIN factor_matches USING(seqid)
       INNER JOIN experiments using(experimentid)
       WHERE (
EOT;

      for ($i = 0; $i < count($geneid); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "geneid = ?";
      }
      
      $sql .= " ) ";

      if ($expid) {
        $sql .= " AND ( ";

        for ($i = 0; $i < count($expid); $i++) {
           if ($i != 0)
              $sql .= " OR ";
           $sql .= "experimentid = ?";
        }

        $sql .= " ) ";
      }
      
      $restr = $this->getRestrictionSQL();
      $sql .= "AND $restr AND regulatory_sequences.hidden <= $showHidden
       AND factor_matches.hidden <= $showHidden
       GROUP BY transfac";

      $query = $this->db->query($sql, array_merge($geneid, $expid));

      $result = $query->result_array();
      foreach ($result as &$row) {
         $row['allRow'] = 0;
      }

      // Get All Count
      $sql = <<<EOT
       SELECT COUNT(seqid) as numTimes, COUNT(DISTINCT geneid) as numGenes
       FROM regulatory_sequences 
       INNER JOIN factor_matches USING(seqid)
       INNER JOIN genes using(geneid)
       INNER JOIN experiments using(experimentid)
       WHERE (
EOT;

      for ($i = 0; $i < count($geneid); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "geneid = ?";
      }
      
      $sql .= " ) ";

      if ($expid) {
        $sql .= " AND ( ";

        for ($i = 0; $i < count($expid); $i++) {
           if ($i != 0)
              $sql .= " OR ";
           $sql .= "experimentid = ?";
        }

        $sql .= " ) ";
      }

      $sql .= "AND $restr AND regulatory_sequences.hidden <= $showHidden
       AND factor_matches.hidden <= $showHidden";

      $query = $this->db->query($sql, array_merge($geneid, $expid));
      $countInfo = $query->row();

      // Add "All" Row.
      $result[] = array(
         'transfac' => 'All',
         'numTimes' => $countInfo->numTimes,
         'numGenes' => $countInfo->numGenes,
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
      $this->load->helper("Ajax");

      $minLa = $this->input->get('minLa');
      $minLaSlash = $this->input->get('minLaSlash');
      $minLq = $this->input->get('minLq');
      $maxLd = $this->input->get('maxLd');

      if (!isset($minLa) || trim($minLa) == "")
         $minLa = -99999;

      if (!isset($minLaSlash) || trim($minLaSlash) == "")
         $minLaSlash = -99999;
      
      if (!isset($minLq) || trim($minLq) == "")
         $minLq = -99999;
      
      if (!isset($maxLd) || trim($maxLd) == "")
         $maxLd = 99999;
      
      $species = $this->input->get('species');
      $comparisontypeid = $this->input->get('comparisontypeid');
      $experiment = $this->input->get('experiment');

      if ($experiment && !is_array($experiment))
         $experiment = array($experiment);

      if ($species && !is_array($species))
         $species = array($species);

      if ($comparisontypeid && !is_array($comparisontypeid))
         $comparisontypeid = array($comparisontypeid);

      $showHidden = $this->showHidden();
      $add = getSearchJoiner($experiment, $comparisontypeid, $species);

      $start = <<<EOT
       SELECT transfac, numOccs, numGenes, numStudies
EOT;

      $sql = <<<EOT
       FROM 
          (SELECT transfac, seqid, count(*) as numOccs
          FROM factor_matches
          INNER JOIN regulatory_sequences r USING (seqid)
          INNER JOIN genes using (geneid)
          $add
           la > ? AND la_slash > ? AND lq > ? AND ld <= ?
          AND factor_matches.hidden <= $showHidden
          GROUP BY transfac) a
        INNER JOIN 
          (SELECT transfac, count(*) as numGenes
          FROM 
             (SELECT DISTINCT transfac, geneid
             FROM factor_matches
             INNER JOIN regulatory_sequences r USING (seqid)
             INNER JOIN genes using (geneid)
             $add
             la > ? AND la_slash > ? AND lq > ? AND
             ld <= ? AND factor_matches.hidden <= $showHidden
             AND r.hidden <= $showHidden
            ) f1
          GROUP BY transfac) b USING (transfac)
        INNER JOIN
          (SELECT transfac, count(*) as numStudies
          FROM 
             (SELECT DISTINCT transfac, study
             FROM factor_matches
               INNER JOIN regulatory_sequences using (seqid)
               INNER JOIN genes using (geneid)
               $add
                la > ? AND la_slash > ? AND lq > ? AND
                ld <= ? AND factor_matches.hidden <= $showHidden) f1
          GROUP BY transfac) c USING (transfac)
EOT;

      $sqli = $start . $sql;

      $sqli .= " GROUP BY transfac";
      $query = $this->db->query($sqli, array($minLa, $minLaSlash, $minLq, $maxLd, $minLa, $minLaSlash, $minLq, $maxLd, $minLa, $minLaSlash, $minLq, $maxLd));

      $result = $query->result_array();
      foreach ($result as &$row) {
         $row['allRow'] = 0;
      }

      $start = <<<EOT
       SELECT sum(numOccs) as numO, sum(numGenes) as numG, sum(numStudies) as numS
EOT;

      $sqli = $start . $sql; 

      $query = $this->db->query($sqli, array($minLa, $minLaSlash, $minLq, $maxLd, $minLa, $minLaSlash, $minLq, $maxLd, $minLa, $minLaSlash, $minLq, $maxLd));
      $countInfo = $query->row();

      // Add "All" Row.
      $result[] = array(
         'transfac' => 'All',
         'numOccs' => $countInfo->numO,
         'numGenes' => $countInfo->numG,
         'numStudies' => $countInfo->numS,
         'allRow' => 1
      );


      echo json_encode($result);
   }

public function getComparisonFromGeneList() {
    $this->load->database();
    $genename = $this->input->get('genename');
    $showHidden = $this->showHidden();
    $sql = <<<EOT
    SELECT comparison_types.species, comparison_types.celltype, experiments.label
    FROM comparison_types, experiments, genes
    WHERE genes.experimentid = experiments.experimentid AND
          experiments.comparisontypeid = comparison_types.comparisontypeid AND
          genes.genename = ?
          AND genes.hidden <= $showHidden
          AND comparison_types.hidden <= $showHidden
          AND experiments.hidden <= $showHidden
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

public function isCommon($value, $list){
   for($i = 0; $i < sizeof($list); $i++){
     if(!in_array($value, $list[$i])){
        return FALSE;
     }
     // for($j = 0; $j < sizeof($list[$i]); $j++){
       //  if($value != $list[$i][$j]){
         //   return FALSE;
        // }
     // }
   }
   return TRUE;
}

   /**
    * Gets the genes in common with all transfacs specified.
    */
   public function getGeneFoundListFromDB() {
      $this->load->database();
      $transFacs = $this->input->get('transFacs');
      $geneIDList = array(array()); 
      $isAll = false;
      $this->load->helper("Ajax");

      $las = array();
      $minLa = $this->input->get('minLa');
      $minLaSlash = $this->input->get('minLaSlash');
      $minLq = $this->input->get('minLq');
      $maxLd = $this->input->get('maxLd');

      if (!isset($minLa) || trim($minLa) == "")
         $minLa = -99999;

      if (!isset($minLaSlash) || trim($minLaSlash) == "")
         $minLaSlash = -99999;
      
      if (!isset($minLq) || trim($minLq) == "")
         $minLq = -99999;
      
      if (!isset($maxLd) || trim($maxLd) == "")
         $maxLd = 99999;
      
      $species = $this->input->get('species');
      $comparisontypeid = $this->input->get('comparisontypeid');
      $experiment = $this->input->get('experiment');

      if ($experiment && !is_array($experiment))
         $experiment = array($experiment);

      if ($species && !is_array($species))
         $species = array($species);

      if ($comparisontypeid && !is_array($comparisontypeid))
         $comparisontypeid = array($comparisontypeid);

      $showHidden = $this->showHidden();
      $add = getSearchJoiner($experiment, $comparisontypeid, $species);
      
      $sql = <<<EOT
         select a.genename, a.regulation
            from
EOT;

      $las = array(); 
      
      for ($i = 0; $i < count($transFacs); $i++) {
         if ($i != 0)
            $sql .= <<<EOT
             inner join
EOT;

         $sql .= <<<EOT
            (SELECT distinct genename, regulation
            from genes g inner join regulatory_sequences r using(geneid)
            inner join factor_matches f using(seqid)
            $add
            transfac=? and 
           la > ? AND la_slash > ? AND lq > ? AND ld <= ?
          AND g.hidden <= $showHidden) 
EOT;

         $sql .= chr($i + ASCII_A);
         array_push($las, $transFacs[$i], $minLa, $minLaSlash, $minLq, $maxLd);

         if ($i != 0)
            $sql .= <<<EOT
               using(genename) 
EOT;
      }

      $query = $this->db->query($sql, $las);

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
       WHERE 
EOT;

      for ($i = 0; $i < count($geneid); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "geneid = ?";
      }

      $query = $this->db->query($sql, $geneid);
      $results = $query->result();

      $sequences = array();

      foreach ($results as $row) {
         $sequences[] = $row->sequence;
      }

      return $sequences;
   }

   public function getSequenceList($asArray = false, $geneid = false, 
    $transfac = false, $study = false, $allRowSelected = false) {
      $this->load->database();
      $geneid = $geneid ?: $this->input->get('geneid');
      $transfac = $transfac ?: $this->input->get('transfac');

      if ($transfac) {
         $transfac = (array) $transfac;
      } else {
         $transfac = array();
      }

      if ($geneid) {
         $geneid = (array) $geneid;
      } else {
         $geneid = array();
      }

      $transfacFilter = '';

      // If $allRowSelected, don't apply the filter.
      if ($transfac[0] != 'All' && !$allRowSelected) {
         $transfacFilter = ' AND ( ';

          for ($i = 0; $i < count($transfac); $i++) {
             if ($i != 0)
                $transfacFilter .= " OR ";
             $transfacFilter .= "transfac = ?";
          }

         $transfacFilter .= ' ) ';
      }

      $showHidden = $this->showHidden();

      $sql = <<<EOT
      SELECT *, r.date_edited, r.hidden, substr(p.sequence, r.beginning, r.length) as sequence,
       FROM_UNIXTIME(r.date_edited) as date_edited_pretty,
       g.genename, c.species, g.geneabbrev

       FROM regulatory_sequences r 
        INNER JOIN factor_matches f USING(seqid)
        INNER JOIN promoter_sequences p USING(geneid)
        INNER JOIN genes g USING(geneid)
        INNER JOIN experiments e USING(experimentid)
        INNER JOIN comparison_types c USING(comparisontypeid)
       WHERE (
EOT;

      for ($i = 0; $i < count($geneid); $i++) {
         if ($i != 0)
            $sql .= " OR ";
         $sql .= "geneid = ?";
      }
       
      $sql .=  ") $transfacFilter 
       AND r.hidden <= $showHidden
       AND f.hidden <= $showHidden";

      $query = $this->db->query($sql, array_merge($geneid, $transfac));

      $sequences = $query->result_array();

      foreach ($sequences as &$row) {
         $row['studyPretty'] = str_replace('/', ' /<br>', $row['study']);
         if ($row['date_edited'] == 0) {
            $row['date_edited_pretty'] = 'Never';
         }
      }

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
      $showHidden = $this->showHidden();

      $sql = <<<EOT
       SELECT *, substr(p.sequence, r.beginning, r.length) as sequence
       FROM regulatory_sequences r
        INNER JOIN promoter_sequences p USING(geneid)
       WHERE geneid = ?
       AND seqid != ?
       AND beginning = ?
       AND sense = ?
       AND length IN (?, ?, ?)
       AND hidden <= $showHidden
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

      $showHidden = $this->showHidden();
      $sql = <<<EOT
       SELECT regulatory_sequences.*, 
        substr(p.sequence, regulatory_sequences.beginning, regulatory_sequences.length)
         as sequence,
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
        INNER JOIN promoter_sequences p USING (geneid)
       WHERE seqid = ?
       AND genes.hidden <= $showHidden
       AND regulatory_sequences.hidden <= $showHidden
       AND experiments.hidden <= $showHidden
       AND comparison_types.hidden <= $showHidden
EOT;
      $query = $this->db->query($sql, array($seqid));
      $sequenceInfo = $query->row_array();

      $sql = <<<EOT
       SELECT *, FROM_UNIXTIME(date_edited) as date_edited_pretty
       FROM factor_matches
       WHERE seqid = ?
       AND hidden <= $showHidden
EOT;
   
      $query = $this->db->query($sql, array($seqid));
      $factorMatchInfo = $query->result_array();

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
}

