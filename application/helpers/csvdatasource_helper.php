<?php

require_once("DataSource.php");
require_once("file_csv_datasource/DataSource.php");

class CSVDataSource implements DataSource {
   private $numBasket;
   private $baskets_csv;
   private $items_csv;

   public function getNextBasket() {
      return $this->moreBaskets()?
       array_slice($this->baskets_csv->getRow($this->numBasket++), 1) : null;
   }

   public function moreBaskets() {
      return $this->baskets_csv->countRows() > $this->numBasket;
   }

   public function initBaskets() { $this->numBasket = 0; }
   public function getItems() { 
      $hdrs = $this->items_csv->getHeaders();
      return $this->items_csv->getColumn($hdrs[0]);
   }

   public function getWholeItem($idx) {
      return $this->items_csv->getRow($idx);
   }

   public function getBasketCount() { return $this->baskets_csv->countRows(); }

   public function __construct($basket_file, $owner_file, $item_file) {
      $this->baskets_csv = new File_CSV_DataSource;
      $this->baskets_csv->load($basket_file);
      
      $this->items_csv = new File_CSV_DataSource;
      $this->items_csv->load($item_file);
   }
}

?>
