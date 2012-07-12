<?php

define("BUCKET_SIZE", 10000);
require_once("DataSource.php");

define("CLEAR_STAGE", <<<EOT
delete from apriori_staging
EOT
);

define("STAGING_QUERY", <<<EOT
insert into apriori_staging(geneid, tf_cart) /*(select geneid, GROUP_CONCAT(tf_id) from*/
(select a.gid as geneid, min(b.mid) as tf_id
from

   (SELECT ((experimentid - 30) * (select count(*) from genes) + geneid) as gid, geneid
FROM genes
Inner Join experiments
USING (experimentid)) a

   inner join

   regulatory_sequences USING (geneid)

   inner join

(select q.mid, r.seqid, q.transfac
from
   (select min(matchid) as mid, transfac
   from factor_matches group by transfac) q
inner join
   (select seqid, transfac
   from factor_matches) r
using(transfac)) b

   USING (seqid)

group by a.gid, b.mid
/*) i
group by geneid
limit ?, ?*/)
EOT
);

define("BASKET_QUERY", <<<EOT
select geneid, tf_cart
from apriori_staging
where geneid = ?
EOT
); // new line: where geneid...

define("BASKET_NUMBER", <<<EOT
select geneid
from apriori_staging
group by geneid
order by geneid asc
EOT
);

class SQLDataSource implements DataSource {
   private $bucket;
   private $offset;
   private $basketNums;
   private $numBasket;
   private $baskets;
   private $items;
   private $basketCount;
   private $db;
   private $itemNames;

   public function getNextBasket() {
      if ($this->moreBaskets()) {
         $query = $this->db->query(BASKET_QUERY, 
          $this->basketNums[$this->numBasket++]->geneid);

         $basket = array();
         foreach ($query->result() as $row) {
            $basket[] = $row->tf_cart;
         }

         return $basket;
      }
      else return null;
      /*if ($this->numBasket >= $this->offset + BUCKET_SIZE) {
         $this->offset += BUCKET_SIZE;

         $query = $this->db->query(BASKET_QUERY, array($this->offset, BUCKET_SIZE));
         $tmp = $query->result();

         $this->baskets = array();

         foreach ($tmp as $row) {
            $this->baskets[] = explode(",", $row->tf_cart);
         }
      }

      return $this->moreBaskets() ? $this->baskets[$this->numBasket++
       % BUCKET_SIZE] : null;*/
   }

   public function moreBaskets() {
      if ($this->basketCount == -1) {
         $sql = "SELECT COUNT(*) as cnt FROM ( " . BASKET_NUMBER . " ) a";//BASKET_QUERY . " ) a";
         $query = $this->db->query($sql);//, array(0, BUCKET_SIZE));
         $res = $query->result();
         
         if (count($res) != 0 && isset($res[0]->cnt))
            $this->basketCount = intval($res[0]->cnt);
         else return false;
      }

      return $this->numBasket < $this->basketCount;
   }

   public function initBaskets() { 
      $this->numBasket = 0;
      $this->offset = -BUCKET_SIZE;
   }

   public function getItems() { return $this->items; }
   public function getItemNames() { return $this->itemNames; }

   public function getBasketCount() { 
      $this->moreBaskets();
      return $this->basketCount; 
   }

   public function __construct($db) {
      $it = $db->query(<<<EOT
select min(matchid) as mid, transfac
   from factor_matches group by transfac
   order by mid asc
EOT
      );

      if (count($res = $it->result()) > 0)
         foreach ($res as $item) {
            $this->items[] = $item->mid;
            $this->itemNames[] = $item->transfac;
         }
      else
         throw new Exception("No items found using specified Database connector.");

      $db->query(CLEAR_STAGE);

      /*$i = 0;
      $size = BUCKET_SIZE;

      while ($size == BUCKET_SIZE) {*/
         $res = $db->query(STAGING_QUERY);/*, array($i++ * BUCKET_SIZE, BUCKET_SIZE));
         $size = count($res);
      }*/

      $query = $db->query(BASKET_NUMBER);
      $this->basketNums = $query->result();

      $this->initBaskets();
      $this->basketCount = -1;
      $this->db = $db;
   }
}

?>
