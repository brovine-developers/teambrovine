<!DOCTYPE html>
<html>
<head>
   <title>Team Brovine -- Experiment Browser</title>
   <?= $_scripts ?>
   <?= $_styles ?>
   <style type="text/css">
   </style>
</head>
<body>
   <div id="header">
      <?=$header?>
   </div>
   <div id="nav-bar" class="bs-docs-sidebar">
   <ul class="nav nav-list bs-docs-sidenav affix">
<? foreach ($tabs as $url => $tab) : ?>
               <li <?if ($tab == $activeTab) :?>class="active"<? endif; ?>>
                  <a href="<?=$url?>"><?=$tab?></a>
               </li>
            <? endforeach; ?>

   </ul>
</div>
   <div class="container">
      <div id="content" class="tab-content">
         <?=$content?>
      </div>
   </div>
   <div id="footer">
      <?=$footer?>
   </div>
</body>
</html>
