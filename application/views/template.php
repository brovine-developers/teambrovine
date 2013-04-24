<!DOCTYPE html>
<html>
<head>
   <title>Team Brovine -- Experiment Browser</title>
   <?= $_scripts ?>
   <?= $_styles ?>
</head>
<body>
   <div id="header">
      <?=$header?>
   </div>

   <?php if ($user !== false): ?>
   <div id="navigation" class="bs-docs-sidebar">
      <ul class="nav nav-list bs-docs-sidenav affix">
         <? foreach ($tabs as $url => $tab) : ?>
            <li <?if ($tab == $activeTab) :?>class="active"<? endif; ?>>
               <a href="<?=$url?>"><?=$tab?></a>
            </li>
         <? endforeach; ?>
      </ul>
   </div>
   <?php endif; ?>

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
