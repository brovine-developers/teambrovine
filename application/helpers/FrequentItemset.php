<?php

/**
 * FrequentItemset.php
 * 
 * @desc A class that holds a set and a frequency count associated with that
 *       set.
 *
 * @author Therin Irwin and Trevor Devore
 * @date April 17, 2012
 */
class FrequentItemset {
   private $set;
   private $setNames;
   private $count;
   public $food;
   public $flavor;
   public $sup;
   public $conf;

   public function __construct($set) {
      $this->set = $set;
      $this->count = 0;
      $this->setNames = array(); 
   }

   public function getSet() {
      return $this->set;
   }

   public function getSetNames() {
      return $this->setNames;
   }

   public function setNames($items, $names) {
      $this->setNames = array();

      foreach ($this->set as $item)
         $this->setNames[] = $names[array_search($item, $items)];
   }

   public function getCount() {
      return $this->count;
   }

   public function increment() {
      $this->count += 1;
   }

   public function add($num) {
      $this->count += $num;
   }

   public function __toString() {
      $str = "set: " . implode($this->set, ", ");
      $str .= "; count: " . $this->count;
      return $str;
   }

   public function toArray() {
      return array(
       "set" => $this->set,
       "cnt" => $this->count
      );
   }
}

?>
