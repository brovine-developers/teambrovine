<?php

require_once("AssociationMining.php");
require_once("FrequentItemset.php");

class Apriori extends AssociationMining {

   /**
    * Finds frequent itemsets of the set of "baskets" baskets. Each basket is
    * a subset of I, where I is every item available. 
    *
    * @param baskets an itemset representing a set of items corresponding to
    *        to one "customer". 
    * @return Array of FrequentItemset's.
    */
   public function findFrequentItemsets() {
      //First Pass
      $freq = array();
      $freq[] = array();
      $num = 1;
      $this->datas->initBaskets();

      foreach ($this->datas->getItems() as $i)
         if (($st = $this->support($tmp = new FrequentItemset(array($i)))) >= $this->minSup
          && $st < $this->maxSup) {
            $tmp->sup = $st;
            $freq[0][] = $tmp;
         }

      while (isset($freq[$num - 1]) && count($freq[$num - 1]) != 0) {
         $candidates = $this->candidateGen($freq[$num - 1]);
         
         $this->datas->initBaskets();
         while (($basket = $this->datas->getNextBasket()) != null) {
            foreach ($candidates as $cand) {
               
               if (count($cand->getSet()) == count(array_intersect($cand->getSet(),
                $basket)))
                  $cand->increment();
            }
         }

         foreach ($candidates as $cand) {
            if ($cand->getCount() / $this->datas->getBasketCount()
             >= $this->minSup) {
               $cand->sup = $cand->getCount() / $this->datas->getBasketCount();
               $freq[$num][] = $cand;
            }
         }

         $num++;
      }

      $this->frequents = array_flatten($freq); 
   }

   public static function candidateGen($itemSets) {
      $candidates = array();
      $itemSetsBreak = array();

      foreach ($itemSets as $i)
         $itemSetsBreak[] = $i->getSet();

      foreach ($itemSetsBreak as $f1) {
         foreach ($itemSetsBreak as $f2) {
            if ($f1 == $f2) {
               break;
            }

            if (arrs_same($f2, $f1)) {
               $cand = array_values(array_unique(array_merge($f2, $f1)));
               $flag = true;

               foreach ($cand as $item)
                  $flag = in_array(array_diff($cand, array($item)), $itemSetsBreak); 
               
               if ($flag) $candidates[] = new FrequentItemset($cand);
            }
         }
      }

      return $candidates;
   }

   public function support($items) {
      $this->datas->initBaskets();

      while (($basket = $this->datas->getNextBasket()) != null ) {

         if (count($items->getSet()) == count(array_intersect($items->getSet(),
          $basket)))
            $items->increment();
      }

      $totB = $this->datas->getBasketCount();
      return $totB == 0 ? 0 : $items->getCount() / $totB; 
   }

   /**
    * This function must call findFrequentItemsets.
    */
   public function findAssociationRules() {
      $this->findFrequentItemsets();      
      $ret = array();
      foreach ($this->frequents as $fItemSet) {
         $k = count($fItemSet->getSet());
         if ($k >= 2) {
            $h = array();
            foreach ($fItemSet->getSet() as $fItem) {
               if (($conf = $this->confidence(array_diff($fItemSet->getSet(), array($fItem)), array($fItem))) >= $this->minConf) {
                  $h[] = array(
                     "lhs" => array_values(array_diff($fItemSet->getSet(), array($fItem))),
                     "rhs" => array($fItem),
                     "sup" => $fItemSet->sup,
                     "conf" => $conf
                  );
               }
            }
            $ret = array_merge($ret,  $h); // + $this->apGenRules();
         }
      }
      $this->assocs = $ret;
   }

   public function confidence($itemsX, $itemsY) {
      $a = 0;
      $b = 0;

      $this->datas->initBaskets();

      while (($basket = $this->datas->getNextBasket()) != null ) {
         $xyUnion = array_merge($itemsX, $itemsY);
         if (sizeof($xyUnion) == sizeof(array_intersect($xyUnion, $basket))) {
            $a = $a + 1;
         }

         if (count($itemsX) == count(array_intersect($itemsX, $basket)))
            $b++;
      }
      return $a/$b;
   }
}

/** 
 * Flattens an array, or returns FALSE on fail. 
 *
 * Taken from: http://php.net/manual/en/function.array-values.php
 * Author: a dot ross at amdev dot eu 30-May-2011 12:21
 */ 
function array_flatten($array) { 
   if (!is_array($array)) 
      return FALSE; 

   $result = array(); 

   foreach ($array as $key => $value) { 
      if (is_array($value)) 
         $result = array_merge($result, array_flatten($value)); 
      else 
         $result[$key] = $value; 
   }
 
   return $result; 
}

function arrs_same($a1, $a2) {
   if (count($a1) != count($a2))
      return false;

   for ($i1 = 0; $i1 < count($a1) - 1; $i1++) {
      if ($a1[$i1 + 1] != $a2[$i1])
         return false;
   }

   return true;
}

?>
