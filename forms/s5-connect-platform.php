<form id="auto-login-form-admin" method="post" target="_blank" action="<?php echo $url; ?>">
  <input type="hidden" name="username" value="<?php echo esc_attr( get_option('username') ); ?>" />
  <input type="hidden" name="password" value="<?php echo esc_attr( get_option('password') ); ?>" />
  <div style="inline-block;"><?php submit_button('Sign in'); ?></div>
</form>