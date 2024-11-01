<form method="post" action="options.php">
  <?php settings_fields( 's5-plugin-settings-group' ); ?>
  <?php do_settings_sections( 's5-plugin-settings-group' ); ?>
  <label for="username">Username*</label><br>
  <input type="text" name="username" value="<?php echo esc_attr( get_option('username') ); ?>" /><br>
  <label for="domainname">Domain Name</label><br>
  <input type="text" name="domainname" value="<?php echo esc_attr( get_option('domainname') ); ?>" /><br>
  <label for="password">Password*</label><br>
  <input type="password" name="password" value="<?php echo esc_attr( get_option('password') ); ?>" /><br>
  
  <label for="bgurl">Background URL</label><br>
  <input type="text" name="bgurl" value="<?php echo esc_attr( get_option('bgurl') ); ?>" /><br>
  
  
  <?php submit_button(); ?>
</form>