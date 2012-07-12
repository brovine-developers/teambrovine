<?php

interface DataSource {
 
   /**
    * Call this function to reset the next basket to #0.
    */
   public function initBaskets();

   /**
    * Used to get the next basket in the queue. If no more baskets, returns
    * null. Call initBaskets() to start from the beginning again.
    */
   public function getNextBasket();

   /**
    * Returns true if another basket is available, false otherwise.
    */
   public function moreBaskets();

   /**
    * Gets the list of items in the baskets
    */
   public function getItems();

   /**
    * Returns how many baskets are in this dataset.
    */
   public function getBasketCount();

}

?>
