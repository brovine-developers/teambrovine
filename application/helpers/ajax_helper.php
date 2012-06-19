<?php

function getSearchJoiner($experiment, $compid, $species) {
   $joiner = 'OR';
   
   if($experiment){
       $first = true;
       $add = " INNER JOIN experiments USING(experimentid)
                WHERE (";

       foreach ($experiment as $exp) {
          if (!$first)
             $add .= $joiner;
          $add .= " experimentid = '$exp' ";
          $first = false;
       }
         
       $add .= ") AND ";
   }
   elseif($compid){
       $first = true;
       $add = " INNER JOIN experiments USING(experimentid) INNER JOIN comparison_types USING(comparisontypeid) WHERE ( ";

       foreach ($compid as $exp) {
          if (!$first)
             $add .= $joiner;
          $add .= " comparisontypeid = '$exp' ";
          $first = false;
       }
         
       $add .= ") AND ";
   }
   elseif($species){
       $first = true;
       $add = " INNER JOIN experiments USING(experimentid) INNER JOIN comparison_types USING(comparisontypeid) WHERE ( ";

       foreach ($species as $exp) {
          if (!$first)
             $add .= $joiner;
          $add .= " species = '$exp' ";
          $first = false;
       }
         
       $add .= ") AND ";
   }
   else {
      $add = " WHERE ";
   }

   return $add;
}

?>
