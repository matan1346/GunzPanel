var credit_title_open = false;
$(document).ready(function(){
    var update_title_open = false;
$("#UpdateTitle").click(function() {
	var prop;
	if(update_title_open)
	{
		prop = {
			left: '+=125'
		};

		$("#Update").animate(prop, 450, function() {
		});
	}
	else
	{
		prop = {
			left: '-=125'
		};

		$("#Update").animate(prop, 600, function() {
		});
	}

	update_title_open = !update_title_open;
});


$("#CreditTitle").click(function() {
	var prop;
	if(credit_title_open)
	{
		prop = {
			left: '+=125'
		};
		$("#Credit").animate(prop, 450, function() {
		  $(this).parents('#CreditsContainer').css('z-index','1');
		});
	}
	else
	{
		prop = {
			left: '-=125'
		};
        $(this).parents('#CreditsContainer').css('z-index','102');
		$("#Credit").animate(prop, 600, function() {
		});
	}

	credit_title_open = !credit_title_open;
});


    
    $('.Act-Read-Content').click(function(){
        /*$(this).parents('.LOG_UPDATE_ROW').find('.LogContent').slideDown();*/
        
        
        //do in differents divs, not in the same div, because we can hidig the table without hiding the message.'
        
        var prop = {
			left: '-=40%',
            opacity: '0'
		}, prop2 = {
			left: '-=74%',
            width: '+=50%',
            opacity: '1'
		}, getLogContentID = $(this).attr('target');
        

		$(this).parents(".table-logs").css({'position': 'absolute'}).animate(prop, 450);//, function() {
		  //$('#Home-Update-Logs-Data-Wrapper').show();
          //$('#'+getLogContentID).show();
          
        $('#Home-Update-Logs-Data-Wrapper').show(450, function(){
            //$('#'+getLogContentID).show(5000);
            $(this).animate(prop2, 450, function() {  
              //alert('#'+getLogContentID);
              //alert("test");
              $('#'+getLogContentID).show();
            });    
        });
          
        
          
          //$(this).css('opacity','0');
          /*$(this).parents('.LOG_UPDATE_ROW').find('.LogContent').show();
		});*/
        /*$(this).parents('.LOG_UPDATE_ROW').find('.LogContent').animate(prop2, 450, function() {
		  $(this).show();
        });*/
    //});
    

    
});

    $('.Act-Back-Logs').click(function(){
        /*$(this).parents('.LOG_UPDATE_ROW').find('.LogContent').slideDown();*/
        
        
        //do in differents divs, not in the same div, because we can hidig the table without hiding the message.'
        
        var prop = {
			left: '+=40%',
            opacity: '1'
		}, prop2 = {
			left: '+=74%',
            width: '-=50%',
            opacity: '0'
		}
        
        //alert("asdas");
        $('.LogData').hide(450);
        
        $('#Home-Update-Logs-Data-Wrapper').animate(prop2, 450, function(){
            //$(this).hide();
            //alert("sad");
            $(this).parents('#Home-Update-Logs-Container').children(".table-logs").css({'position': 'relative'}).animate(prop, 450);//, function() {    
        });
        
          
        
          
          //$(this).css('opacity','0');
          /*$(this).parents('.LOG_UPDATE_ROW').find('.LogContent').show();
		});*/
        /*$(this).parents('.LOG_UPDATE_ROW').find('.LogContent').animate(prop2, 450, function() {
		  $(this).show();
        });*/
    //});
    

    
    });
    
    
    $('#Panel-Lang-Holder').hover(function(){
        $(this).animate({
                top: "+=50px"
            }, 250);
        //$(this).mouseout();
        //$(this).parent().find('section a:first-child').click();
    },function(){
        $(this).animate({
                top: "-=50px"
            }, 250);
    });
    
    $('#Panel-Lang-List a').hover(function(){
        $(this).children('img').animate({
                marginTop: "+=10px"
            }, 250);
    },function(){
        $(this).children('img').animate({
                marginTop: "-=10px"
            }, 250);
    });

});