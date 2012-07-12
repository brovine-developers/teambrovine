<?php

define("CLASS_NAME", "AssociationMining");

abstract class AssociationMining {
   private static $classType;
   protected $datas;
   protected $minSup;
   protected $maxSup;
   protected $minConf;
   protected $frequents;
   protected $assocs;

   /**
    * Finds frequent itemsets of the set of "baskets" baskets. Each basket is
    * a subset of I, where I is every item available. 
    *
    * @param baskets an itemset representing a set of items corresponding to
    *        to one "customer". 
    * @return Array of FrequentItemset's.
    */
   public abstract function findFrequentItemsets();

   /**
    * This function must call findFrequentItemsets.
    */
   public abstract function findAssociationRules();
   
   public function getFrequentItemsets() { return $this->frequents; }
   public function getAssociationRules() { return $this->assocs; }

   public function getFrequentNamedItemsets() {
      $names = $this->datas->getItemNames();
      $items = $this->datas->getItems();

      foreach ($this->frequents as $freq)
         $freq->setNames($items, $names);
      
      return $this->frequents;
   }
   
   public static function getInstance() {
      $classType = static::classType;
      return new $classType;
   }   

   public static function setClassType($ct) {
      if (is_subclass_of(CLASS_NAME . $ct))
         static::$classType = $ct;
      else
         throw new Exception("Class: " .$ct. " is not a subclass of ".
          CLASS_NAME); 
   }

   public function __construct($dataSource, $minSup, $maxSup, $minConf) {
      $this->datas = $dataSource;
      $this->minSup = $minSup;
      $this->maxSup = $maxSup;
      $this->minConf = $minConf;
   }
}

?>
