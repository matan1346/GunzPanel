
// Waits to document to load all the resource than starts the hashtag action of menu actions.
$(window).load(function(){
   OpenActionByAnchor(HashTag()); 
});

    function HashTag()
    {
        var hash_ = window.location.hash;
        if(hash_.length > 1)
            return hash_;
        return false;
            
    }
    
    
    function OpenResetSideMenuAction()
    {
        $('.Side-Menu-Action-Category-Text.Selected-Action-Category').click().parent().children('.Side-Menu-Action-List').hide();//.removeClass('Selected-Action-Category');
        $('#Side-Menu-Button').click();
            
    }
    
    
    function SelectSpecificCategory(cat)
    {
        $('.Side-Menu-Action-Category[category-name='+cat+'] > .Side-Menu-Action-Category-Text:not(.Selected-Action-Category)').click();
    }
    
    function SelectSpecificAction(cat, act)
    {
        SelectSpecificCategory(cat);
        
        $('.Side-Menu-Action-Category[category-name='+cat+'] .Side-Menu-Action-List .Side-Menu-Action-Item[action-name='+act+'] > .Side-Menu-Action-Item-Text:not(.Selected-Action-Category)').click();
    }
    
    function OpenActionByAnchor(anchor)
    {
        if(anchor == false || anchor.indexOf('#actions:') != 0)
            return;
        
        
        var ActionExists = false,split_anchor = anchor.slice(1).split(':');
        
        ActionExists = (split_anchor[2] != undefined);
        
        if(!$('#Side-Menu-Content-Wrapper').is(':visible'))
        {
            
            OpenResetSideMenuAction();
            if(split_anchor[1] != undefined && split_anchor[1] != '')
            {
                if(ActionExists)
                    SelectSpecificAction(split_anchor[1], split_anchor[2]);
                else
                    SelectSpecificCategory(split_anchor[1]);    
            }
            
        }
        /*    alert("MENU IS OPEN");
        else
            alert("YOU HAVE TO OPEN IT!");
            */
    }


$(document).ready(function() {
	var pos=false;
	var menu_scroll = false;

	var last_width = 0;
	var last_id = null;
	var last_handle = null;

	var warp = null;
	var warp_id = null;

	var _click_in_progress = false;

	$("div.button").click(function(){
		if(_click_in_progress)
		{
		}
		else if(!this.id)
		{
		}
		else if(menu_scroll && this.id != last_id)
		{
			_click_in_progress = !_click_in_progress;
			warp = $(this);
			warp_id = this.id;

			last_handle.children("div.menuslide").slideUp(200, function(){
				last_handle.animate({ width: last_width }, 100, function(){
					last_id = warp_id;
					last_handle = warp;

					last_width = warp.width();
					last_handle.animate({ width: "100" }, 200, function(){
						$(this).children("div.menuslide").slideDown(400);
						_click_in_progress = !_click_in_progress;
					});
				});
			});
		}
		else
		{
			last_id = this.id;
			last_handle = $(this);
			menu_scroll = !menu_scroll;
			_click_in_progress = !_click_in_progress;
		
			if(menu_scroll)
			{
				last_width = $(this).width();
				$(this).animate({ 
							width: "100"
						}, 200, function(){
					$(this).children("div.menuslide").slideDown(400);
					_click_in_progress = !_click_in_progress;
				});
			}
			else
			{
				$(this).children("div.menuslide").slideUp(200, function() {
					last_handle.animate({ width: last_width }, 100, function(){
						_click_in_progress = !_click_in_progress;
					});
				});
			}
		}
	});
    
    
    // Returns array of query paramatr url, if none data - return false;
    function getUrlSearch()
    {
        var SrchArr = [], srch = window.location.search;
        
        if(srch.indexOf('?') == 0)
            srch = srch.slice(1);
        
        if(srch == '')
            return false;
            
        //if has more than 1 paramater
        if(srch.indexOf('&') != -1)
        {
            SrchArr = srch.split('&');
            
            for(var i = 0;i < SrchArr.length;i++)
            {
                if(SrchArr[i].indexOf('=') != -1)
                    SrchArr[i] = SrchArr[i].split('=');
                else
                    SrchArr[i] = [SrchArr[i], '']; 
            }
        }
        else if(srch.indexOf('=') != -1)
            SrchArr[0] = srch.split('=');
        else
            SrchArr[0] = [srch, ''];
        return SrchArr;
    }
    
    $('.Panel-Lang-Item').click(function(){
        
        var SearchStr = '',getSearch = getUrlSearch();
        
        // Build the search query (the last one) without the `lang` paramater
        if(getSearch != false)
            for(var i = 0; i < getSearch.length;i++)
                if(getSearch[i][0] != 'lang')
                    SearchStr += getSearch[i][0] + '=' + getSearch[i][1] + '&'; 
        
        window.location.href = window.location.pathname + '?' + SearchStr + 'lang=' + $(this).attr('lang-key') + window.location.hash;
    });
    
    
    $('.Panel-Menu-Item-Title.Menu-Category-Active').click(function(){
        
        if(!$(this).children('span').length)
            return;
        OpenActionByAnchor($(this).children('span').attr('category-name'));
    });
    
    $('.Panel-Menu-Item-Sub-Menu-Title.Menu-Action-Active').click(function(){
        if(!$(this).children('span').length)
            return;
        
        
        OpenActionByAnchor($(this).children('span').attr('action-name'));
    });
    
    $('.Panel-Menu-Item').hover(function(){
        if($(this).children('.Panel-Menu-Item-Sub-Menu').children('.Panel-Menu-Item-Sub-Menu-Item').length)
        {
            $(this).children('.Panel-Menu-Item-Sub-Menu').slideDown('fast');
            //alert($(this).attr('class'));
        }
        //alert($(this).attr('class'));
            
    }, function(){
        $(this).children('.Panel-Menu-Item-Sub-Menu').slideUp('fast');
    });
    
    
    
    counter_start = 0;
    
    $('#Nav-Button-Left').click(function(){
        var ScrollLeft = $('#Panel-Menu-Content').scrollLeft();
        $('#Panel-Menu-Content').animate({
                scrollLeft: "-=" + 242/*116*/ + "px"
            }, 250, function(){
                
                
                //scrollArrowShow();
            });
        
    });
    
    $('#Nav-Button-Right').click(function(){
        var ScrollLeft = $('#Panel-Menu-Content').scrollLeft();
        $('#Panel-Menu-Content').animate({
                scrollLeft: "+=" + 242/*116*/ + "px"
            }, 250, function(){
                /*if(ScrollLeft == $(this).scrollLeft())
                    $('#Nav-Button-Right').css('visibility','none');
                else
                    $('#Nav-Button-Right').css('visibility','visible');*///counter_start++;
                //scrollArrowShow();
            });
        //scrollArrowShow();
    });
    
    /*fix right arrow when open sub menu - its go top*/
    
    


    last_hash = 'actions:';
    
    $('#Side-Menu-Button').click(function(){
       if($(this).hasClass('Menu-Open'))
       {
            $('#Side-Menu-Content').fadeOut('fast', function(){
                $('#Side-Menu-Content-Wrapper').slideUp('fast');
            });
            if(credit_title_open)
                $("#CreditsContainer").css('z-index','102');
                
            
            last_hash = window.location.hash;
            window.location.hash = '';
            $('#lights-off').fadeOut('fast');
            $('#Side-Menu-Button').text(ACTIONS_OPEN).removeClass('Menu-Open');
       }
       else
       {
            $('#Side-Menu-Content-Wrapper').slideDown('fast',function(){
                $('#Side-Menu-Content').fadeIn('fast');
            });
            if(credit_title_open)
                $("#CreditsContainer").css('z-index','1');
                
            window.location.hash = last_hash;
            $('#lights-off').fadeIn('fast');
            $('#Side-Menu-Button').text(ACTIONS_CLOSE).addClass('Menu-Open');
       }
    });
    
    
    /*
    $('#Side-Menu-Button').toggle(function(){
        $('#Side-Menu-Content-Wrapper').slideDown('fast',function(){
            $('#Side-Menu-Content').fadeIn('fast');
        });
        if(credit_title_open)
            $("#CreditsContainer").css('z-index','1');
            
        window.location.hash = last_hash;
        $('#lights-off').fadeIn('fast');
        $('#Side-Menu-Button').text(ACTIONS_CLOSE);
    },function(){
        $('#Side-Menu-Content').fadeOut('fast', function(){
            $('#Side-Menu-Content-Wrapper').slideUp('fast');
        });
        if(credit_title_open)
            $("#CreditsContainer").css('z-index','102');
            
        
        last_hash = window.location.hash;
        window.location.hash = '';
        $('#lights-off').fadeOut('fast');
        $('#Side-Menu-Button').text(ACTIONS_OPEN);
    });
     */ 
       
    $('.Side-Menu-Action-Category-Text').click(function(){
        
        
        
        //$('#Side-Menu-Form-Wrapper').hide();
        
        //Supposted$('.Menu-Form-Item').hide();
        $('.Side-Menu-Action-Item-Form').hide();
        
        $('.Side-Menu-Action-List').slideUp();
        $('.Side-Menu-Action-Category-Text').removeClass('Selected-Action-Category');//.children('a').removeClass('Selected-Action-Category-A');//.animate({left: '0px'}, 400);
        
        var ActionList = $(this).parents('.Side-Menu-Action-Category').children('.Side-Menu-Action-List');
        if(ActionList.css('display') == 'none')
        {
            window.location.hash = 'actions:' + $(this).parent().attr('category-name');//$(this).children('a').attr('href');
            
            ActionList.slideDown();
            $(this).addClass('Selected-Action-Category');//.children('a').addClass('Selected-Action-Category-A');//.animate({left: '+='+($(this).parent().width() - $(this).width())}, 400);
            //$(this).parents('.Side-Menu-Action-Category').children('.Side-Menu-Action-Search-Item-Input').focus();
            
            if(ActionList.find('.Side-Menu-Action-Item-Text.Selected-Action-Category').length)
                ActionList.find('.Side-Menu-Action-Item-Text.Selected-Action-Category').click();//.is('[selected=selected]').click();
            else
                ActionList.find('.Side-Menu-Action-Item-Text:not(.Action-InActive):first').click();
        }
        else
            window.location.hash = 'actions:';
        /*else
            $('#Side-Menu-Form-Wrapper').hide();
        */   
        
        //$(this).parents('.Side-Menu-Action-Category').children('.Side-Menu-Action-List').slideDown();
    });
    
    
    ActionItemSlide = false;
    
    $('.Side-Menu-Action-Item-Text').click(function(){
        
        
        if($(this).hasClass('Action-InActive')) return;
        
        window.location.hash = 'actions:' + $(this).parents('.Side-Menu-Action-Category').attr('category-name') + ':' + $(this).parent().attr('action-name');
        
        var form_target = /*$('#'+*/$(this).parents('.Side-Menu-Action-Item').find('.Side-Menu-Action-Item-Form');//attr('form-name'));
        
        /*if(form_target.length <= 0)
        {
            $('#Side-Menu-Form-Wrapper').hide();
            $('.Menu-Form-Item').hide();
        }
        else
            $('#Side-Menu-Form-Wrapper').show();
        
        if(ActionItemSlide)
            $('.Menu-Form-Item').slideUp();
        else
            $('.Menu-Form-Item').hide();
        */
        
        if(ActionItemSlide)
            $('.Side-Menu-Action-Item-Form').slideUp();
        else
            $('.Side-Menu-Action-Item-Form').hide();
        
        $(this).parents('.Side-Menu-Action-List').find('.Side-Menu-Action-Item-Text').removeClass('Selected-Action-Category');//.children('a').removeClass('Selected-Action-Category-A');
        
        if(form_target.css('display') == 'none')
        {
            if(ActionItemSlide)
                form_target.slideDown();
            else
                form_target.fadeIn(0);
            
            $(this).addClass('Selected-Action-Category');//.children('a').addClass('Selected-Action-Category-A');//.animate({left: '+='+($(this).parent().width() - $(this).width())}, 400);
            $(this).parents('.Side-Menu-Action-Category').children('.Side-Menu-Action-Search-Item-Input').focus();
        }
    });
    
    
    
});


function scrollArrowShow(counter_start) {
        alert("Panel-menu-Width: "+ $('#Panel-Menu').width()+"\n\r" + "Panel-Menu-Content-ScrollL: " + $('#Panel-Menu-Content').scrollLeft() + "\n\r"+"Panel-Menu-Content-Width: " + $('#Panel-Menu-Content').width());
        //if ( )
        
        
        var windw = $('#Panel-Menu').width();//important
        var documet = $('#Panel-Menu-Content').width();
            //alert($('#Panel-Menu').scrollLeft());
                if($('#Panel-Menu').scrollLeft() + windw + 230 == documet){
                    console.log("right!");
                }
            
        
        //alert("Item num: " + counter_start);
        var maxScroll = ($('#Panel-Menu').width() - $('#Panel-Menu-Content').scrollLeft()) - $('#Panel-Menu-Content').width() + 1;
        if ($('#Panel-Menu-Content').scrollLeft() == 0) {
            $('#Nav-Button-Left').css({visibility: 'hidden'});
        }else{
            $('#Nav-Button-Left').css({visibility: 'visible'});
        }
        if ($('#Panel-Menu-Content').scrollLeft() == ($('#Panel-Menu-Content').width() - $('#Panel-Menu').width())) {
            $('#Nav-Button-Right').css({visibility: 'hidden'});
        }else{
            $('#Nav-Button-Right').css({visibility: 'visible'});
        }
    }


function scrollThumb(direction) {
        if (direction=='Go_L') {
            $('#Panel-Menu-Content').animate({
                scrollLeft: "-=" + 230/*116*/ + "px"
            }, function(){
                // createCookie('scrollPos', $('#slide-wrap').scrollLeft());
                //scrollArrowShow();
                //$(this).width('100%');
            });
        }else
        if (direction=='Go_R') {
            $('#Panel-Menu-Content').animate({
                scrollLeft: "+=" + 230/*116*/ + "px"
            }, function(){
                // createCookie('scrollPos', $('#slide-wrap').scrollLeft());
                //scrollArrowShow();
                //$(this).width('99%');
            });
        }
       }