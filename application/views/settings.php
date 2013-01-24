<div class="row">
  <div class="span6 offset3">
     <div class="page-header">
       <h1>Settings</h1>
     </div>
  </div>
</div>

<div class="row">
  <div class="span6 offset3">
    <?php if (validation_errors() != ''): ?>
    <div class="alert alert-block alert-error">
      <h4 class="alert-heading">Try Again!</h4>
      <p><?php echo validation_errors(); ?></p>
    </div>
    <?php endif ?>
  </div>
</div>

<?php
   $disp = set_value('display_name');

   if (empty($disp)) {
      $disp = $user['display_name'];
   }
?>

<div class="row">
  <div class="span6 offset3"><div class="well">
      <?php echo form_open('auth/settings', array('class' => 'form-horizontal')); ?>
      <div class="control-group">
        <?php
          echo form_label('Current Password:', 'curpass', array('class' => 'control-label'));
          echo '<div class="controls">';
          echo form_password(array('name' => 'curpass', 'id' => 'curpass'));
          echo '</div>';
        ?>
      </div>

      <div class="control-group">
        <?php
          echo form_label('Password:', 'password', array('class' => 'control-label'));
          echo '<div class="controls">';
          echo form_password(array('name' => 'password', 'id' => 'password'));
          echo '</div>';
        ?>
      </div>

      <div class="control-group">
        <?php
          echo form_label('Again:', 'passconf', array('class' => 'control-label'));
          echo '<div class="controls">';
          echo form_password(array('name' => 'passconf', 'id' => 'passconf'));
          echo '</div>';
        ?>
      </div>

      <div class="control-group">
        <?php
          echo form_label('Display Name:', 'display_name', array('class' => 'control-label'));
          echo '<div class="controls">';
          echo form_input(array('name' => 'display_name', 'id' => 'display_name',
            'value' => $disp));
          echo '</div>';
        ?>
      </div>

      <div class="control-group"><div class="controls">
         <?php echo form_submit('submit', 'Save'); ?>
      </div></div>

      <?php echo form_close(); ?>
  </div></div>
</div>
