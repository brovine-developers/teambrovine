<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller {
   private static $targetPath = '/var/www/html/genedata-uploads/';

   public function importExisting() {
      $this->load->database();
      $files = glob(self::$targetPath . "*.csv");
      $genesImported = array();
      foreach ($files as $targetFile) {
         if (!file_exists($targetFile))
            continue;
         $returnData = $this->attemptImport($targetFile);
         $returnData['filename'] = $targetFile;
         $fileNames = $this->getFileNames($targetFile);
         $returnData['fileInfo'] = $fileNames;
         
         $whichFiles = $this->whichFilesExist($fileNames['files']);
         $returnData['whichFilesExist'] = 
            array($fileNames['groupName'], $whichFiles['jobParams'],
             $whichFiles['hits'], $whichFiles['sequences']);
         $genesImported[] = $fileNames['groupName'];

         if ($this->allFilesExist($fileNames['files'])) {
            foreach ($fileNames['files'] as $curFile) {
               unlink($curFile);
            }
         }
      }

      echo json_encode($genesImported);
   }

   public function clearAllData() {
      $this->load->database();
      $this->db->trans_begin();
      $tables = array(
         'study_pages',
         'factor_matches',
         'regulatory_sequences',
         'promoter_sequences',
         'genes',
         'experiments',
         'comparison_types'
      );
      $totalRows = 0;
      foreach ($tables as $table) {
         $query = "DELETE FROM $table";
         $this->db->query($query);
         $totalRows += $this->db->affected_rows();
      }
      $this->db->trans_complete();
      $files = glob(self::$targetPath . "*.csv");
      foreach ($files as $file) {
         unlink($file);
      }
      echo "$totalRows Rows Affected";
   }

   /**
    * Return a JSON array with file status.
    */
   public function status() {
      $files = glob(self::$targetPath . "*.csv");

      $filesByGroupName = array();
      foreach ($files as $file) {
         $fileNames = $this->getFileNames($file);
         if (!isset($filesByGroupName[$fileNames['groupName']])) {
            $filesByGroupName[$fileNames['groupName']] = 
             $this->whichFilesExist($fileNames['files']);
         }
      }

      $table = array();
      foreach ($filesByGroupName as $groupKey => $exist) {
         foreach ($exist as &$fileExists) {
            $fileExists = $fileExists ? 'Success' : 'Missing';
         }
         $message = "Previously uploaded.";
         $table[] = array(
            $groupKey, $exist['jobParams'], $exist['hits'], $exist['sequences'], $message);
         
      }

      echo json_encode(array('aaData' => $table));
   }
   public function index() {
      $targetPath = self::$targetPath;
      $targetFile = '';
      if (!empty($_FILES)) {
         $tempFile = $_FILES['Filedata']['tmp_name'];
         $targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
         
         // $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
         // $fileTypes  = str_replace(';','|',$fileTypes);
         // $typesArray = split('\|',$fileTypes);
         // $fileParts  = pathinfo($_FILES['Filedata']['name']);
         
         // if (in_array($fileParts['extension'],$typesArray)) {
            // Uncomment the following line if you want to make the directory if it doesn't exist
            // mkdir(str_replace('//','/',$targetPath), 0755, true);
            
            move_uploaded_file($tempFile,$targetFile);
            //echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
         // } else {
         // 	echo 'Invalid file type.';
         // }
      } else {
         $targetFile = $targetPath . "bACO2 promoter TESS Hits 1.csv";
      }

      if ($targetFile) {
         $this->load->database();
         $fileNames = $this->getFileNames($targetFile);
         $returnData = array();
         $returnData['filename'] = basename($targetFile);

         if (!$fileNames) {
            $exploded = explode(' ', basename($targetFile));
            $returnData['fileInfo'] = array('groupName' => $exploded[0],
               'hits' => false,
               'jobParams' => false,
               'sequences' => false
            );
            $returnData['whichFilesExist'] = array(
               basename($targetFile), false, false, false
            );
            $returnData['success'] = false;
            $returnData['message'] = 
               "Filename is in wrong format.";
               //"Filename must be 'Gene promoter TESS {Hits, Job Parameters, Sequences}.csv'.";
            unlink($targetFile);
         } else {
            $returnData['fileInfo'] = $fileNames;
            $returnData = array_merge($returnData, $this->attemptImport($targetFile));

            $whichFiles = $this->whichFilesExist($fileNames['files']);
            $returnData['whichFilesExist'] = 
               array($fileNames['groupName'], $whichFiles['jobParams'],
                $whichFiles['hits'], $whichFiles['sequences']);

            if ($this->allFilesExist($fileNames['files'])) {
               foreach ($fileNames['files'] as $curFile) {
                  unlink($curFile);
               }
            }
         }

         echo json_encode($returnData);
      }
   }

   private function whichFilesExist($files) {
      $exists = array();
      foreach ($files as $type => $curFile) {
         $exists[$type] = file_exists($curFile);
      }
      return $exists;
      
   }

   private function allFilesExist($files) {
      foreach ($files as $curFile) {
         if (!file_exists($curFile)) {
            return false;
         }
      }
      return true;
   }  

   private function getFileNames($path) {
      $expectedEndings = array(
         'hits'      => array(' promoter TESS Hits 1.csv', ' Hits 1.csv'),
         'jobParams' => array(' promoter TESS Job Parameters.csv', ' Job Parameters.csv'),
         'sequences' => array(' promoter TESS Sequences.csv', ' Sequences.csv') 
      );

      $dirName = dirname($path);
      $fileName = basename($path);
      $groupName = '';
      $which = -1;
      foreach ($expectedEndings as $endingArr) {
         foreach ($endingArr as $idx => $ending) {
            // If the filename ends with $ending, pull out the ending.
            if (substr($fileName, -1 * strlen($ending)) == $ending) {
               $groupName = substr($fileName, 0, -1 * strlen($ending));
               $which = $idx;
               break;
            }
         }

         if ($groupName) break;
      }

      if (!$groupName) {
         return false;
      }
      
      $files = array();
      foreach ($expectedEndings as $fileType => $endingArr) {
         foreach ($endingArr as $idx => $ending) {
            if ($which == $idx) {
               $files[$fileType] = "$dirName/$groupName$ending";
               break;
            }
         }
      }
      return array('files' => $files, 'groupName' => $groupName);
   }

   private function attemptImport($path) {
      $files = $this->getFileNames($path);
      $files = $files['files'];

      if (!$files) {
         return array('success' => false, 'message' => 'Filename is in the wrong format.');
      }

      if (!$this->allFilesExist($files)) {
         return array('success' => true, 'message' => 'Missing files.');
      }

      $sequenceContents = $this->csvToAssoc(file($files['sequences']));

      $sequence = $sequenceContents[0]['Sequence'];
      $jobParamsData = $this->handleJobParams(file($files['jobParams']));
      $hitsData = $this->handleHits(file($files['hits']));

      $dbError =  $this->insertToDatabase($jobParamsData, $hitsData, $sequence);

      if ($dbError) {
         return $dbError;
      }

      return array('success' => true, 'message' => 'Success!');
   }
   
   private function getId($table, $autoIncrementId, $data,
    $searchFields = false) {
      if ($searchFields) {
         $searchData = array();
         foreach ($searchFields as $field) {
            $searchData[$field] = $data[$field];
         }
      } else {
         $searchData = $data;
      }

      $existing = $this->db->get_where($table, $searchData);
      $existing = $existing->row();

      if ($existing) {
         return $existing->$autoIncrementId;
      } else {
         return false;
      }
   }

   private function insertAndGetId($table, $data) {
      $this->db->insert($table, $data);
      return $this->db->insert_id();
   }

   private function insertOrGetId($table, $autoIncrementId, $data,
    $searchFields = false) {
       // Try to get the id first.
       return $this->getId($table, $autoIncrementId, $data, $searchFields) ?:
         // Else insert it.
         $this->insertAndGetId($table, $data);
   }

   /**
    * This does the actual inserting. Returns false on success, error data on failure.
    */
   private function insertToDatabase($jobParamsData, $hitsData, $sequence) {
      $this->load->database();
      $this->db->trans_begin();
      // Check if entry in comparison_types exists.
      // Get id / insert.
      $jobParamsData['comparison_types']['species'] = 
       strtolower($jobParamsData['comparison_types']['species']);
      $comparisonTypeId = $this->insertOrGetId('comparison_types', 'comparisontypeid',
       $jobParamsData['comparison_types']);

      // Check if entry in experiments exists.
      // Get id / insert.
      $jobParamsData['experiments']['comparisontypeid'] = $comparisonTypeId;
      $experimentId = $this->insertOrGetId('experiments', 'experimentid',
       $jobParamsData['experiments'], array('label'));

      // Insert entry in genes. / Get id.
      $jobParamsData['genes']['experimentid'] = $experimentId;

      $geneExists = $this->getId('genes', 'geneid', $jobParamsData['genes'],
       array('experimentid', 'genename'));

      if ($geneExists) {
         return array('success' => false,
          'message' => 'Gene exists for this experiment.');
      }

      $geneid = $this->insertAndGetId('genes', $jobParamsData['genes']);

      // Insert promoter sequence.
      $this->db->insert('promoter_sequences', array(
         'geneid' => $geneid,
         'sequence' => $sequence
      ));

      // Insert entries into regulatory_sequences / Get ids.
      foreach ($hitsData as $sequenceInfo) {
         $sequenceInfo['regulatory_sequences']['geneid'] = $geneid;
         $seqId = $this->insertAndGetId('regulatory_sequences',
          $sequenceInfo['regulatory_sequences']);

         // Insert entries into factor_matches.
         foreach ($sequenceInfo['factor_matches'] as $matchInfo) {
            $matchInfo['seqid'] = $seqId;
            $this->db->insert('factor_matches', $matchInfo);
         }

         // Insert entries into study_pages.
         foreach ($sequenceInfo['study_pages'] as $page) {
            $this->db->insert('study_pages', array('pageno' => $page,
               'seqid' => $seqId));
         }

      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
         return array('success' => false,
            'message' => 'A database error occurred.');
      }

      return false;
   }


   private function handleHits($hitsData) {
      $hitsData = $this->csvToAssoc($hitsData);

      $realFieldNames = array(
         'regulatory_sequences' => array(
            'beginning' => 'Beg',
            'length' => 'Len',
            'sense' => 'Sns'
            // 'seqence' => 'Sequence', // Not stored?
         ),
         'factor_matches' => array(
            'transfac' => 'Factor',
            'study' => 'Model',
            'la' => 'L  a',
            'la_slash' => 'L  a/',
            'lq' => 'L  q',
            'ld' => 'L  d',
            'lpv' => 'L  pv',
            'sc' => 'S  c',
            'sm' => 'S  m',
            'spv' => 'S  pv',
            'ppv' => 'P  pv'
         ),
         'study_pages' => array(
            'pageno' => 'tNumbers'
         )
      );

      $byRegSeq = array();
      $newHitsData = array();
      foreach ($hitsData as &$row) {
         // Expand model and factor.
         $row = $this->parseModelAndFactor($row);
         $assocByGroup = array();
         
         // Group fields.
         foreach ($realFieldNames as $groupKey => $fieldData) {
            $assocByGroup[$groupKey] = array();
            foreach ($fieldData as $fieldKey => $fieldName) {
               $assocByGroup[$groupKey][$fieldKey] = $row[$fieldName];
            }
         }

         // Expand transacs into one row per factor.
         $factorMatchRows = array();
         $tmpRow = $assocByGroup['factor_matches'];

         foreach ($assocByGroup['factor_matches']['transfac'] as $curTransfac) {
            $tmpRow['transfac'] = $curTransfac;
            $factorMatchRows[] = $tmpRow;
         }
         $assocByGroup['factor_matches'] = $factorMatchRows;
         $assocByGroup['study_pages'] = $assocByGroup['study_pages']['pageno'];

         $newHitsData[] = $assocByGroup;
         $regSeqKey = implode('-', $assocByGroup['regulatory_sequences']);

         if (!isset($byRegSeq[$regSeqKey])) {
            $byRegSeq[$regSeqKey] = $assocByGroup;
         } else {
            $byRegSeq[$regSeqKey]['study_pages'] = array_unique(array_merge(
               $byRegSeq[$regSeqKey]['study_pages'],
               $assocByGroup['study_pages']));
            $byRegSeq[$regSeqKey]['factor_matches'] = array_merge(
               $byRegSeq[$regSeqKey]['factor_matches'],
               $assocByGroup['factor_matches']);
         }
      }

      return $byRegSeq;
   }

   private function parseModelAndFactor($row) {
      $model = $row['Model'];
      $factor = $row['Factor'];

      $modelData = explode(' ', trim($model));
      $modelType = $model[0];

      $factorData = explode(' ', trim($factor));

      if ($modelType == 'I' || $modelType == 'M') {
         $row['Model'] = trim($modelData[0]);
      } else {
         // Model type must be R.
         $rNumbers = array();
         // Pull even elements.
         $modelDataCount = count($modelData);
         for ($i = 0; $i < $modelDataCount; $i += 2) {
            $rNumbers[] = $modelData[$i];
         }
         $row['Model'] = implode('/', $rNumbers);
      }

      if ($modelType == 'I') {
         $tNumbers = array();
         $factorNames = array($factorData[1]);
      } else {
         $tNumbers = array();
         $factorNames = array();
         foreach ($factorData as $datum) {
            if (preg_match('/^T\d\d\d\d\d$/', $datum)) {
               $tNumbers[] = $datum;
            } else {
               $factorNames[] = $datum;
            }
         }
      }

      $row['tNumbers'] = $tNumbers;
      $row['Factor'] = array_unique($factorNames);

      return $row;
   }

   private function handleJobParams($jobParamsData) {
      $jobParamsData = $this->csvToArray($jobParamsData);
      $assoc = array();
      foreach ($jobParamsData as $datum) {
         if (isset($datum[1]) && trim($datum[0]))
         $assoc[strtolower(trim($datum[0]))] = trim($datum[1]);
      }

      $realFieldNames = array(
         'comparison_types' => array(
            'species' => 'Species',
            'celltype' => 'Comparison'
         ),
         'experiments' => array(
            'label' => 'Experiment',
            'experimenter_email' => 'Your email address',
            'tessjob' => 'TESS Job',
            'storage_time' => 'Length of time to store results of the job',
            'search_transfac_strings' => 'Search TRANSFAC Strings',
            'search_my_site_strings' => 'Search My Site Strings',
            'selected' => 'Selected?',
            'search_transfac_matrices' => 'Search TRANSFAC Matrices',
            'search_imd_matrices' => 'Search IMD Matrices',
            'search_cbil_matrices' => 'Search CBIL-GibbsMat Matrices',
            'search_jaspar_matrices' => 'Search JASPAR Matrices',
            'search_my_weight_matrices' => 'Search My Weight Matrices',
            'combine_with' => 'Combine with',
            'factor_attr_1' => 'Factor Attribute 1',
            'matches' => 'matches',
            'use_core_positions' => 'Use only core positions for TRANSFAC strings',
            'max_mismatch' => 'Maximum Allowable String Mismatch % (tmm)',
            'min_log_likelihood' => 'Minimum log-likelihood ratio score (ts-a)',
            'min_strlen' => 'Minimum string length (tw)',
            'min_lg' => 'Minimum lg likelihood ratio (ta)',
            'group_selection' => 'Group Selection',
            'max_lg' => 'Maximum lg-likelihood deficit (td)',
            'min_core' => 'Minimum core similarity (tc)',
            'min_matrix' => 'Minimum matrix similarity (tm)',
            'secondary_lg' => 'Secondary Lg-Likelihood Deficit',
            'count_significance' => 'Count significance threshold',
            'pseudocounts' => 'Pseudocounts',
            'use_at' => 'Use A-T Content (%)',
            'explicit_acgt' => 'Explicit A,C,G,T Distribution',
            'handle_ambig' => 'Handle Ambiguous Bases Using'
         ),
         'genes' => array(
            'genename' => 'Gene Name',
            'geneabbrev' => 'Gene Abbreviation',
            'regulation' => 'Regulation',
            'chromosome' => 'Chromosome',
            'start' => 'Begin Site',
            'end' => 'End Site'
         )
      );
      // Not saving the extra data, I guess.

      $assocByGroup = array();

      foreach ($realFieldNames as $groupKey => $fieldData) {
         $assocByGroup[$groupKey] = array();
         foreach ($fieldData as $fieldKey => $fieldName) {
            // Error supression here so we don't get errors with missing data.
            $assocByGroup[$groupKey][$fieldKey] = @$assoc[strtolower($fieldName)];
         }
      }

      return $assocByGroup;
   }
   
   private function csvToArray($lines) {
      return array_map('str_getcsv', $lines);
   }

   private function csvToAssoc($lines) {
      $columnNames = str_getcsv(array_shift($lines));
      array_walk($columnNames, function(&$name) {
         $name = trim($name);
      });

      $data = array();
      foreach ($lines as $line) {
         $lineData = str_getcsv($line);
         $newRow = array();
         foreach ($lineData as $key => $str) {
            $newRow[$columnNames[$key]] = $str;
         }
         $data[] = $newRow;
      }
      return $data;
   }
}

