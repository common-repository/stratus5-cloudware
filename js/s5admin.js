      jQuery(document).ready(function () {
        jQuery('.s5PartnerPopup').on('click', function(){
          jQuery('.s5partnerSignupForm').show();
        });
        
        
        jQuery('#showLogs').on('click', function(){
          jQuery("#logsContainer > pre").load( s5admin.pluginsUrl + "scripts/log.log");
        });
        
        jQuery('#deleteLogs').on('click', function(){
          jQuery.ajax({
            url: s5admin.pluginsUrl + "scripts/emptylogs.php",
            dataType : "html",
            success: function(result){
              jQuery("#logsContainer > pre").load(s5admin.pluginsUrl + "scripts/log.log");
            },
            error: function(result){
              alert('Couldn\'t Delete Log File!!');
            }
          });  
        });
        
        jQuery('.s5partnerSignupForm').css("top", "50px");
        jQuery('.s5partnerSignupForm').css("left", Math.max(0, ((jQuery(window).width() - jQuery('.s5partnerSignupForm').outerWidth()) / 2) + 
                                                jQuery(window).scrollLeft()) + "px");   
        jQuery('#s5PartnerFormClose').on('click', function () {
          jQuery('.s5partnerSignupForm').hide();
        });
        
        jQuery('#domain').on('change', function () {
        jQuery('#s5partnercheckerloader').show();
        
        var org = jQuery('#domain').val();
        var res = org.replace(/\s+/g, '');
        
        var url = 'https://stratus5.net/signupApi/v2/checkDomainAvailability?domain=' + res.toLowerCase() + '.stratus5.net';
        var isavailable = false;
        
        jQuery.ajax({
          url: url,
          dataType : "json",
          success: function(result){
            if(result['result']['code'] == "available")
              isavailable = true;
            
            jQuery('#s5partnercheckerloader').hide();
        
            if (!isavailable) {
              jQuery('#partnererrormsg').show();
              jQuery('#domain').val('');
            }
            else
            {
              jQuery('#partnererrormsg').hide();
            }
          },
          error: function(result){
            alert('Error while checking domain availability!');
            jQuery('#s5partnercheckerloader').hide();
            jQuery('#partnererrormsg').show();
            jQuery('#domain').val('');
          }
        });
      });
        
      jQuery('#s5email').on('change', function () {

        var email = jQuery('#s5email').val();
        //jQuery('#s5emailcheckerloader').show();
        jQuery('#s5emailcheckerloader').hide();
        var url = 'https://stratus5.net/signupApi/v2/checkEmailAvailability?email=' + email;
        var isavailable = false;
        
        jQuery.ajax({
          url: url,
          dataType : "json",
          success: function(result){
            if(result['result']['code'] == "available")
              isavailable = true;
            
            jQuery('#s5emailcheckerloader').hide();
        
            if (!isavailable) {
              jQuery('#partnererrormsgMail').show();
              jQuery('#s5email').val('');
            }
            else
            {
              jQuery('#partnererrormsgMail').hide();
            }
          },
          error: function(result){
            alert('Error while checking email availability!');
            jQuery('#s5emailcheckerloader').hide();
            jQuery('#partnererrormsgMail').show();
            jQuery('#s5email').val('');
          }
        });
      });
      
      });  
