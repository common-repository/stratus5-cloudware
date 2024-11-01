  === Plugin Name ===
  Contributors: patbolger
  Donate link: 
  Tags: Stratus5, Cloudware, Containers, Docker, Docker as a Service, SaaS, Software as a Service, Cloud Apps, Cloud Deployment, WPDocker, Plugin as a Service, Theme as a Service
  Requires at least: 3.0.1
  Tested up to: 4.9.5
  Stable tag: 1.2.12
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  
  Add 'sell' buttons to your website (using shortcodes) to sell software apps, themes and plugins from your site, as a hosted service (SaaS).
  == Description ==

  This plugin enables WordPress users to add 'sell' buttons to your website (using a shortcode) to sell software applications, themes and plugins from 
  your website as a hosted service (SaaS).
  
  After you install the plugin (see Installation section) you place this shortcode in any post/page/product description 
  to get a registration popup that allows your end customer to purchase your software. You should use your own 'domain' and     'planCode' in the plugin configuration.
  
  If you haven't defined a domain name in the main plugin settings then use:
  
  [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan"]
  If you have defined a domain name in the main plugin settings then use:
  
  [s5-cloud planCode="IXVCDplan"]
  ATTENTION: If you have defined a domain in both the settings and the shortcode then our plugin will 
  use the shortcode's domain.
  
  Full Example with all attributes
  [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan" requireCreditCard="y" s5username="myemail@mail.com" s5password="abcd1234A" s5bg="https://stratus5.com/images/bg.jpg" login="wp-admin" themeUrls="http://mytheme.com/themes/theme1.zip" pluginUrls="http://mytheme.com/themes/plugin1.zip,http://mytheme.com/themes/plugin2.zip"]Button Text[/s5-cloud]
  
  For more details, see the FAQ section.
  
  == Installation ==
  
  At your wp-admin page, click on Plugins | Add new and then Upload file.
  Click on the Choose file button and upload the s5-cloud-plugin.zip
  After uploading the plugin you press 'Activate' to enable it.
  
  A Stratus5 Cloudware menu link will be added in your wp-admin menu. Once installed you will need to create an account on
  the Stratus5 platform to retrieve your credentials to configure your API Settings.
  
  
  == Frequently Asked Questions ==
  
  = How do I change the button text? =
  If you want to change the button text (default button text is 'Host Me Now!') then use:
  
  [s5-cloud planCode="IXVCDplan"] Button Text [/s5-cloud] or
  
  [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan"]Button Text[/s5-cloud]
  
  To learn more about creating a price plan refer to Stratus5 Dev Center at https://stratus5.com/documentation/
  
  = How do I add a link to admin panel? =
  
  If you want to add a link for your customers to connect to the admin panel of your
  application then use the shortcode attribute 'login'. When set it will create an additional
  link in the final screen after the application deployment.
  
  [s5-cloud domain="testdomain.stratus5.net" planCode="IXVCDplan" login="wp-admin"]Button Text[/s5-cloud]
  
  This shortcode will create the following output: testdomain.stratus5.net/wp-admin
  
  = Can I define themes or plugins installed with my application? =
  
  If you would like themes or plugins installed with your application, then you should use the the following shortcode attributes:
  themeUrls: A comma separated list of publicly accessible urls to themes that you would like installed with your application 
  pluginUrls: A comma separated list of publicly accessible urls to plugins that you would like installed with your application
  [s5-cloud domain="testdomain.stratus5.net" themeUrls="http://mytheme.com/themes/theme1.zip" pluginUrls="http://mytheme.com/themes/plugin1.zip,http://mytheme.com/themes/plugin2.zip"]Button Text[/s5-cloud]
  
  = What if I want credit card details in the registration screen? =
  
  If you want to add credit card details for example if the plan has 0 trial days, you should add requireCreditCard="y"
  [s5-cloud domain="testdomain.stratus5.net" requireCreditCard="y"]Button Text[/s5-cloud]
  
  == What if my customer's domain will be different from the api domain? 

   If you want the target domain to be different from the domain defined above you need to add a target attribute  
   [s5-cloud domain="accounts.mydomain.com" target="mydomain.com"]Button Text[/s5-cloud]
  
  
  == How to use custom CSS classes? =
  
  You can use your existing CSS classes through the shortcode attribute 'class':
  
  [s5-cloud planCode="IXVCDplan" class="myclass"] 
  Shortcodes for individual settings
  The following attributes can be used for individual setup.
  s5username, s5password, s5bg.
  
  == Screenshots ==
  
  To be provided
  
  == Changelog ==
  = 1.2.12
   Updates to support stratus5 v 3

  = 1.2.9
   domains are always lower case
   check for empty company name  

  = 1.2.8
   added target domain 

  = 1.2.7
   spinning arrow while waiting to redirect

  = 1.2.5
   Redirects to user admin page after signup
	
  = 1.1.26 =
  tested for Wordpress 4.4

  = 1.1.25 =
  css fix

  = 1.1.24 =
  Initial version
  
  
