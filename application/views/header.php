<div class="navbar navbar-fixed-top">
   <div class="navbar-inner">
      <div class="container">
         <span class="brand">Brovine</span>

         <ul class="nav">
            <?php if ($user !== false): /* User logged in? */ ?>

            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <? echo $user['display_name'] ?>
                  <b class="caret"></b>
               </a>

               <ul class="dropdown-menu">
                  <li><a href="/auth/settings">Settings</a></li>
                  <li><a href="/auth/logout">Log out</a></li>
                  <?php if ($user['can_write']): ?>
                  <li class="divider"></li>
                  <li><a href="/Upload">Upload</a></li>
                  <?php endif; ?>
                  <? /* Not yet supported
                  <?php if ($user['can_admin']): ?>
                  <li class="divider"></li>
                  <li><a href="/auth/admin">Account List</a></li>
                  <?php endif; ?>
                  */ ?>
               </ul>
            </li>

            <li class="main-menu dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  Navigation <b class="caret"></b>
               </a>

               <ul class="dropdown-menu">
                  <? foreach ($tabs as $url => $tab) : ?>
                     <li <?= ($tab == $activeTab) ? 'class="active"' : '' ?>>
                        <a href="<?=$url?>"><?=$tab?></a>
                     </li>
                  <? endforeach; ?>
               </ul>
            </li>

         <?php else: /* User not logged in? */ ?>
            <li class="main-menu"><a href="/auth">Log In</a></li>
         <?php endif; /* User logged in? */ ?>

            <li class="main-menu"><a href="/help">Help</a></li>
         </ul>
      </div>
   </div>
</div>
