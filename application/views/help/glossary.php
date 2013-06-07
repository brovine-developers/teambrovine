<h1>Glossary</h1>

<ul class="glossary">
   <? foreach ($defs as $term => $def) : ?>
      <li><a href="#<?= $term ?>"><div class="term"><?= $term ?>:</a></div>
         <div class="def"><?= $def ?></div>
      </li>
   <? endforeach; ?>
</ul>
