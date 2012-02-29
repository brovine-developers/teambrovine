<div class="navbar navbar-fixed-top">
   <div class="navbar-inner">
      <div class="container">
         <span class="brand">Brovine</span>
         <ul class="nav">
            <? foreach ($tabs as $url => $tab) : ?>
               <li <?if ($tab == $activeTab) :?>class="active"<? endif; ?>>
                  <a href="<?=$url?>"><?=$tab?></a>
               </li>
            <? endforeach; ?>
         </ul>
      </div>
   </div>
</div>
