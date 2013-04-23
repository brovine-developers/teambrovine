<div class="navbar navbar-fixed-top">
   <div class="navbar-inner">
      <div class="container">
         <span class="brand">Brovine</span>
         <?php if ($user !== false): ?>
         <ul class="nav">
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
         </ul>
         <?php endif ?>
      </div>
   </div>
</div>
