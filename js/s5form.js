  function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
  };
  
  function isValidPassword(password) {
    var pattern = new RegExp(/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/);
    return pattern.test(password);
  };
  
  /*
   * baseUrl is apiDomain, domain is customerDomain
   */
  
  var customerDomain = jQuery('#domain').val();
  var adminlogin = jQuery('#adminlogin').val();
  var baseUrl = jQuery('#baseUrl').val();
  
  function checkS5Domain()
  {
	  // jQuery('#domaincheckerloader').show();
	  jQuery('#domaincheckerloader').hide();

	  var org = jQuery('#organization-form').val();
	  var normOrg = org.replace(/\s+/g, '');
	  var normOrg = normOrg.toLowerCase(); 

	  jQuery('#organization').val(normOrg);
	  jQuery('#organization-form').val(normOrg);
	  jQuery('#sldAndSubdomain').val(normOrg);
	  var url = "";
	  if (customerDomain.indexOf("stratus5.net") >= 0 )
		  url = 'https://' + baseUrl + '/signupApi/v2/checkDomainAvailabilityJSONP?domain=' + normOrg.toLowerCase() + '-' + customerDomain.toLowerCase() + "&callback=?";
	  else
		  url = 'https://' + baseUrl + '/signupApi/v2/checkDomainAvailabilityJSONP?domain=' + normOrg.toLowerCase() + '.' + customerDomain.toLowerCase() + "&callback=?";

	  var isavailable = false;

	  jQuery.ajax({      
		  url: url,
		  dataType : "jsonp",
		  success: function(result){
			  if(result['result']['code'] == "available")
				  isavailable = true;

			  jQuery('#domaincheckerloader').hide();

			  if (!isavailable) {
				  jQuery('#errormsg').show();
				  jQuery('#organization-form').val('');
				  return false;
			  }
			  else
			  {
				  jQuery('#errormsg').hide();
				  jQuery('#orgEmptyError').hide();
				  return true;
			  }
		  },
		  error: function(result){
			  alert('Error while checking domain availability. Please try again!');
			  jQuery('#domaincheckerloader').hide();
			  jQuery('#errormsg').show();
			  jQuery('#organization-form').val('');
			  return false;
		  }
	  });
  }
  
  function checkS5Email()
  {
    var email = jQuery('#email').val();
      
    //jQuery('#emailcheckerloader').show();
    jQuery('#emailcheckerloader').hide();
    jQuery('.s5ImgLoader').hide();
    
    var url = 'https://' + baseUrl + '/signupApi/v2/checkEmailAvailabilityJSONP?email=' + email + "&callback=?";;
    var isavailable = false;
    jQuery.ajax({
      url: url,
      dataType : "jsonp",
      success: function(result){
        if(result['result']['code'] == "available")
          isavailable = true;
        
        jQuery('#emailcheckerloader').hide();
    
        if (!isavailable) {
          jQuery('#errormsgMail').html('Error: This email is already in use! Please try with another!');
          jQuery('#errormsgMail').show();
          jQuery('#email').val('');
          return false;
        }
        else
        {
          jQuery('#errormsgMail').hide();
          return true;
        }
      },
      error: function(result){
        alert('Error while checking email availability. Please try again!');
        jQuery('#errormsgMail').show();
        jQuery('#email').val('');
        jQuery('#emailcheckerloader').hide();
        return false;
      }
    });    
  }
  
  function submitForm() {
    var org = jQuery('#organization-form').val();
    var res = org.replace(/\s+/g, '');
    jQuery('#organization').val(res);
    jQuery('#sldAndSubdomain').val(res);
    var errors = 0;
    
    if (jQuery('#organization-form').val().length > 0) {
      checkS5Domain();
    }
    else {
        jQuery('#orgEmptyError').show();
    }    
    if (jQuery('#email').val().length > 0) 
      checkS5Email();
    else
    {
      jQuery('#errormsgMail').html('Error: The email is not valid. Try this format myemail@domain.com.');
      jQuery('#errormsgMail').show();
      jQuery('#email').val('');
      jQuery('#email').css('border', '2px solid red');
    }
      
    setTimeout(function() {
      if (jQuery('#organization-form').val().length < 1) {
            jQuery('#orgEmptyError').show();
            errors++;
      } else { 
          jQuery('#orgEmptyError').hide();
      }      
      if (jQuery('#firstname').val().length < 1) {
        jQuery('#firstname').css('border', '2px solid red');
        errors++;;
      } else 
          jQuery('#firstname').css('border', '1px solid #efefef');
      
      if (jQuery('#lastname').val().length < 1) {
        jQuery('#lastname').css('border', '2px solid red');
        errors++;
      } else
        jQuery('#lastname').css('border', '1px solid #efefef');
      if (!(isValidEmailAddress(jQuery('#email').val()))) {
        jQuery('#errormsgMail').html('Error: The email is not valid. Try this format myemail@domain.com.');
        jQuery('#errormsgMail').show();
        jQuery('#email').val('');
        jQuery('#email').css('border', '2px solid red');
        errors++;
      } else
        jQuery('#email').css('border', '1px solid #efefef');
      if (!(isValidPassword(jQuery('#passwd').val()))) {
        jQuery('#passwd').css('border', '2px solid red');
        jQuery('#passwd').focus();
        errors++;
      } else
        jQuery('#passwd').css('border', '1px solid #efefef');
      if (jQuery('#domain').val().length < 1) {
        jQuery('#domain').css('border', '2px solid red');
        errors++;
      }
      if (errors == 0) {
        jQuery('#applicationForm').hide();
        jQuery('#s5mktloader').hide();
    	jQuery('#s5-loading-gif').show();
        var css_o = {
                width: "100%",
                height: "100%",
                background: "black",
                position: "fixed",
                zIndex: 666999,
                top: 0,
                left: 0,
                opacity: 0.8
            };
        jQuery("<div id='dimming'></div>").css(css_o).appendTo("body");
        jQuery("#s5-loading-gif").appendTo("body");
    	
        jQuery.ajax({
          url: s5form.pluginsUrl + "scripts/ajax.php?call=signup",
          dataType : "HTML",
          method: "POST",
          data: { 
            firstname: jQuery('#firstname').val(),
            lastname: jQuery('#lastname').val(),
            email: jQuery('#email').val(),
            password: jQuery('#passwd').val(),
            tldid: jQuery('#tldid').val(),
            planCode: jQuery('#planCode').val(),
            themeUrls: jQuery('#themeUrls').val(),
            pluginUrls: jQuery('#pluginUrls').val(),
            domain: jQuery('#domain').val(),
            sldAndSubdomain: jQuery('#sldAndSubdomain').val(),
            organization: jQuery('#organization').val(),
            adminlogin: jQuery('#adminlogin').val(),
            baseUrl: jQuery('#baseUrl').val(),
            bgimage: jQuery('#bgimage').val(),
            token: jQuery('#token').val(),
            requireCreditCard: jQuery('#requireCreditCard').val(),
            nameoncard: jQuery('#nameoncard').val(),
            cardnumber: jQuery('#cardnumber').val(),
            expmonth: jQuery('#expmonth').val(),
            expyear: jQuery('#expyear').val(),
            cvn: jQuery('#cvn').val()
          },
          success: function(result){
            if (result == "SUCCESS") {
              setTimeout(function() {
            	  jQuery('#s5mktloader').hide();
                  jQuery('#applicationForm').hide();
                  jQuery('#linksdialog').hide();
                // window.location = s5form.pluginsUrl + "scripts/auto-login.php";
                  
                  var customerDomain = jQuery('#domain').val();
                  var organization = jQuery('#organization').val();
                  
                  if (customerDomain.indexOf('stratus5.net') > -1) {
                	  customerDomain = organization + "-" + customerDomain;
                  } else {
                	  customerDomain = organization + "." + customerDomain;
                  }
                  var clientUsername = jQuery('#email').val();
                  var clientPassword = jQuery('#passwd').val();
                  var applogin = customerDomain;
                  var adminlogin = jQuery('#adminlogin').val();
                  var baseurl = jQuery('#baseUrl').val();
                  // fixing the values on the autologin page

                  // application url
                  jQuery('#s5apploginlink').attr("href", "https://" + applogin);

                  //autologin to app
                  jQuery('#auto-login-form-admin').attr("action", "https://" + baseurl + "/login/authenticate");
                  jQuery('#auto-login-form-admin-j_username').val(clientUsername);
                  jQuery('#auto-login-form-admin-j_password').val(clientPassword);
                  
                  // autologin to wordpress admin
                  if (adminlogin!=null && adminlogin.length>0) {
                	  jQuery('#s5wordpressadminpannel').show();
                  } else {
                	  jQuery('#s5wordpressadminpannel').hide();
                  }
                  jQuery('#s5wordpressadminpannellink').attr("href", "https://" + applogin + "/" + adminlogin );
                  jQuery('#auto-login-form-app').attr("action", "https://" + applogin + "/wp-autologin.php");

                  jQuery('#password').val(clientPassword);
                  jQuery('#redirect_to').val("https://" + applogin + "/wp-admin/");
                  submitFormAdmin();
                  return true;
              }, 0 );
            } else {  // result != "SUCCESS"
              if (result=='cardNumber'  
                || result=='nameOnCard'
                || result=='expYear'
                || result=='expMonth'
                || result=='cvn'
                ) {
                  var field='';
                  if (result=='cardNumber') field = 'card number';
                  else if (result=='nameOnCard') field = 'name';
                  else if (result=='expYear') field = 'year';
                  else if (result=='expMonth') field = 'month';
                  else field=result;

                  jQuery('#s5mktloader').hide();
              	  jQuery('#s5-loading-gif').hide();
              	  jQuery('#dimming').hide();
                  jQuery('#applicationForm').show();
                  jQuery('#errormsgCreditCard').html('Error: The ' + field + ' is invalid');
                  jQuery('#errormsgCreditCard').show();
              } else if (result=='email') {  
                  jQuery('#s5mktloader').hide();
              	  jQuery('#s5-loading-gif').hide();
              	  jQuery('#dimming').hide();
                  jQuery('#applicationForm').show();
                  jQuery('#errormsgMail').html('This email already exists in our system. Please try another email.');
                  jQuery('#errormsgMail').show();
              }
              else {                
                alert(result);
                jQuery('#s5mktloader').hide();
           	  	jQuery('#s5-loading-gif').hide();
              	jQuery('#dimming').hide();
                jQuery('#applicationForm').show();
              }
            }          
          }, // end of success function
          error: function(result){
            alert('Unable to initialise Instance Deployment!');
            jQuery('#s5mktloader').hide();
       	  	jQuery('#s5-loading-gif').hide();
          	jQuery('#dimming').hide();
            jQuery('#applicationForm').show();
          }
        }); // ajax request
      } // if errors==0
    }, 2000);
  }
  
  jQuery(document).ready(function () {  
	jQuery('#linksdialog').hide();

	jQuery('#linksdialog').css("left", Math.max(0, ((jQuery(window).width() - jQuery('#linksdialog').outerWidth()) / 2) + 
			jQuery(window).scrollLeft()) + "px");
	
	jQuery('.s5Formloader').css("top", "110px");
    jQuery('.s5Formloader').css("left", Math.max(0, ((jQuery(window).width() - jQuery('.s5Formloader').outerWidth()) / 2) + 
                                                jQuery(window).scrollLeft()) + "px");
    jQuery('.s5mktloader').css("top", "110px");
    jQuery('.s5mktloader').css("left", Math.max(0, ((jQuery(window).width() - jQuery('.s5mktloader').outerWidth()) / 2) + 
                                                jQuery(window).scrollLeft()) + "px");
    
    jQuery('#s5-loading-gif').css("top", "110px");
    jQuery('#s5-loading-gif').css("left", Math.max(0, ((jQuery(window).width() - jQuery('.s5-loading-gif').outerWidth()) / 2) + 
                                                jQuery(window).scrollLeft()) + "px");
	jQuery('#s5-loading-gif').hide();

    
    jQuery('#organization-form').on('change', function () {
      if (jQuery('#organization-form').val().length > 0) 
      {
        checkS5Domain();
      }
      else
          jQuery('#orgEmptyError').show();
    });

    jQuery('#organization-form').on('focusout', function () {
        if (jQuery('#organization-form').val().length > 0) 
        {
          checkS5Domain();
        }
        else
            jQuery('#orgEmptyError').show();
      });
    
    
    jQuery('#email').on('change', function () {
      if (!(isValidEmailAddress(jQuery('#email').val())))
      {
        jQuery('#errormsgMail').html('Error: The email is not valid. Try this format myemail@domain.com.');
        jQuery('#errormsgMail').show();
        jQuery('#email').val('');
        jQuery('#email').css('border', '2px solid red');
      }
      else
      {
        jQuery('#email').css('border', '1px solid #efefef');
        jQuery('#errormsgMail').hide();
        checkS5Email();
      }
    });
    
    jQuery('#launchapp').on('click', function () {
       jQuery('#applicationForm').show();
    });
    
    jQuery('#s5FormClose').on('click', function () {
      jQuery('#applicationForm').hide();
    });

  });
   
  
  function submitFormAdmin(){
	  jQuery('#auto-login-form-admin').submit();
  }

  function submitFormApp(){
	  jQuery('#auto-login-form-app').submit();
  }


  function poll(times) 	{
	  if (times > 101) {
	    return false;
	  }
	  
	  setTimeout(function(){
	    var domain = "https://" + customerDomain.toLowerCase() + "/?param=" + times;
	    jQuery.ajax({
	      url: 'checkdomain.php',
	      method: 'POST',
	      data: {domain: domain},
	      cache: false,
	      success: function(data){
	        //alert(data);

	        if (times == 100) {
	        	jQuery('#linksshowmessage').show();
	        	jQuery('#linksloader').hide();
	        }
	        if (data == "YES") {
	          setTimeout(function(){
	        	  jQuery('#showlinks').show();
	        	  jQuery('#linksloader').hide();
	        	  var app = '@Session["applogin"]';
	        	  alert(app);
	        	  return true;
	          }, 1000);
	        }
	        if (data == "NO") {
	          poll(times+1);
	          jQuery('#progressbar').append('<div class="loaderpiece"></div>')
	        }
	        
	      },
	      error: function(data){},
	      dataType: "HTML"});
	  }, 1000);
	};

	  //poll(0);

