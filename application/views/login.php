<div class="row">
  <div class="span6 offset3">
     <div class="page-header">
       <h1>Log In <small class="bigspace">or 
         <a href="mailto:dpeterson@calpoly.edu?subject=Request%20for%20Brovine%20Account">
           request an account
         </a>
       </small></h1>
     </div>
  </div>
</div>

<div class="row">
  <div class="span6 offset3">
    <p class="lead">All users must log in before using the database.</p>

    <?php if (validation_errors() != ''): ?>
    <div class="alert alert-block alert-error">
      <h4 class="alert-heading">Try Again!</h4>
      <p><?php echo validation_errors(); ?></p>
    </div>
    <?php endif ?>
  </div>
</div>

<div class="row">
  <div class="span6 offset3"><div class="well">
      <?php echo form_open('/auth', array('class' => 'form-horizontal')); ?>
      <div class="control-group">
        <?php
          echo form_label('Username:', 'username', array('class' => 'control-label'));
          echo '<div class="controls">';
          echo form_input(array('name' => 'username', 'id' => 'username',
            'value' => set_value('username')));
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

      <div class="control-group"><div class="controls">
         <?php echo form_submit('submit', 'Log in'); ?>
      </div></div>

      <?php echo form_close(); ?>
  </div></div>
</div>
