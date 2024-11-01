<?php
/**
 * Plugin Name: Stratus5 Cloud Deployment
 * Plugin URI: https://wordpress.org/plugins/stratus5-cloudware/
 * Description: Add 'sell' buttons to your website (using shortcodes) to sell software apps, themes and plugins from your site, as a hosted service (SaaS).
 * Version: 1.2.12
 * Author: Stratus5
 * Author URI: http://stratus5.com
 * License: GPL2
 */
session_start();
	
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// WP Shortcode to display the signup form on any page or post.
function s5form($atts, $content){
  
  //session_start();
  
  $tldid = 115;
  $showbutton = 1;
  
  $adddelay = 0;  //40 seconds
  
  $buttontext = "Host Me Now!";
  if(!empty($content))
    $buttontext = $content;
  
  
  extract( shortcode_atts( array(
    'domain' => '0',
    'plancode' => '0',
    'class' => 's5-button',
    's5username' => '',
    's5bg' => '',
    's5password' => '',
    'login' => '',
    'requirecreditcard' => '',
    'target' => '',
    'themeurls' => '',
    'pluginurls' => ''
  ), $atts, 's5-cloud' ) );
  
  /*  We need the following attributes 
   *   apiDomain : the base url for the api: set by the domain (or domainname) attributes
   *   customerDomain : the target domain of the user 
   *   orgName: orgname set by user,
   * 
   */
  
  $domaintocheck = esc_attr( get_option('domainname') );
  
  if(!empty($domain))
    $apiDomain = strtolower($domain);
  else if (!empty($domaintocheck))
    $apiDomain = esc_attr( get_option('domainname') );
  else
    $showbutton = 0;
  
  $planCode = $plancode;
  $themeUrls = $themeurls; 
  $pluginUrls = $pluginurls;
  $requireCreditCard = $requirecreditcard;
  $customerDomain = strtolower($target); 
  if (empty($target)) {
  	$customerDomain= strtolower($apiDomain);
  }
  
  if(!empty($s5username))
    $_SESSION['baseUsername'] = $s5username;
  else
    $_SESSION['baseUsername'] = esc_attr( get_option('username') );
  
  if(!empty($s5password))
    $_SESSION['basePassword'] = $s5password;
  else
    $_SESSION['basePassword'] = esc_attr( get_option('password') );
  
  $_SESSION['adminlogin'] = $login;
  $_SESSION['pluginUri'] = plugin_dir_url( __FILE__ );

  if(!empty($s5password))
  	$_SESSION['basePassword'] = $s5password;
  
  if(!empty($s5bg))
    $bgurl = $s5bg;
  else
    $bgurl = esc_attr( get_option('bgurl', plugin_dir_url( __FILE__ ).'images/default.png') );
  
  if($bgurl == "")
    $bgurl = plugin_dir_url( __FILE__ ).'images/default.png';
  
  wp_enqueue_script( 's5form', plugins_url( 'js/s5form.js', __FILE__ ), array(), false, false );

  
  wp_localize_script('s5form', 's5form', array(
  'pluginsUrl' => plugin_dir_url( __FILE__ ),
  ));
  
  ?>
    
  <!-- s5-cloud version Version: 1.2.9 -->
  <div id="cover" />
  <form id="applicationForm" class="s5Formloader" onSubmit="return false;" method="post" name="Application">
    <div class="s5FormCloseWrap">
      <span id="s5FormClose"><img src="<?php echo plugin_dir_url( __FILE__ );  ?>images/close.png" width="20"></span>
    </div>
    
    <div class="s5FormHeader" >Sign Up Now!</div>
    
    <div class="s5domaincheckerloaderWrapper">
	    <span id="domaincheckerloader"><img class="s5ImgLoader" src="<?php echo plugin_dir_url( __FILE__ );  ?>images/ajax-loader.gif"></span>
	    <input id="organization-form" autocomplete="off" name="organization-form" type="text" class="s5textfield" placeholder="Company Name"/><br>
    <!-- <label style="font-size: 8pt;"> (no spaces or special characters)</label><br> -->
    </div>
    <div id="errormsg" class="s5Error s5ErrorMsg" >
      Error: This company name already exists in our system. Please try a different one.
    </div>
    <div id="orgEmptyError" class="s5Error s5ErrorMsg" >
      Error: You must set a company name.
    </div>
    <div class="s5firstname">
      <input id="firstname" name="firstname" required="" type="text" class="s5textfield" placeholder="First Name"/>
    </div>
    <div class="s5lastname"> 
      <input id="lastname" name="lastname" required="" type="text" class="s5textfield" placeholder="Last Name"/>
    </div>
    <div class="s5emailcheckerloader">
    <span id="s5emailcheckerloader"></span>
    <input id="email" placeholder="Email" autocomplete="off" name="email" required="" size="40" class="s5textfield"/><br>
    <!-- <label style="font-size: 8pt;">(ensure you enter a valid address)</label><br> -->
    </div>
    
    <div id="errormsgMail" class="s5Error">
      Error: This email is already in use. Please use another.
    </div>

    <input id="passwd" placeholder="Password" autocomplete="off" title="min. 8 characters with 1 uppercase and 1 digit required" name="password" required="" type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="s5textfield" /><br>
    <label class="s5hint">(min. 8 characters with 1 uppercase and 1 digit required)</label>
    
		
    <?php if($requireCreditCard=="y") { ?>
    <div class="s5ForCredidCardHeader">Credit Card Details</div>
    
    <div class="s5emailcheckerloader">
    	<input id="nameoncard" autocomplete="off" name="nameoncard" required="" type="text" class="s5textfield" placeholder="Name On Card"/><br>
    </div>
    <div class="s5cardnumber">
      <input id="cardnumber" name="cardnumber" required="" type="text" class="s5textfield" placeholder="Card Number"/>
    </div>
    <div class="s5creditexp">
      <input id="expmonth" name="expmonth" required="" type="text" class="s5textfield" placeholder="MM"/>
    </div>
    <div class="s5creditexp">
      <input id="expyear" name="expyear" required="" type="text" class="s5textfield" placeholder="YYYY"/>
    </div>
    <div class="s5creditcvn">
      <input id="cvn" name="cvn" required="" type="text" class="s5textfield" placeholder="CVN"/>
    </div>
    <div id="errormsgCreditCard" class="s5Error">
      Error: The credit card details are invalid.
    </div>
    <label class="s5hint">You will not be billed during your trial.</label>
    
    <?php } else { ?>
    	<input id="nameoncard" name="nameoncard" type="hidden" />
    	<input id="cardnumber" name="cardnumber" type="hidden" />
    	<input id="expmonth" name="expmonth" type="hidden" />
    	<input id="expyear" name="expyear" type="hidden" />
    	<input id="cvn" name="cvn" type="hidden" />
    <?php }  ?>
    
    <input id="tldid" name="tldid" type="hidden" value="<?php echo $tldid; ?>" />
    <input id="planCode" name="planCode" type="hidden" value="<?php echo $planCode; ?>" />
    <input id="themeUrls" name="themeUrls" type="hidden" value="<?php echo $themeUrls; ?>" />
    <input id="pluginUrls" name="pluginUrls" type="hidden" value="<?php echo $pluginUrls; ?>" />
    <input id="requireCreditCard" name="requireCreditCard" type="hidden" value="<?php echo $requireCreditCard; ?>" />
    <input id="domain" name="domain" type="hidden" value="<?php echo $customerDomain; ?>" />
    <input id="baseUrl" name="baseUrl" type="hidden" value="<?php echo $apiDomain; ?>" />
    <input id="sldAndSubdomain" name="sldAndSubdomain" type="hidden" value="" />
    <input id="organization" name="organization" type="hidden" value="" />
    <input id="adminlogin" name="adminlogin" type="hidden" value="<?php echo $login; ?>" />
    <input id="bgimage" name="bgimage" type="hidden" value="<?php echo $bgurl; ?>" />
    <input id="token" name="token" type="hidden" value="u7y3cohysyiqDT3y9t1hVp32szhIsXlXdW7HTFh1" />
    <div class="s5FormButtonSubmit">
      <input id="buttonSubmit" onClick="submitForm()" type="submit" value="<?php echo $buttontext; ?>" class="<?php echo $class; ?>"/>
    </div>
  </form>
  
  <div id="s5mktloader" class="s5mktloader">
    <div class="s5mktloaderbold"><label>Please wait while we install your hosted website.</label></div>
    <div class="s5mktloaderbold"><label>This typically takes less than 60 seconds.</label></div>
    <div class="s5mktloaderbold"><img src="<?php echo plugin_dir_url( __FILE__ );  ?>images/loading.gif" alt="" /></div>
    <div class="s5mktloadercnterhead">You will be emailed your website login information shortly.</div>
    <div class="s5mktloadercnterhead">
      <b>Some of our key features and benefits:</b><br>
      
					- 1-click install with your own dedicated WordPress instance<br>
					- No plugin or theme restrictions<br>
					- Unlimited bandwidth<br>
					- 10 GB free storage<br>
					- Free automated daily backups<br>
					- Automated upgrades<br>
					- Fully managed service with 24x7 support<br>
		</div>  
  </div>
  
  <div id="s5-loading-gif" style="display:none;">
  	<img src="<?php echo plugin_dir_url( __FILE__ );  ?>images/loading1.gif" style="position:block; margin-left: auto; margin-right: auto; width:70px;" alt="" /></div>
  </div>
  
	<div id="linksdialog">
	  <div id="linksloader">
          <h2>Waiting for DNS to setup!</h2>
          <img src="../images/loading.gif"><br><br>
          <div id="progressbar"></div>
        </div>
        
        <div id="linksshowmessage">
          <h2>We apologise for the inconvenience</h2>
          Due to heavy workload DNS setup might take a while!<br><br>
          You will be informed with an email when your instance is ready for use!!<br><br>
          <strong>DO NOT WORRY</strong> since it will not take more than 10 minutes!        
        </div>
        
        <div id="showlinks" style="display: block;">
          <div class="s5formLine">
            <strong>Thank you! Your website is now ready to use.</strong>
          </div>
          <div class="s5formLine">
            <strong>Your new, live Website:</strong>
          </div>
  
          <div class="s5formLine">
            <strong>></strong> Click <a id="s5apploginlink" href="https://<?php echo $_SESSION['applogin']; ?>" target="newWindow" ><b>here</b></a> to go to your new, live website<br>
          </div>

          <div class="s5formLine">
            We have also emailed you your website login credentials.
          </div>
          
          <div id="s5wordpressadminpannel" >
	          <div class="s5formLine">
	            <strong>WordPress Admin Panel:</strong>
	          </div>
	
	          <div class="s5formLine">
	          	<strong>></strong> Click Click <a href="#" onclick="submitFormApp();"><b>here</b></a> to go to your WordPress admin panel
          	</div>
          </div>
          
          <div class="s5formLine">
          	<strong>Account Details:</strong>
          </div>

          <div class="s5formLine">
          	<strong>></strong> Click <a href="#" onclick="submitFormAdmin();"><b>here</b></a> to view your account details.<br>
          </div>

          <div class="s5formLine">
          	<strong>Note: </strong>If your new website does not appear immediately please wait a few minutes for your local network (i.e. DNS) to recognize the new website.<br>
          </div>
          
        </div>
        
    
    <form id="auto-login-form-admin" method="post" action="<?php echo $_SESSION['post_url']; ?>/login/authenticate">
      <input id="auto-login-form-admin-j_username" type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>" />
      <input id="auto-login-form-admin-j_password" type="hidden" name="password" value="<?php echo $_SESSION['password']; ?>" />
    </form>
    <form id="auto-login-form-app" method="post" target="_blank" action="https://<?php echo $_SESSION['applogin'] ?>/wp-autologin.php">
      <input type="hidden" id="username" name="username" value="admin" />
      <input type="hidden" id="password" name="password" value="<?php echo $_SESSION['password']; ?>" />
      <input type="hidden" id="redirect_to" name="redirect_to" value="https://<?php echo $_SESSION['applogin']; ?>/wp-admin/" />
      <input type="hidden" name="testcookie" value="1" />
    </form>

	</div>
  
  
  <?php
  
  if($showbutton) 
    return "<button id=\"launchapp\" class=\"".$class."\">".$buttontext."</button>";
  else 
    return "<div class=\"s5Error\">Domain Name is not set properly.</div>";

	?>
	
	
	
  <?php
  
}



function wptuts_scripts_important()
{
  wp_enqueue_script('jquery');
  wp_register_style( 's5Style', plugins_url( 'css/s5-cloud.css', __FILE__ ), array(), '20120208', 'all' );
  wp_enqueue_style( 's5Style' );
}

add_action('admin_menu', 'stratus5_admin');

add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
  wp_register_style( 'admin_css', plugins_url( 'css/s5-cloud.css', __FILE__ ), false, '1.0.0' );
  wp_enqueue_style( 'admin_css');
  wp_enqueue_script('jquery');
  wp_enqueue_script( 's5admin', plugins_url( 'js/s5admin.js', __FILE__ ), array(), false, false );
  wp_localize_script('s5admin', 's5admin', array(
  'pluginsUrl' => plugin_dir_url( __FILE__ ),
  ));
  
  /*
  wp_enqueue_script( 's5autologin', plugins_url( 'js/s5autologin.js', __FILE__ ), array(), false, false );
  wp_localize_script('s5autologin', 's5autologin', array(
  'applogin' => $_SESSION['applogin'],
  ));
  */
}

function stratus5_admin(){
  add_menu_page( 'Stratus5 Integration', 'Stratus5 Intergration', 'administrator', 's5-cloud', 's5admin_init', 'dashicons-cloud' );
}
 
function s5admin_init(){

  ?>
  <div class="wrap">
    
    
    <h2>Stratus5 Integration Plugin</h2>
    
    <div class="adminwrapper">
      
      <div class="welcome-panel">
        
        <a href="https://stratus5.com" target="_blank">
          <img src="https://stratus5.com/wp-content/uploads/2014/12/logo1.png" height="45">
        </a>
        
        <div class="s5plugin-descr">Plugin Description</div>
        <p>This plugin adds a button through a shortcode that opens up a<br>
        popup form for signing up to deploy a theme in the stratus5 cloud.
        <br>
        Sign up <button class="button button-primary s5PartnerPopup" value="here">here</button> and start using our cloudware to enhance your theme sales.</p>
      </div>
     
      <div class="welcome-panel">
      <h3>API Settings</h3>
      
      <?php    
        $showConnectButton = 0;
        $params = array(
          "username" => esc_attr( get_option('username') ),
          "password" => esc_attr( get_option('password') ),
          "remember-me" => "on"
        );

        $url = "https://".esc_attr( get_option('domainname') )."/login/authenticate";
        $response = (array) $response;
        $response = (array) wp_remote_post( $url,
          array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => $params,
            'cookies' => array()
          )
        );
				
        $responsetocheck = $response['cookies'][1];
				// echo "<label>COOKIES=".serialize($responsetocheck)."</label>";
        if(!empty($responsetocheck)) {
          echo "<div class= \"api-settings-ok\" \">API Settings are Correct</div>";
          $showConnectButton = 1;
        } else {
          echo "<div class= \"api-settings-error\" \">Your API settings are not set properly!</div>";
        }  
      ?> 
      
      <?php
    
      if($_GET['settings-updated'])
      {
      ?>
        <div class= "api-settings-saved" >Your settings have been saved</div><br>
      <?php
      }
      ?>

      <?php include 'forms/s5-admin-api-settings.php'; ?>
      
      <div>
        <p>Username and password are mandatory fields. If you do not<br>
        define a domain here then you have to do it through the<br>
        shortcode attribute "domain".</p>
        <p>To get your credentials click <button class="button button-primary s5PartnerPopup" value="here">here</button> and signup.</p>
        <p>
          Paste your desired background image in the 'Background URL' field. This is the image that appears as a background in the last screen after your customer's application is deployed.
        </p>
      
      </div>
      
      </div>
      
<div class="s5partnerSignupForm"> 
  
  <div class="s5partnerSignupFormClose" >
    <span id="s5PartnerFormClose"><img src="<?php echo plugin_dir_url( __FILE__ );  ?>images/close.png" class="s5partnerSignupCursor" "></span>
  </div>
  
  <h2 class="s5partnerSignupFormHeader" >Partner Signup</h2>
  <form action="https://stratus5.net/signUp/addPartner" method="POST" target="_blank">
  <p>
    <strong class="s5partnerSignupFormNameHeader">Your Name (required)</strong><br>
    <input class="s5partnerSignupFormNameText"  type="text" name="primaryContact.firstName" size="50" required="" id="primaryContact.firstname" placeholder="First Name" />
    <input class="s5partnerSignupFormNameText" type="text" name="primaryContact.lastName"  size="50" required="" id="primaryContact.lastname" placeholder="Last Name"/>
  </p>
  
  <div>
    <strong class="s5partnerSignupFormNameHeader">Choose a temporary domain for your application: (required)</strong>
  </div>
  
  <div>
    <div class="s5partnerSignupFormHttps"><strong>https://</strong> </div>
    <div class="s5partnerSignupFormHttpsWrap" >
      <span id="s5partnercheckerloader" ><img src="<?php echo plugin_dir_url( __FILE__ );  ?>images/ajax-loader.gif" class="s5spinner"></span>
      <input class="s5partnerSignupSubDomain" type="text" name="domain" required="" id="domain" placeholder="subdomain" />
    </div>
    <div class="s5partnerSignupDomain" >
      <strong>.stratus5.net*</strong>
    </div>
    
    <div id="partnererrormsg" class="s5PartnerSignupError" >
      Error: This company name already exists in our system. Please try a different one.
    </div>
    
    
  </div>
  <p class="update-nag s5-update-nag" >
  *This can be changed at any time after registration...even to your own domain name.
  </p>
  
  <p>
    <div class="s5partnerSignupFormInner">
	    <strong class="s5partnerSignupFormEmail" >Your Email Address (required)</strong><br>
	    <span id="s5emailcheckerloader"><img src="<?php echo plugin_dir_url( __FILE__ );  ?>images/ajax-loader.gif" class="s5spinner"></span>
	    <input id="s5email" type="email" name="primaryContact.username" class="s5partnerSignupFormUsername" required="" size="50" id="primaryContact.lastname" placeholder="your.name@email.com" /> 
    </div>
    <div id="partnererrormsgMail" >
      Error: This email is already in use! Please try with another!
    </div>  
  </p>
  
  <p class="update-nag s5-update-nag">
    Verification of your email will be required before using Stratus5.
  </p>
  
  
  <div class="s5partnerSignupFormHidden">
    <input name="orgName" id="orgName" type="hidden" value="" />
    <input name="publicid" id="publicid" type="hidden"  value="54c1c797ec95cac1f65ce38fd428dca2" />
    <input name="leadsource" id="leadsource" type="hidden" value="Partner Registration" />
    <input name="recaptcha_challenge_field" id="recaptcha_challenge_field" type="hidden" value="" />
    <input name="recaptcha_response_field" id="recaptcha_response_field" type="hidden" value="" />
    <input name="submit" id="submit" type="hidden" value="Create My Free Account" />
  </div>
  
  <p class="s5partnerSignupFormButton">
    <input type="submit" class="button button-primary" value="Create My Free Account" />
    <br><strong> No Credit Card Required</strong>
  </p>
  </form>      
</div>        

      
  </div>
  <div class="s5-welcome-panel welcome-panel" >
    <h2>How to use the shortcode</h2>
    <p>Place this shortcode in any post/page/product description to get the registration popup.<br>
    Use your own 'domain' and 'planCode'</p>
    <p>If you haven't defined a domain name in the settings on the left then use:</p>
    <code>
      [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan"]
    </code>
    <p>If you have defined a domain name in the settings on the left then use:</p>
    <code>
      [s5-cloud planCode="IXVCDplan"]
    </code>
    <p><strong>ATTENTION:</strong> If you have defined a domain in both the<br>
    settings and the shortcode then our plugin will use the<br>
    shortcode's domain.</p>
    <p>If you want to change the button text (default button text is 'Host Me Now!') then use:</p>
    <code>
      [s5-cloud planCode="IXVCDplan"] Button Text [/s5-cloud]
    </code>
    or<br><br>
    <code>
      [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan"]Button Text[/s5-cloud]
    </code>
    <p>
      To learn more about creating a price plan read this
      <a href="https://stratus5.com/documentation/creating-a-plan-for-your-application/" target="_blank">
      entry</a> in our <a href="https://stratus5.com/documentation/" target="_blank">Dev Center</a>
    </p>
    
    <p><p>
      If you want to add a link for your customers to connect to the admin panel of your<br>
      application then use the shortcode attribute 'login'. When set it will create an additional<br>
      link in the final screen after the application deployment.
    </p>
    <p>  
      <code>
      [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan" login="wp-admin"]Button Text[/s5-cloud]
    </code>
    </p>
    <p>This shortcode will create the following output: testdomain.stratus5.net/wp-admin</p>
    
    <p>
    If you would like themes or plugins installed with your application, then you should use the the following shortcode attributes:<br>
    themeUrls: A comma separated list of publicly accessible urls to themes that you would like installed with your application
		<br>
		pluginUrls: A comma separated list of publicly accessible urls to plugins that you would like installed with your application<br>
    <p>  
      <code>
      [s5-cloud domain="testdomain.stratus5.net" themeUrls="http://mytheme.com/themes/theme1.zip" pluginUrls="http://mytheme.com/themes/plugin1.zip,http://mytheme.com/themes/plugin2.zip"]Button Text[/s5-cloud]
    </code>
    </p>

    If you want to add credit card details for example if the plan has 0 trial days, you should add requireCreditCard="y" 
    <p>  
      <code>
      [s5-cloud domain="testdomain.stratus5.net" requireCreditCard="y"]Button Text[/s5-cloud]
    </code>
    </p>

    <p>  
    If you want the target domain to be different from the domain defined above you need to add a target attribute  
      <code>
      [s5-cloud domain="accounts.mydomain.com" target="mydomain.com"]Button Text[/s5-cloud]
    </code>
    </p>
    
    
    <h3>How to use custom Classes</h3>
    <p>
      You can use your existing CSS classes through the shortcode attribute 'class':<br><br>
      <code>
      [s5-cloud planCode="IXVCDplan" class="myclass"]
      </code><br>
    </p>
    <h3>Shortcodes for individual settings</h3>
    <p>
      The following attributes can be used for individual setup.<br>
      <strong>s5username, s5password, s5bg</strong>.
    </p>
    <h3>Full Example with all attributes</h3>
    <p>
       [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan" requireCreditCard="y" s5username="myemail@mail.com" s5password="abcd1234A" s5bg="https://stratus5.com/images/bg.jpg" login="wp-admin" themeUrls="http://mytheme.com/themes/theme1.zip" pluginUrls="http://mytheme.com/themes/plugin1.zip,http://mytheme.com/themes/plugin2.zip"]Button Text[/s5-cloud] 
    </p>
      <?php if($showConnectButton) { ?>
    <div class="welcome-panel">
      <h3>Cloud Platform Admin Panel</h3>
       <?php include 'forms/s5-connect-platform.php'; ?>
    </div>
    <?php } ?>
    </div>
    
    <div class="welcome-panel log-session">
      <h3>Log/Debugging Section</h3>
      <br>
      <button id="showLogs" class="button button-primary">Show Logs</button> <button id="deleteLogs" class="button button-danger s5-button-danger" >Delete Logs</button>
      <div id="logsContainer"><pre></pre></div>
    </div>
    
  </div>
  <?php
}

add_action( 'admin_init', 's5_plugin_settings' );

function s5_plugin_settings() {
  register_setting( 's5-plugin-settings-group', 'username' );
  register_setting( 's5-plugin-settings-group', 'password' );
  register_setting( 's5-plugin-settings-group', 'domainname' );
  register_setting( 's5-plugin-settings-group', 'bgurl' );
}

add_action( 'wp_enqueue_scripts', 'wptuts_scripts_important', 5 );

add_shortcode('s5-cloud', 's5form');
