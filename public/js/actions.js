
//$(document).ready(function(){
  $(function(){
    
    
    //window.getSelection().toString();
    
    $.widget("myNameSpace.copy_text",{
        options: {
            className: ''
        },
        
        _create() {
            //var className = this.options.className;
            var textHtml = '<div class="text_to_copy">'+ this.element.text() +'</div>';
            
            //this.element.append('<span class="Copy_text_Button"><img src="public/img/copy.png" style="background: none;" alt="Copy Text" /></span>');
            this.element.html('<span class="Copy_text_Button"></span> '+ textHtml);
            this.element.find('.Copy_text_Button').hover(function(){
                
                
                $(this).siblings('.text_to_copy').selectText();
                $(this).css('background-image',"url('public/img/copy.png')");
                //.css('background-color','red');
                //alert("OK");
                //alert(window.getSelection().toString());
            },function(){
                window.getSelection().removeAllRanges();
                //alert("OK2");
                //$(this).parent().css('background','none');
            });
            
            this.element.find('.Copy_text_Button').click(function(){
                try {
                    var successful = document.execCommand('copy');
                    /*
                    if(successful)
                        $(this).css('background-image',"url('public/img/copy3.png')");//$(this).children().attr('src','public/img/copy3.png');
                    */
                    $(this).effect("pulsate", { times:1 }, 1000);
                    
                    var msg = successful ? 'successful' : 'unsuccessful';
                    console.log('Copying text command was ' + msg);
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
            })
        }
        
    })
    
    //$('body').copy_text();
    
    /*$.fn.copy_text = function(){
        this.append('<input type="button" class="Copy_text_Button" value="Copy" />');
        return this;
   }*/
    
    function moveAnimation(element, newParent)
    {
        element = $(element);
        newParent = $(newParent);
        
        var oldOffset = element.offset();
        element.appendTo(newParent);
        var newOffset = element.offset();
        
        var temp = element.clone().appendTo('body');
        temp.css({
            'position': 'absolute',
            'left': oldOffset.left,
            'top': oldOffset.top
        });
        element.hide();
        temp.animate({'top': newOffset.top,'left': newParent.width()},'slow', function(){
           element.show();
           temp.remove(); 
        });
    }
    
    function MinimizeSearchResult(ResultObj)
    {
        return false;
        ResultObj.find('.Panel-Search-Results-Item-Buttons').hide();
        ResultObj.find('.Panel-Search-Results-Item-SideMenu,.Panel-Search-Results-Item-Contexts').hide();
        ResultObj.animate({height: '0',width:'0'},'slow',function(){
            
            ResultObj.css({
               'position': 'relative',
               'margin-left': '5px',
               'display': 'inline-table',
               'border-width': '1px'
            });
            
            moveAnimation(this, '#Testing');
            $('#Testing').children().animate({left: -10}, 'slow');
            //$(this).find('.Panel-Search-Results-Item-Title').clone().appendTo('#Testing');
            //$(this).css('top','auto !important');//.animate({bottom: 0},'slow');
        });
        //ResultObj.find('.Panel-Search-Results-Item-Top-Bar').animate({'width': '0'}, 'slow');
    }
    
    
    $('.Result-Minimize-Button').click(function(){
        MinimizeSearchResult($(this).parents('.Panel-Search-Results-Item'));
    });
    
    function CloseSearchResult(ResultObj)
    {
        ResultObj.hide('puff');//puff
    }
    
    $('.Result-Close-Button').click(function(){
       CloseSearchResult($(this).parents('.Panel-Search-Results-Item')); 
    });
    
    function openReason(ReasonObj)
    {
        
        if(ReasonObj.css('display') == 'none')
        {
            ReasonObj.animate({right: ((isRTL) ? '105%' : '-55%')},'slow',function(){
            ReasonObj.animate({top: 0,height: '215px'/*'100%'*/},'slow',function(){
                ReasonObj.children('.Menu-Form-Item-Reason-Text').slideDown();
                });    
            });
            
            ReasonObj.show();    
        }
        
        
        
    }
    
    
    $('.Menu-Form-Item-Reason-Text').keydown(function(){
        //alert($(this).val().length)
        
        if($(this).val().length > 150)
            alert("error");
        else
            $(this).siblings('.Menu-Form-Item-Reason-Send').find('span').text(150 - ($(this).val().length));
        
        /*alert($(this).length);
        $(this).siblings('.Menu-Form-Item-Reason-Send').find('span').val($(this).length);*/
    });
    
    
    $('.Menu-Form-Item-Form form input').keydown(function(){
        openReason($(this).parents('.Menu-Form-Item').siblings('.Menu-Form-Item-Reason-Wrapper'));
    });
    
   /*$('.Actions-Form')*/$('.Menu-Form-Item-Form form').submit(function(){
        
        
        
        var FormObj = {},
            formValues = $(this).serializeArray();
        
        for(i = 0; i < formValues.length;i++)
            FormObj[formValues[i]['name']] = formValues[i]['value'];
        
        FormObj['ActionID'] = $(this).parents('.Side-Menu-Action-Item').attr('action-id');
        FormObj['CategoryID'] = $(this).parents('.Side-Menu-Action-Category').attr('category-id');
        FormObj[$(this).find('input[type=submit]').attr('name')] = true;
        
        
        SendAction(FormObj);
        
        event.preventDefault();
   });
   
   
   $('.Side-Menu-Action-Search-Item form').submit(function(){
    
        var FormObj = {
            InputValue: $(this).parents('.Side-Menu-Action-Search-Item').find('.Side-Menu-Action-Search-Item-Input input').val(),
            SelectName: $(this).parents('.Side-Menu-Action-Search-Item').find('.Auto-Complete-Live-Select').attr('select-name'),
            SelectValue: $(this).parents('.Side-Menu-Action-Search-Item').find('.Auto-Complete-Live-Select').val(),
            ActionID: $(this).parents('.Side-Menu-Action-Search-Item').siblings('.Side-Menu-Action-Item[action-name="search"]').attr('action-id'),
            CategoryID: $(this).parents('.Side-Menu-Action-Category').attr('category-id')
        };
        
        FormObj[$(this).find('input[type=submit]').attr('name')] = true;
        
        
        SendAction(FormObj);
        
        event.preventDefault();
   });
   
   
   var selected_live = 0;
   
   function setAutoCompleteValueToInput(selected_option_li)
   {
        selected_option_li.parents('.Auto-Complete-Live-Wrapper').siblings('input').val(selected_option_li.text());//.focus
        selected_option_li.parents('.Auto-Complete-Live-Wrapper').siblings('input').get(0).click();
        //alert("asd");
   }
   
   function setAutoCompleteOnKeyDown(auto_complete,key_selected_id)
   {
        if(key_selected_id == 17)
        {
            if(auto_complete.find('li.Auto-Complete-Live-Selected').length)
            {
                setAutoCompleteValueToInput(auto_complete.find('li.Auto-Complete-Live-Selected'));
                auto_complete.change();
                //auto_complete.find('li').removeClass('Auto-Complete-Live-Selected');
                //selected_live = 0;
                return;
            }
        }
        switch(key_selected_id)
        {
            case 37: // left
                break;
    
            case 38: // up
                auto_complete.find('li').removeClass('Auto-Complete-Live-Selected');
                selected_live--;
                if(selected_live < 1)
                    selected_live = auto_complete.find('li').index(auto_complete.find('li:last-child')) + 1;//5;
                break;
    
            case 39: // right
                break;
    
            case 40: // down
                auto_complete.find('li').removeClass('Auto-Complete-Live-Selected');
                selected_live++;
                if(selected_live > auto_complete.find('li').index(auto_complete.find('li:last-child')) + 1)//5)
                    selected_live = 1;
                break;
    
            default: return; // exit this handler for other keys
        }
        auto_complete.find('li:nth-child('+selected_live+')').addClass('Auto-Complete-Live-Selected');
   }
   
   /*$('input.Auto-Complete').blur(function(){
        if($(this).siblings('.Auto-Complete-Live-Wrapper').length)
            $(this).siblings('.Auto-Complete-Live-Wrapper').slideUp();
   });
   */
   
   function displayAutoComplete(auto_complete, display)
   {
        if(display && auto_complete.css('display') != 'block')
        {
            $('.Auto-Complete-Live-Wrapper').not(auto_complete).slideUp('fast');
            auto_complete.slideDown('fast');
            selected_live = 0;   
        }
        else if(!display)
        {
            $('.Auto-Complete-Live-Wrapper').slideUp('fast');
            //auto_complete.slideUp('fast');
        }
            
   }
   
   $('input.Auto-Complete').click(function(){
        if($(this).val().length > 0)
            displayAutoComplete($(this).siblings('.Auto-Complete-Live-Wrapper'), true);
        else
            displayAutoComplete($(this).siblings('.Auto-Complete-Live-Wrapper'), false);
   });
   
   //Prevent from closign auto complete result when clicking input and auto-complete items.
   //If the user click something else - sliding up the auto-complete.
   $('body').click(function(evt) {
        var $tgt = $(evt.target);
         if ($tgt.is('input.Auto-Complete') || $tgt.is('.Auto-Complete-Live-Wrapper ul li')  ) {
              //return;
          }else{
                displayAutoComplete($('.Auto-Complete-Live-Wrapper'), false);
                //$('.Auto-Complete-Live-Wrapper').slideUp('fast');
               //$('.addcommentOn').not($tgt).removeClass("addcommentOn");
          }
    });
    
    function setAutoCompleteSuggestion(AutoCompleteWrapper,Suggestion, columnName)
    {    
        AutoCompleteWrapper.find('ul li').remove();
        
        //console.log(Suggestion);
        
        jQuery.each(Suggestion, function(i, val) {
            AutoCompleteWrapper.children('ul').append('<li>'+val[columnName]+'</li>');
        });    
    }
    
    function SendAutoCompleteRequest(inputAuto,inputData)
    {
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_auto_complete.php',
            data: {InputValue: inputAuto.val(),
                    SelectName: inputData.SelectName,/*inputAuto.parents('tr').find('.Auto-Complete-Live-Select').attr('select-name'),*/
                    SelectValue: inputData.SelectValue,/*inputAuto.parents('tr').find('.Auto-Complete-Live-Select').val(),*/
                    CategoryID: inputData.CategoryID,/*inputAuto.parents('.Side-Menu-Action-Category').attr('category-id'),*/
                    ActionID: inputData.ActionID/*inputAuto.parents('.Side-Menu-Action-Item').attr('action-id')*/
            },
            //contentType: false,"application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr){
                /*$('#lightOff').css('z-index', 9999999999);
                $('#lightOff div').css('display', 'block');
                */
            },
            success: function(data) {
                //console.log(data);
                
                if(data.Success)
                    setAutoCompleteSuggestion(inputAuto.siblings('.Auto-Complete-Live-Wrapper'), data.CompleteSuggestion, data.ColumnName);
                
            },
            complete: function(data){
                //console.log(data);
            }
        });
    }
    
    $('input.Auto-Complete:not(.Auto-Complete-Search)').bind('keyup click', function(e){
        
        var AutoCompleteWrapper = $(this).siblings('.Auto-Complete-Live-Wrapper');
        
        if(!$(this).val())
        {
            displayAutoComplete(AutoCompleteWrapper, false);
            return;
        }
        
        
        if($(this).val().length > 0)
        {
            if(e.which != 17 && (e.which < 37 || e.which > 40))
                SendAutoCompleteRequest($(this), {
                    SelectName: $(this).parents('tr').find('.Auto-Complete-Live-Select').attr('select-name'),
                    SelectValue: $(this).parents('tr').find('.Auto-Complete-Live-Select').val(),
                    CategoryID: $(this).parents('.Side-Menu-Action-Category').attr('category-id'),
                    ActionID: $(this).parents('.Side-Menu-Action-Item').attr('action-id')
                });
            
            displayAutoComplete(AutoCompleteWrapper, true);
        }
            
        
        
    });
    
    sendSearch = true;
    
    function sendSearchRequest(SearchData)
    {
        if(!sendSearch)
            return false;
        sendSearch = false;
        
        //console.log(urlToSend);
        
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_send_search.php',
            data: urlToSend,
            //contentType: false,"application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr){
                /*$('#lightOff').css('z-index', 9999999999);
                $('#lightOff div').css('display', 'block');
                */
            },
            success: function(data) {
                //console.log(data);
                
                /*var ActionForm = $('.Side-Menu-Action-Category[category-id='+urlToSend['CategoryID']+']')
                .find('.Side-Menu-Action-Item[action-id='+urlToSend['ActionID']+']');
                
                var ErrorWrapper = ActionForm.find('.Menu-Form-Item-Error-Wrapper');
                
                if(!data.NoErrors)
                {
                    displayErrors(ErrorWrapper, data.Messages, true);
                }
                else if(!data.Success)
                {
                    displayErrors(ErrorWrapper, data.Messages, false);
                }
                else
                {
                    ActionForm.find('*').css('border','');
                    ErrorWrapper.children('ul').slideUp();
                    
                    insertResult(ActionForm.find('.Menu-Form-Item-Result-Wrapper'), data.Result, true);
                }*/
                
                sendSearch = true;
            },
            complete: function(data){
                console.log(data);
            }
        });
    }
    
    $('input.Auto-Complete-Search').bind('keyup click', function(e){
        
        var AutoCompleteWrapper = $(this).siblings('.Auto-Complete-Live-Wrapper');
        
        /*AutoCompleteWrapper.css({
            'width': '85%',
            'top': '-1px'
        });
        */
        if(!$(this).val())
        {
            displayAutoComplete(AutoCompleteWrapper, false);
            return;
        }
        
        //alert("sfsf");
        if($(this).val().length > 0)
        {
            var SearchData = {
                SelectName: $(this).parents('.Side-Menu-Action-Search-Item').find('.Auto-Complete-Live-Select').attr('select-name'),
                SelectValue: $(this).parents('.Side-Menu-Action-Search-Item').find('.Auto-Complete-Live-Select').val(),
                CategoryID: $(this).parents('.Side-Menu-Action-Category').attr('category-id'),
                ActionID: $(this).parents('.Side-Menu-Action-Search-Item').siblings('.Side-Menu-Action-Item[action-name="search"]').attr('action-id')
            };
            /*alert($(this).parents('.Side-Menu-Action-Search-Item').find('.Auto-Complete-Live-Select').attr('select-name'));
            alert($(this).parents('.Side-Menu-Action-Search-Item').find('.Auto-Complete-Live-Select').val());
            alert($(this).parents('.Side-Menu-Action-Category').attr('category-id'));
            alert($(this).parents('.Side-Menu-Action-Search-Item').siblings('.Side-Menu-Action-Item[action-name="search"]').attr('action-id'));*/
            /*if(e.which == 13)
            {
                SearchData.InputValue = $(this).parents('.Side-Menu-Action-Search-Item').find('.Side-Menu-Action-Search-Item-Input input').val();
                SendAction(SearchData);//SendSearchRequest(SearchData);//alert("enter")
            
            }
            else */if(e.which != 17 && (e.which < 37 || e.which > 40))
                SendAutoCompleteRequest($(this), SearchData);
            
            displayAutoComplete(AutoCompleteWrapper, true);
        }
            
        
        
    });
   
    $('input.Auto-Complete').bind('keydown', function(e){
        
        var AutoCompleteWrapper = $(this).siblings('.Auto-Complete-Live-Wrapper');
        
        if(AutoCompleteWrapper.length <= 0)
        {
            
            $(this).parent().append('<div class="Auto-Complete-Live-Wrapper" style="display: none;"></div>');
            AutoCompleteWrapper = $(this).siblings('.Auto-Complete-Live-Wrapper');
            AutoCompleteWrapper.append('<div class="Auto-Complete-Live-Title">' + AUTO_COMPLETE_TEXT + '</div>');
            AutoCompleteWrapper.append('<ul></ul>');
            /*
            for(i = 1;i <= 5;i++)
                AutoCompleteWrapper.children('ul').append('<li>Testing '+i+'</li>');
            */
            displayAutoComplete(AutoCompleteWrapper, true);
        }
        
        setAutoCompleteOnKeyDown(AutoCompleteWrapper,e.which);
        if(e.keyCode == '38' || e.keyCode == '40'){
            e.preventDefault();
        }
    });
   
   $('.Auto-Complete-Live-Wrapper').on('mouseover', function(){//.live('mouseover', function(){
        $(this).siblings('input').focus();
        $(this).find('ul li').removeClass('Auto-Complete-Live-Selected');
        
   });
   
   $('.Auto-Complete-Live-Wrapper ul li').on('mouseover', function(){//.live('mouseover',function(){
        selected_live = $(this).parent().children('li').index(this) + 1;
   });
   
   $('.Auto-Complete-Live-Wrapper ul li').on('mouseout', function(){//.live('mouseout',function(){
        selected_live = 0;
   });
   
   /*$('.Auto-Complete-Live-Wrapper ul li').hover(function(){
        selected_live = $(this).parent().children('li').index(this) + 1;
        //alert($(this).parent().children('li').index(this));
   },function(){
        selected_live = 0;
   });*/
   
   /*$('.Auto-Complete-Live-Wrapper ul li').on('click'*/
   $(document).on('click','.Auto-Complete-Live-Wrapper ul li', function(){//.live('click', function(){
        
        //alert("asd");
        setAutoCompleteValueToInput($(this));
        
   });
   
   function displayErrors(ErrorWrapper, Messages, flag)
   {
        ErrorWrapper.find('li').remove();
        
        if(flag)
        {
            jQuery.each(Messages, function(i, val) {
                
                jQuery.each(val, function(j, val2){
                    ErrorWrapper.children('ul').append('<li class="text-shadow"><label for="'+i+'">' + val2 + '</label></li>');
                });
            }); 
        }
        else
        {
            jQuery.each(Messages['System'], function(i, val) {
                ErrorWrapper.children('ul').append('<li class="text-shadow">' + val + '</li>');
            }); 
        }
        
        ErrorWrapper.children('ul').slideDown();
   }
   
   form_message_dialog_id = 1;
   
   function CreateFormMessageDialog(FormMessages, flagMini)
   {
    //alert(flagMini);
        var err_dialog = $('<div id="form_message_dialog_'+ form_message_dialog_id +'" title="Search List: UserID `matan`"><div class="form_message_dialog_inside_title">Results: '+ (flagMini ? 'Success' : 'Failure') +'</div></div>');
        
        //console.log('asdas');
        //console.log(errorMessages);
        jQuery.each(FormMessages, function(i, FormKey) {
            //aler("asdas");
            //console.log(ErrorKey['Translated']);
            err_dialog.append('<h1>' + FormKey['Translated'] + '</h1><ul id="form_message_'+ form_message_dialog_id +'_message_'+ i +'"></ul>')
            jQuery.each(FormKey['List'], function(j, val) {
                err_dialog.children('#form_message_'+ form_message_dialog_id +'_message_' + i).append('<li>' + val + '</li>');
            });
        }); 
        
        err_dialog.appendTo('body');
        
        $('#form_message_dialog_' + form_message_dialog_id).dialog({
            z_index: 99999,
            draggable: false,
            resizable: false,
            minWidth: 400,
            maxWidth: 800,
            dialogClass: 'form_message_dialog',
            /*position: {
                my: 'center',
                at: 'bottom'
            },*/
            buttons: {
                Ok: function(){
                    $(this).dialog('close');
                }
            }
        });
        
        if(!flagMini)
            $('.form_message_dialog[aria-describedby=form_message_dialog_' + form_message_dialog_id + ']').find('.ui-dialog-titlebar-minimize').hide();
        
        form_message_dialog_id++;
        //err_dialog.append();
   }
   
   
   sendAction = true;
    
    function SendAction(urlToSend)
    {
        if(!sendAction)
            return false;
        sendAction = false;
        
        //console.log(urlToSend);
        
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_send_action.php',
            data: urlToSend,
            //contentType: false,"application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr){
                /*$('#lightOff').css('z-index', 9999999999);
                $('#lightOff div').css('display', 'block');
                */
            },
            success: function(data) {
                //console.log(data);
                
                var ActionForm = $('.Side-Menu-Action-Category[category-id='+urlToSend['CategoryID']+']')
                    .find('.Side-Menu-Action-Item[action-id='+urlToSend['ActionID']+']');
                
                if(data.ActionName == 'Search')
                {
                    //alert('Search');
                    
                    //if(!data.NoErrors)
                    //{
                        //displayErrors(ErrorWrapper, data.Messages, true);
                        //alert("sad");
                        /*console.log(ActionForm);
                        if(data.Result.NOT_EXISTS !== undefined)
                        {
                            //console.log(data.Result);
                            data.Result.NOT_EXISTS = data.Result.NOT_EXISTS.replace(':column:', $('.Side-Menu-Action-Category[category-id='+urlToSend['CategoryID']+'] .Side-Menu-Action-Search-Item').find('button span:last-child').text());
                            //console.log(data.Result);
                            if(data.Messages.Message !== undefined)
                                data.Messages['DATA'][Object.keys(data.Messages['DATA']['List']).length] = data.Result.NOT_EXISTS;
                            else
                                data.Messages = {'DATA': {'Translated': data.Messages.DATA.Translated, 'List': {0: data.Result.NOT_EXISTS}}};
                            //console.log(data.Messages);
                        }
                        */
                        //ActionForm.find('input').blur();
                        CreateFormMessageDialog(data.Messages, data.NoErrors);
                    //}
                }
                else
                {
                    //alert("Regular");
                    
                    var ErrorWrapper = ActionForm.find('.Menu-Form-Item-Error-Wrapper');
                    
                    if(!data.NoErrors)
                    {
                        displayErrors(ErrorWrapper, data.Messages, true);
                    }
                    else if(!data.Success)
                    {
                        displayErrors(ErrorWrapper, data.Messages, false);
                    }
                    else
                    {
                        ActionForm.find('*').css('border','');
                        ErrorWrapper.children('ul').slideUp();
                        
                        insertResult(ActionForm.find('.Menu-Form-Item-Result-Wrapper'), data.Result, true);
                    }
                }
                
                sendAction = true;
            },
            complete: function(data){
                console.log(data);
            }
        });
    }
    
    /*$('.Menu-Form-Item-Error-Wrapper ul li').on('click'*/
    $(document).on('click', '.Menu-Form-Item-Error-Wrapper ul li', function(){//.live('click',function(){
        
        var CategoryAction = $(this).parents('.Menu-Form-Item-Error-Wrapper').siblings('.Menu-Form-Item').find('.Menu-Form-Item-Title').text();
        
        var NameId = $(this).parents('.Side-Menu-Action-Category').attr('category-name') + '-'
            + $(this).parents('.Side-Menu-Action-Item').attr('action-name').replace(/_/g,'-')
            + '-' + $(this).children('label').attr('for');
            
        
        if($(this).parents('.Side-Menu-Action-Item-Form').find('*[name='+NameId+']').length)
            {
                //alert($(this).parents('.Side-Menu-Action-Item-Form').find('*[name='+NameId+']').html());
                $(this).parents('.Side-Menu-Action-Item-Form').find('*[name='+NameId+']').siblings('button').css('border','2px solid rgb(0, 90, 255)').focus();
            }
        /*else
            alert("FALSE");*/ 
    });

DefaultRes = {0: FORM_N_DATA};    
ActionsResults = {};

    function ResultExists(resultWrapper)
    {
        return ((resultWrapper.parents('.Side-Menu-Action-Category').attr('category-name') in ActionsResults) &&
                (resultWrapper.parents('.Side-Menu-Action-Item').attr('action-name') in ActionsResults[resultWrapper.parents('.Side-Menu-Action-Category').attr('category-name')]));
    }
    
    function getResultsSelected(resultWrapper)
    {
        if(ResultExists(resultWrapper))
             return ActionsResults[resultWrapper.parents('.Side-Menu-Action-Category').attr('category-name')][resultWrapper.parents('.Side-Menu-Action-Item').attr('action-name')];
        return DefaultRes;
    }
    
    function getIndexSelected(selectElem, ActionsRes)
    {
        if(selectElem.val() !== undefined && selectElem.val() in ActionsRes)
            return selectElem.val();
        return Object.keys(ActionsRes).length - 1;
    }
    
    function insertResult(resultWrapper, result, display)
    {
        var CatName = resultWrapper.parents('.Side-Menu-Action-Category').attr('category-name'),
            ActName = resultWrapper.parents('.Side-Menu-Action-Item').attr('action-name');
        
        if(ResultExists(resultWrapper))
            ActionsResults[CatName][ActName][Object.keys(ActionsResults[CatName][ActName]).length] = result;
        else
        {
            if(!(CatName in ActionsResults))
                ActionsResults[CatName] = {};
            ActionsResults[CatName][ActName] = {0: result};
        }
            
        resultWrapper.find('.Menu-Form-Item-Result-Select select option').remove();
        jQuery.each(ActionsResults[CatName][ActName], function(i, val) {
            resultWrapper.find('.Menu-Form-Item-Result-Select select').prepend('<option value="'+i+'">'+FORM_RESULT+' #' + (parseInt(i) + 1) + '</option>');
        });
        
        
        resultWrapper.find('.Menu-Form-Item-Result-Select select').change();
        
        if(display)
        {
            displayResult(resultWrapper, true);
            
            if($(resultWrapper).children('.Menu-Form-Item-Result-List-Wrapper').css('display') != 'block')
                $(resultWrapper).children('.Menu-Form-Item-Result-Title').click();
        }
            
    }
    
    function displayResult(resultWrapper, display)
    {
        // Display the result...
        
        var ActionsRes = getResultsSelected(resultWrapper);
        var IndexRes = getIndexSelected(resultWrapper.siblings('.Menu-Form-Item-Result-Select').children(), ActionsRes);
        
        if(display == true)
        {
            resultWrapper.find('.Menu-Form-Item-Result-Data-Item:first-child').html(ActionsRes[IndexRes]);
            resultWrapper.slideDown();
        }
        else// Hide the result...
        {
            resultWrapper.slideUp();
        }
    }
    
    $('.Menu-Form-Item-Result-Title').click(function(){
        if($(this).hasClass('Result-Open'))
        {
            $(this).siblings('.Menu-Form-Item-Result-Select').slideUp();
            displayResult($(this).siblings('.Menu-Form-Item-Result-List-Wrapper'), false);
            $(this).text(FORM_DISPLAY_RESULTS).removeClass('Result-Open');
        }
        else
        {
            displayResult($(this).siblings('.Menu-Form-Item-Result-List-Wrapper'), true);
            $(this).text(FORM_HIDE_RESULTS).addClass('Result-Open');
            $(this).siblings('.Menu-Form-Item-Result-Select').slideDown();
        }
    });
    
    /*$('.Menu-Form-Item-Result-Title').toggle(function(){
        displayResult($(this).siblings('.Menu-Form-Item-Result-List-Wrapper'), true);
        $(this).text('Close Result');
        $(this).siblings('.Menu-Form-Item-Result-Select').slideDown();
    },function(){
        $(this).siblings('.Menu-Form-Item-Result-Select').slideUp();
        displayResult($(this).siblings('.Menu-Form-Item-Result-List-Wrapper'), false);
        $(this).text('Display Result');
    });*/
    
    $('.Menu-Form-Item-Result-Select select').change(function(){
        
        var ActionsRes = getResultsSelected($(this));
        var IndexRes = getIndexSelected($(this), ActionsRes);
        
        $(this).parent().siblings('.Menu-Form-Item-Result-List-Wrapper').find('.Menu-Form-Item-Result-Data-Item:first-child').
        html(ActionsRes[IndexRes]).animate({opacity:0},200,"linear",function(){
            $(this).animate({opacity:1},200).css('color','');
        }).css('color','yellow');
    });
});