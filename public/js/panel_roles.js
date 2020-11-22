
$(document).ready(function(){
   
   var SELECTION_LIST_HTML = {};
   
   SetSelectionGradeListAsHtml();
   
   function SetMultiSelectSettings(){
        $(".multi_select").multiselect({
            checkAllText: CHECK_ALL,
            uncheckAllText: UNCHECK_ALL,
            noneSelectedText: NONE_SELECTED,
            selectedText: SELECTED,
            selectedList: 4,
            minWidth: 300,
            
        });
   }
   SetMultiSelectSettings();
   
   
   /*
   $(".multi_select").multiselect({
    checkAllText: CHECK_ALL,
      uncheckAllText: UNCHECK_ALL,
      noneSelectedText: NONE_SELECTED,
      selectedText: SELECTED,
      selectedList: 4,
      minWidth: 300,
      maxWidth: 400
   });
   */
   
   /**
    Start of Getting data by selecting grade.
   **/
   
   
   /*
   
   Query to get the lis of the desire selection for each grade
   
    SELECT  MAX(PFSID) AS PFSID, SelectionName,MAX(Grade) AS Grade FROM PanelFormSelection
    WHERE PAID = 3 AND (Grade = 252 OR Grade = 0)
    GROUP BY SelectionName;
    
   Query to find all the options of the specific Grade.
    
    SELECT * FROM PanelFormSelectionSetting 
    WHERE Grades = '254' OR Grades LIKE '254,%' OR Grades LIKE '%,254' OR Grades LIKE '%,254,%';
   
   */
   
   
   
   $('#SelectGrades').change(function(lol){
    //alwer("sel");
        gettingGradeActionsSettings($(this).val());
   });
   
   /*$('.ui-multiselect').focus(function(){//$('#SelectGrades').on('focus',function(){
        alert("sada");
        $(this).attr('old_value', $(this).val());
   });
   */
   
   function UncheckAllCategory(CategoryID)
   {
        //var SuperExistsCheckbox = $('.Panel-Roles-Category-Actions[category-id=' + CategoryID + '] input[type=checkbox].checkbox_exists_all');
        //$('.Panel-Roles-Category-Actions input[type=checkbox].checkbox_exists_all:checked').click();
        /*SuperExistsCheckbox.each(function(index){
            /*if(!$(this).is(':checked'))
                $(this).prop('checked', true);
            $(this).click();*/
            /*alert("asd");
            if($(this).is(':checked'))
            {
                alert("asd");
                $(this).click();
            }
                
        });*/
        
        //SuperExistsCheckbox.prop('checked', true).click();
        
        $('.checkbox_exists').prop('checked', false);
        $('.checkbox_display').prop('checked', false);
        $('.checkbox_active').prop('checked', false);
        
        $('.Actions-All td:last-child, .Action-Item td:last-child').removeClass(ActionExists + ' ' + ActionDisplay + ' ' + ActionActive + ' text-shadow').css('color','');
        
        
   
        
        
        /*$('.Panel-Roles-Category-Actions .checkbox_exists_all').each(function(){
            if($(this).is(':checked'))
                $(this).click();  
        });*/
   }
   
   function SettingActions(CategoryID, Actions)
   {
        var CategoryActionsWrapper = $('.Panel-Roles-Category-Actions[category-id=' + CategoryID + ']'),
            ActionWrapper;
        jQuery.each(Actions, function(j, ActionItem) {
            
            //alert("Category: "+ CategoryID + ", Action: "+ActionItem['PAID']);
            ActionWrapper = CategoryActionsWrapper.find('.Action-Item[action-id=' + ActionItem['PAID'] + ']');
            
            //ActionWrapper.find('input').click();
            
            //ActionWrapper.children().click();
            
            ActionWrapper.find('.checkbox_action_exists').click();//.prop('checked', false).click();
            
            //console.log();
            
            //alert()
            
            //console.log(ActionWrapper);
            
            if(ActionItem.Display)
                ActionWrapper.find('.checkbox_action_display').click();//.prop('checked', false).click();
            if(ActionItem.Active)
                ActionWrapper.find('.checkbox_action_active').click();//.prop('checked', false).click();
            
            //alert(ActionItem['Name']);
        });
   }
   
   function SettingActionsCheckBoxesValues(Categories)
   {
        $(CategorySelected).addClass('Save-Selected-Category');
            
        $('.Panel-Roles-Category-Actions').addClass('Panel-Roles-Category-Actions-Selected');
        
        //UncheckAllCategory("sa");
        
        //alert("asd2");
        
        console.log(Categories);
        
        //setTimeout(function(){
            jQuery.each(Categories, function(i, CategoryItem) {
                //console.log(CategoryItem);
                //alert(CategoryItem['PCID']);
                //UncheckAllCategory(CategoryItem['PCID']);
                
                    
                    //alert("asd");
                    SettingActions(CategoryItem['PCID'],CategoryItem['sub_menu']);
                
                    //alert(CategoryItem['PCID']);
                    //setTimeout(function(){
                        $('.Panel-Roles-Category-Actions[category-id=' + CategoryItem['PCID'] + ']').find('.Action-Item:first-child').click();
                    //}, 0);
                    //alert("asd");
            });
        //}, 0);
        //alert("asd");
        setTimeout(function(){
            $(CategorySelected).not('.Save-Selected-Category').removeClass('Panel-Roles-Category-Actions-Selected');
            $(CategorySelected).removeClass('Save-Selected-Category');
            
            setAllCheckboxesByRegular();
        }, 0);
        //setAllCheckboxesByRegular();
   }
   
   function isAllActionsSelectedBySpecific(CategoryWrapper,fieldName)
   {
        //alert($(CategoryWrapper + ' .' + ActionInput[fieldName]['ANY_REGULAR_INPUT'] + ':checked').length + '/' + $(CategoryWrapper + ' .' + ActionInput[fieldName]['ANY_REGULAR_INPUT']).length);
        return ($(CategoryWrapper + ' .' + ActionInput[fieldName]['ANY_REGULAR_INPUT'] + ':checked').length == $(CategoryWrapper + ' .' + ActionInput[fieldName]['ANY_REGULAR_INPUT']).length);
   }
   
   function setAllCheckboxesByRegular()
   {
    //alert("asd");
        var CategoryWrapper;
        for(var i = 1;i <= 3;i++)
        {
            //alert(ActionInput.exists.SUPER_INPUT);
            CategoryWrapper = '.Panel-Roles-Category-Actions[category-id=' + i + ']';
            if(isAllActionsSelectedBySpecific(CategoryWrapper,'exists') && !$(CategoryWrapper).find('.' + ActionInput.exists.SUPER_INPUT).is(':checked'))
                $(CategoryWrapper).find('.' + ActionInput.exists.SUPER_INPUT).click();
            /*else
                alert("error1");*/
            if(isAllActionsSelectedBySpecific(CategoryWrapper,'display') && !$(CategoryWrapper).find('.' + ActionInput.display.SUPER_INPUT).is(':checked'))
                $(CategoryWrapper).find('.' + ActionInput.display.SUPER_INPUT).click();
            /*else
                alert("error2");*/
            if(isAllActionsSelectedBySpecific(CategoryWrapper,'active') && !$(CategoryWrapper).find('.' + ActionInput.active.SUPER_INPUT).is(':checked'))
                $(CategoryWrapper).find('.' + ActionInput.active.SUPER_INPUT).click();
            /*else
                alert("error3");*/
        }
   }
       
    function SetSelectionGradeListAsHtml()
    {
        var groupCollect;
        jQuery.each(_SELECTION_LIST, function (SelectName, SelectData) {
           
            SELECTION_LIST_HTML[SelectName] = $('<select title="Basic example" multiple="multiple" id="SE_'+SelectName+'" name="'+SelectName+'" size="5" class="selection_permission multi_select"></select>');
           
            jQuery.each(SelectData['SELECT'], function (optionNum, optionData) {
                if(optionData['sub_title'] != '')
                {
                    groupCollect = $('<optgroup label="'+optionData['sub_title']+'"></optgroup>');
                    jQuery.each(optionData['sub_options'], function(subOptionNum, subOptionData){
                        groupCollect.append('<option value="'+subOptionData['option_id']+'">'+subOptionData['textTranslated']+'</option>')
                    });
                    SELECTION_LIST_HTML[SelectName].append(groupCollect);
                }
                else
                    SELECTION_LIST_HTML[SelectName].append('<option value="'+optionData['option_id']+'">'+optionData['textTranslated']+'</option>')    
            });
        });
        
   }
   
   function AddSelecctedSelections()
   {
        //alert("happy");
        //console.log($('#AddSelections').val());
        var getSelectedSelectons = $('#AddSelections').val(),
            optionExists = false,
            SelectionItem;
        if(getSelectedSelectons !== null)
        {
            for(var i = 0;i < getSelectedSelectons.length;i++)
            {
                if($('#SE_'+getSelectedSelectons[i]).length <= 0)
                {
                    optionExists = true;
                    
                    SelectionItem = $('<tr class="SelectionItem"></tr>');
                    SelectionItem.append('<td><label><sup>'+_SELECTION+'</sup> '+_SELECTION_LIST[getSelectedSelectons[i]]['SELECTION_NAME_TR']+'</label></td>');
                    SelectionItem.append('<td class="SelectAppear"></td>');
                    SelectionItem.find('.SelectAppear').append(SELECTION_LIST_HTML[getSelectedSelectons[i]]);
                    
                    $('.Selection-Info').append(SelectionItem);
                }
            }
            
            if(optionExists)
            {
                SetSelectionListForAdd();
                SetMultiSelectSettings();
                //alert("asd");
                /*$(".multi_select").multiselect({
                    checkAllText: CHECK_ALL,
                      uncheckAllText: UNCHECK_ALL,
                      noneSelectedText: NONE_SELECTED,
                      selectedText: SELECTED,
                      selectedList: 4,
                      minWidth: 300,
                      maxWidth: 'auto',
                      widthManuallly: true
                   });*/
                   
            }
                //alert(getSelectedSelectons[i]);
            
            /*jQuery.each(getSelectedSelectons, function (index, name) {
                alert(na);
            });*/
        }
        
   }
   
   function SetSelectionListForAdd()
   {
        //$('#AddSelections').remove();
        var AddSelections = $('<select title="Basic example" multiple="multiple" id="AddSelections" size="5" class="multi_select"></select>');
        
        var optionExists = false;
        
        jQuery.each(SELECTION_LIST_HTML, function (SelectName, SelectData) {
            //$('#AddSpace').append(SelectData);
            if($('#SE_'+SelectName).length <= 0)
            {
                optionExists = true;
                AddSelections.append('<option value="'+SelectName+'">'+_SELECTION+' '+_SELECTION_LIST[SelectName]['SELECTION_NAME_TR']+'</option>');
            }
                
        });
        
        if(optionExists)
            $('#Selection-List').html(AddSelections).append('<button id="AddSelectionButton">'+_SELECTIONS_ADD+'</button>');
        else
            $('#Selection-List').html('');
   }
   
   function SetSelectionGradeData(SelectionsData)
   {
        //$('.Selection-Action').append('<table class="Selection-Info"></table>');
        
        var SelectionItem;
        var selectCollect;
        var groupCollect;
        
        jQuery.each(SelectionsData, function(SelectName, SelectData) {
            //alert(SelectName);
            
            
            //Create a div every select
            selectCollect = SELECTION_LIST_HTML[SelectName];
            
            //Use Grade Data to Check
            jQuery.each(SelectData['SELECT'], function (optionGradeNum, optionGradeData) {
                if(optionGradeData['sub_title'] !== undefined && optionGradeData['sub_title'] != '')
                {
                     jQuery.each(optionGradeData['sub_options'], function(subOptionGradeNum, subOptionGradeData){
                        //$('#SE_'+SelectName+' option[value='+subOptionGradeData['option_id']+']').prop('selected', true);
                        selectCollect.find('option[value='+subOptionGradeData['option_id']+']').prop('selected', true);
                        //console.log(subOptionGradeData['option_id']);
                    });   
                }
                else
                {
                    //$('#SE_'+SelectName+' option[value='+optionGradeData['option_id']+']').prop('selected', true);
                    selectCollect.find('option[value='+optionGradeData['option_id']+']').prop('selected', true);
                    //console.log(optionGradeData['option_id']);
                }
                    
                    
            });
            
            //console.log(SELECTION_LIST_HTML[SelectName]);
            
                SelectionItem = $('<tr class="SelectionItem"></tr>');
                SelectionItem.append('<td><label class="text-shadow"><sup>'+_SELECTION+'</sup> '+_SELECTION_LIST[SelectName]['SELECTION_NAME_TR']+'</label></td>');
                SelectionItem.append('<td class="SelectAppear"></td>');
                SelectionItem.find('.SelectAppear').append(selectCollect);
                
                $('.Selection-Info').append(SelectionItem);
        });
        
   }
   
   function ClearSelections()
   {
        $('.SelectionItem').remove();
        jQuery.each(SELECTION_LIST_HTML, function(SelectName, SelectData){
            SelectData.find('option').prop('selected', false);
        });
   }
   
   function getSelectedSelections()
   {
        var PrepareData = false,holderData, index = 0;
        $('.selection_permission').each(function(i, selection){
            //alert(selection.attr('id'));
            holderData = $(this).val();
            
            if(holderData !== null)
            {
                if(index == 0) 
                    PrepareData = {};
                for(var j = 0;j < holderData.length;j++)
                PrepareData[index++] = holderData[j];    
            }
            
            
            
                
            /*$(this).val().each(function(j, option){
                alert(option);
            });*/
            
            //PrepareData[$(this).attr('name')] = $(this).val();
            //console.log($(this).val()); 
        });
        return PrepareData;
   }
   
   function getSelectedGrade()
   {
        return $('#SelectGrades').val();
   }
   
   function SaveSelections()
   {
        alert("SaveSelections");
        SendSaveSelectedRequest(getSelectedGrade(), getSelectedSelections(), 1);
   }
   
   function getSelectedActions()
   {//Panel-Roles-Category-Actions
    alert("asd");
    
        var SelectedActions = {},CategoryID, index = 0;
        $('.Panel-Roles-Category-Actions').each(function(){
            CategoryID = $(this).attr('category-id');
            $(this).find('.Action-Item').each(function(){
                
                SelectedActions[index++] = {
                    'CategoryID': CategoryID,
                    'ActionID': $(this).attr('action-id'),
                    'Exists': $(this).find('.checkbox_exists').is(':checked'),
                    'Display': $(this).find('.checkbox_display').is(':checked'),
                    'Active': $(this).find('.checkbox_active').is(':checked') };
                
                //console.log($(this)); 
            });    
        });
    
        return SelectedActions;
   }
   
   function SaveActions()
   {
        alert("SaveActions");
        SendSaveSelectedRequest(getSelectedGrade(), getSelectedActions(), 0);
        
        //console.log(getSelectedActions());
   }
   
   
   function SendSaveSelectedRequest(grade, selectedata, saveType)
   {
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_save_panel_roles_settings.php',
            data: {Grade: grade,SelectedData: selectedata,SaveType: saveType},
            //contentType: false,"application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr){
                $('.Loading-Changes').css('display','block');
                
                /*$('#lightOff').css('z-index', 9999999999);
                $('#lightOff div').css('display', 'block');
                */
            },
            success: function(data) {
                
                
                if(data.Success)
                {
                    alert("The changes has been saved.");
                }
                
                
                //console.log(data.)
                
                console.log(data);
                
                
                
                
                    
            },
            complete: function(data){
                $('.Loading-Changes').css('display','none');
                
                console.log(data);
            }
        });
   }
   
   
   function gettingGradeActionsSettings(grade)
   {
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_grade_actions_settings.php',
            data: {Grade: grade},
            //contentType: false,"application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr){
                $('.Loading-Changes').css('display','block');
                
                /*$('#lightOff').css('z-index', 9999999999);
                $('#lightOff div').css('display', 'block');
                */
            },
            success: function(data) {
                //console.log(data);
                
                //console.log(data);
                
                UncheckAllCategory(1);
                
                ClearSelections();
                //alert("asd");
                if(data.HasActions)
                {
                    //alert("asd");
                    //UncheckAllCategory(1);
                    //setTimeout(function(){
                        SettingActionsCheckBoxesValues(data.GradesData);    
                    //}, 0);
                    
                    console.log(data.Selections);
                    //SetSelectsData();
                    
                    SetSelectionGradeData(data.Selections);
                    
                    
                }
                
                $('#Selected-Permission-Title #PermissionGrade').text(grade);
                    
                SetSelectionListForAdd();
                    
                $('#Panel-Roles-Category-Info').show(0);
                //$('#SaveActions button').show();
                $('#Panel-Roles-Menu tr:last-child').show();
                $('#SaveActionsTitle span').text(_ACTIONS+':')//.hide();
                
                
                
                SetMultiSelectSettings();
                
                /*else
                {
                    $('#Panel-Roles-Category-Info').hide(0);
                }
                /*else
                {
                    //alert($('#SelectGrades').attr('old_value'));
                    
                    //console.log($('#SelectGrades').data('events'));
                    
                    if($('#SelectGrades').hasClass('multiselect_object'))
                    {
                        $('.ui-multiselect-menu input[name=multiselect_SelectGrades]').parent().removeClass('ui-state-active');
                        $('.ui-multiselect-menu input[value=255]').parent().addClass('ui-state-active');
                        
                        $('#SelectGrades').siblings('.ui-multiselect').children(':last-child').text('255')
                        
                    }
                    else
                    {
                        $('#SelectGrades option').removeAttr('selected');
                        $('#SelectGrades option:contains("255")').attr('selected', true);
                    }
                    
                    
                    //ui-state-active
                }*/
                
                //if(data.Success)
                  //  setAutoCompleteSuggestion(inputAuto.siblings('.Auto-Complete-Live-Wrapper'), data.CompleteSuggestion, data.ColumnName);
                
            },
            complete: function(data){
                $('.Loading-Changes').css('display','none');
                
                console.log(data);
            }
        });
   }
   
   $(document).on('click', '#AddSelectionButton', function(){
        AddSelecctedSelections();
   });
   
   $(document).on('click', '#SaveSelectionsButton', function(){
        SaveSelections();
   });
   
   $(document).on('click', '#SaveActionsButton', function(){
        SaveActions();
   });
   
   
   
   $('#Panel-Roles-Category-Title input[type=checkbox]').on('click', function(){//.live('click', function(){
        if($(this).is(':checked'))
            $(this).parent().removeClass('Category-InActive');
        else
            $(this).parent().addClass('Category-InActive');
   });
   
   $('.Action-Item').on('click', function(event){//.live('click', function(){
        
        if($(this).hasClass('Save-Selected'))
            return false;
        
        //$(this).children(':first-child').children('input').click();
        
        //console.log(event.target.type);
        return;
        if(event.target.type !== 'checkbox')
        {
            var ActionID = $(this).attr('action-id');
        
            $(CategorySelected + ' .Action-Item td:last-child').removeClass('Action-Category-Selected');
            
            $(this).children(':last-child').addClass('Action-Category-Selected');
            
            
            
            //$('.Selection-Action').css('display','none');
                            //alert(ActionID);
                            //$('.Selection-Action[action-id!=' + ActionID + ']').slideUp('fast', function(){
                //$('.Selection-Action[action-id=' + ActionID + ']').show('easing');
            //});
            /*
            setTimeout(function(){
                $('.Selection-Action[action-id=' + ActionID + ']').slideDown();
            }, 0);*/
            
            //alert("asd");    
        }
        
        
   });
   
   /*$('.Action-Item td:last-child').live('click', function(){
        
        if($(this).hasClass('Action-Category-Selected'))
            $(this).removeClass('Action-Category-Selected');
        else
            $(this).addClass('Action-Category-Selected');
        alert("asd");
        /*$('.Action-Item td:last-child').removeClass('Action-Category-Selected');
        
        $(this).children(':last-child').addClass('Action-Category-Selected');
        //alert("asd");
   });*/
   
   function SetNewCategorySelected(CategoryID)
   {
        $('.Panel-Roles-Category-Actions[category-id=' + CategoryID + ']').addClass('Panel-Roles-Category-Actions-Selected');
        $(CategorySelected).show('easing');
        
        if($(CategorySelected + ' .Action-Item .Action-Category-Selected').length)
        {
            //alert("test1");
            $(CategorySelected + ' .Action-Item .Action-Category-Selected').click();
        }
        else
        {
            //alert(CategorySelected);
            $(CategorySelected + ' .Action-Item:first').click();
        }
            
   }
   
   CategorySelected = '.Panel-Roles-Category-Actions-Selected';
   CategorySelectedI = 'Panel-Roles-Category-Item-Selected';
   
   $('.Panel-Roles-Category-Item').click(function(){
        
        if($(this).hasClass(CategorySelectedI))
            return false;
        var CategoryID = $(this).attr('category-id');
        //alert("asd");
        $('.Panel-Roles-Category-Item').removeClass(CategorySelectedI);
        $(this).addClass(CategorySelectedI);
        
        if($(CategorySelected).length)
            $(CategorySelected).slideUp('fast', function(){
                $('.Panel-Roles-Category-Actions').removeClass('Panel-Roles-Category-Actions-Selected');
                SetNewCategorySelected(CategoryID);
            });
        else
            SetNewCategorySelected(CategoryID);
        
        //CategorySelected = '.Panel-Roles-Category-Actions';//[category-id="' + $(this).attr('category-id') + '"]';
        //alert(CategorySelected);
   });
   
   $('.Panel-Roles-Category-Item:first-child').click();
   
   
   ActionDisplay = 'Action-Category-Display';
   ActionActive = 'Action-Category-Active';
   ActionExists = 'Action-Category-Approve';
   
   ActionInput = {
        display: {ANY_INPUT: 'checkbox_display', SUPER_INPUT: 'checkbox_display_all', ANY_REGULAR_INPUT: 'checkbox_action_display'},
        active:  {ANY_INPUT: 'checkbox_active', SUPER_INPUT: 'checkbox_active_all', ANY_REGULAR_INPUT: 'checkbox_action_active'},
        exists:  {ANY_INPUT: 'checkbox_exists', SUPER_INPUT: 'checkbox_exists_all', ANY_REGULAR_INPUT: 'checkbox_action_exists'}
   };
   
   // Check if all regular cheboxes are selected by fieldName - returns true when true, otherwise- false;
   function isAllActionsSelectedBy(fieldName)
   {
        return ($(CategorySelected + ' .' + ActionInput[fieldName]['ANY_REGULAR_INPUT'] + ':checked').length == $(CategorySelected + ' .' + ActionInput[fieldName]['ANY_REGULAR_INPUT']).length);
   }
   
   function SetActionColorStatus(ActionRow)
   {
        var hasClassDisplay = ActionRow.find('.' + ActionInput.display.ANY_INPUT).is(':checked'),
            hasClassActive  = ActionRow.find('.' + ActionInput.active.ANY_INPUT).is(':checked');
            
        //if a display/active chechbox are selected - check exists checkbox if not selected.
        if(hasClassDisplay || hasClassActive)
        {
            var checbox_exists = ActionRow.children(':first-child').children('input');
            if(checbox_exists.hasClass(ActionInput.exists.ANY_INPUT) && !checbox_exists.is(':checked'))
                checbox_exists.click();
        }
        
        var textHolder = ActionRow.children(':last-child');
        
        //if just display or just active cheboxes are selected for the action, set color to white.
        if((hasClassDisplay && !hasClassActive) || (!hasClassDisplay && hasClassActive))
            textHolder.attr('style', 'color: white !important');
        else
            textHolder.css('color', '');
        
        //if both of the checkboxes (display and active) are selected, add text shadow to the action.
        if(hasClassDisplay && hasClassActive)
            textHolder.addClass('text-shadow');// Action-Category-Approve');
        else
            textHolder.removeClass('text-shadow');// Action-Category-Approve');
   }
   
   
   function SetActionColorStatusFix(textHolder)
   {
        /*var hasClassDisplay = textHolder.parents('.Action-Item').find('.checkbox_display').is(':checked');//.hasClass(ActionDisplay),
        hasClassActive  = textHolder.parents('.Action-Item').find('.checkbox_active').is(':checked');//.hasClass(ActionActive);
        */
        var hasClassDisplay = textHolder.hasClass(ActionDisplay),
        hasClassActive  = textHolder.hasClass(ActionActive);
        
        
        //alert(hasClassDisplay + " " + hasClassActive);
        
        //if a display/active chechbox are selected - check exists checkbox if not selected.
        if(hasClassDisplay || hasClassActive)
        {
            var checbox_exists = textHolder.siblings().filter(':first-child').children('input');
            if(checbox_exists.hasClass(ActionInput.exists.ANY_INPUT) && !checbox_exists.is(':checked'))
                checbox_exists.click();
        }
        
        //if just display or just active cheboxes are selected for the action, set color to white.
        if((hasClassDisplay && !hasClassActive) || (!hasClassDisplay && hasClassActive))
            textHolder.attr('style', 'color: white !important');
        else
            textHolder.css('color', '');
        
        //if both of the checkboxes (display and active) are selected, add text shadow to the action.
        if(hasClassDisplay && hasClassActive)
            textHolder.addClass('text-shadow');// Action-Category-Approve');
        else
            textHolder.removeClass('text-shadow');// Action-Category-Approve');
            
        if(!hasClassDisplay && !hasClassActive)
            textHolder.css('color', '');
   }
   
   function SetStatusAction(ActionInput, HasClass, flag, flagNoExists)
   {
        //Gettin action text.
        var getTextHolder = ActionInput.parent().siblings().filter(':last-child');
        
        //if checkbox selected, add style class to the action text. otherwise - remove..
        if(flag)
        {
            ActionInput.prop('checked',true);
            getTextHolder.addClass(HasClass);
        }
        else
        {
            ActionInput.prop('checked',false);
            getTextHolder.removeClass(HasClass);
        }
        //alert("lol " + flagNoExists);
        //If the checkbox is not a an "exists checbox"
        if(flagNoExists)
            SetActionColorStatusFix(getTextHolder);
   }
   
   $(document).on('click', CategorySelected + ' input[type=checkbox].checkbox_super', function(){//.live('click',function(){
   //$('.Panel-Roles-Category-Actions-Selected input[type=checkbox].checkbox_super').on('click', function(){//.live('click', function(){
        
        //alert("asd");
        
        var IsSuperChecked,getTextHolder, ExistsSuper = 0;
        
        var IsLastActionSelected = $(CategorySelected + ' .Action-Item:last-child td:last-child').hasClass('Action-Category-Selected');//removeClass('Action-Category-Selected');
        
        $(CategorySelected + ' .Action-Item').addClass('Save-Selected');
        
        if($(this).hasClass(ActionInput.display.SUPER_INPUT))
        {
            IsSuperChecked = $(this).is(':checked');
            $(CategorySelected + ' .' + ActionInput.display.ANY_REGULAR_INPUT).each(function(){
                getTextHolder = $(this).parent().siblings().filter(':last-child');
                
                if(IsSuperChecked)
                {
                    $(this).prop('checked',true);
                    getTextHolder.addClass(ActionDisplay);
                }
                else
                {
                    $(this).prop('checked',false);
                    getTextHolder.removeClass(ActionDisplay);
                }
                SetActionColorStatusFix(getTextHolder);
            });//attr('checked','').click();
            //alert("sad");
            //$('.' + ActionInput.display.ANY_REGULAR_INPUT + ':first-child').parent().parent().click();
        }
        else if($(this).hasClass(ActionInput.active.SUPER_INPUT))
        {
            IsSuperChecked = $(this).is(':checked');
            $(CategorySelected + ' .' + ActionInput.active.ANY_REGULAR_INPUT).each(function(){
                getTextHolder = $(this).parent().siblings().filter(':last-child');
                
                if(IsSuperChecked)
                {
                    $(this).prop('checked',true);
                    getTextHolder.addClass(ActionActive);
                }
                else
                {
                    $(this).prop('checked',false);
                    getTextHolder.removeClass(ActionActive);
                }
                SetActionColorStatusFix(getTextHolder);
            });//attr('checked','').click();
        }
        else if($(this).hasClass(ActionInput.exists.SUPER_INPUT))
        {
            IsSuperChecked = $(this).is(':checked');
            $(CategorySelected + ' .' + ActionInput.exists.ANY_REGULAR_INPUT).each(function(){
                getTextHolder = $(this).parent().siblings().filter(':last-child');
                
                if(IsSuperChecked)
                {
                    $(this).prop('checked',true);
                    getTextHolder.addClass(ActionExists);
                }
                else
                {
                    $(this).prop('checked',false);
                    getTextHolder.removeClass(ActionExists);
                }
                SetActionColorStatusFix(getTextHolder);
            });//attr('checked','').click();
            
            if(!IsSuperChecked)
            {
                //$(CategorySelected + ' .Action-Item .Action-Category-Selected').addClass('Selected-Save');
                
                
                $(CategorySelected + ' .' + ActionInput.display.SUPER_INPUT).prop('checked', true).click();
                $(CategorySelected + ' .' + ActionInput.active.SUPER_INPUT).prop('checked', true).click();
                $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).prop('checked', true).click();/*.each(function(){
                   $(this).prop('checked', false);
                   SetActionColorStatusFix($(this).parent().siblings().filter(':last-child'));
                });*/
                //$(CategorySelected + ' .Action-Item:last-child td:last-child').removeClass('Action-Category-Selected');
                
                //$('.Selection-Info').hide(1);
                //alert("sadsa");
                
            }
        }
        //alert($(this).parent().siblings().filter(':last-child').attr('class'));
        
        var flagNoExists = !$(this).hasClass(ActionInput.exists.ANY_INPUT);
        
        if(flagNoExists)
            SetActionColorStatusFix(getTextHolder);
        
        
        var AllSelectedExists = isAllActionsSelectedBy('exists');
        
        SetStatusAction($(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT), '', AllSelectedExists, flagNoExists);
        
        if(AllSelectedExists)
            $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).parent().siblings().filter(':last-child').addClass(ActionExists);
        else
            $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).parent().siblings().filter(':last-child').removeClass(ActionExists);
        
        SetStatusAction($(CategorySelected + ' .' + ActionInput.display.SUPER_INPUT), ActionDisplay, isAllActionsSelectedBy('display'), flagNoExists);
        SetStatusAction($(CategorySelected + ' .' + ActionInput.active.SUPER_INPUT), ActionActive, isAllActionsSelectedBy('active'), flagNoExists);
        
        
        
        
        //SetActionColorStatus($(this).parent());
        
        /*if(!IsLastActionSelected)
            $(CategorySelected + ' .Action-Item:last-child td:last-child').removeClass('Action-Category-Selected');
            
        if(ExistsSuper == 1)
            $('.Selection-Info').hide(1);
            
        /*$('.Selection-Info').slideUp();
        if($(CategorySelected + ' .Action-Item .Selected-Save:first').length)
            $(CategorySelected + ' .Action-Item .Selected-Save:first').removeClass('Selected-Save').click();
        /*else
            $(CategorySelected + ' .Action-Item:first').click();*/
        //alert("asd");
        setTimeout(function() {
          $(CategorySelected + ' .Action-Item').removeClass('Save-Selected');
        }, 0);
   });
   
   
   $(document).on('click', CategorySelected + ' input[type=checkbox].checkbox_regular', function(){//.live('click',function(){
   //$('.Panel-Roles-Category-Actions-Selected input[type=checkbox].checkbox_regular').on('click', function(){//.live('click', function(){
        
        //alert("test");
        var getTextHolder = $(this).parent().siblings().filter(':last-child');
        
        //Styling text action by checboxes
        if($(this).hasClass(ActionInput.display.ANY_INPUT))
        {
            if($(this).is(':checked'))
                getTextHolder.addClass(ActionDisplay);
            else
                getTextHolder.removeClass(ActionDisplay);
        }
        else if($(this).hasClass(ActionInput.active.ANY_INPUT))
        {
            if($(this).is(':checked'))
                getTextHolder.addClass(ActionActive);
            else
                getTextHolder.removeClass(ActionActive);
        }
        else if($(this).hasClass(ActionInput.exists.ANY_INPUT))
        {
            if($(this).is(':checked'))
                getTextHolder.addClass(ActionExists);
            else
            {
                getTextHolder.removeClass(ActionExists);
                $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).prop('checked',false);
            }
                
        }
            
        /*
        if($(this).hasClass(ActionInput.display.SUPER_INPUT))
        {
            if($(this).is(':checked'))
                $('.' + ActionInput.display.ANY_REGULAR_INPUT).attr('checked','').click();
            else
                $('.' + ActionInput.display.ANY_REGULAR_INPUT).attr('checked','checked').click();
        }
        
        if($(this).hasClass(ActionInput.active.SUPER_INPUT))
        {
            if($(this).is(':checked'))
                $('.' + ActionInput.active.ANY_REGULAR_INPUT).attr('checked','').click();
            else
                $('.' + ActionInput.active.ANY_REGULAR_INPUT).attr('checked','checked').click();
                
        }
        
        if($(this).hasClass(ActionInput.exists.SUPER_INPUT))
        {
            if($(this).is(':checked'))
                $('.' + ActionInput.exists.ANY_REGULAR_INPUT).attr('checked','').click();
            else
                $('.' + ActionInput.exists.ANY_REGULAR_INPUT).attr('checked','checked').click();
        }
        */
        
        var flagNoExists = !$(this).hasClass(ActionInput.exists.ANY_INPUT);
        
        if(flagNoExists)
            SetActionColorStatusFix(getTextHolder);
        
        
        var AllSelectedExists = isAllActionsSelectedBy('exists');
        
        SetStatusAction($(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT), '', AllSelectedExists, flagNoExists);
        
        if(AllSelectedExists)
            $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).parent().siblings().filter(':last-child').addClass(ActionExists);
        else
        {
            $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).parent().siblings().filter(':last-child').removeClass(ActionExists);
            $(CategorySelected + ' .' + ActionInput.exists.SUPER_INPUT).prop('checked',false);
            //alert("asd");
        }
            
        
        SetStatusAction($(CategorySelected + ' .' + ActionInput.display.SUPER_INPUT), ActionDisplay, isAllActionsSelectedBy('display'), flagNoExists);
        SetStatusAction($(CategorySelected + ' .' + ActionInput.active.SUPER_INPUT), ActionActive, isAllActionsSelectedBy('active'), flagNoExists);
        
         
        if(!flagNoExists)
        {
            //alert("yes");
            if($(this).is(':checked'))
                getTextHolder.addClass(ActionExists);
            else
            {
                onUnCheckExists($(this).parent().parent());
                /*
                var allCheck;
                $(this).parent().siblings().not(':last-child').find('input').each(function(){
                   $(this).prop('checked', false);
                   allCheck = $(this).attr('class').split(' ')[0] + '_all';
                   //$('.' + allCheck).prop('checked', false);
                   //SetActionColorStatus($('.' + allCheck).parent().parent());
                   //alert($('.' + allCheck).parent().parent().html());
                });
                //SetActionColorStatusFix(getTextHolder);
                //prop('checked', false);//not('.Side-Menu-Action-Item-Text').find('input:checked').click();
                $(this).prop('checked',false);
                //SetActionColorStatusFix(getTextHolder);
                getTextHolder.removeClass(ActionExists);
                //SetActionColorStatus($(this).parent().parent());*/
            }   
        }
        
   });
   
   function onUnCheckExists(ActionRow)
   {
        ActionRow.find('.checkbox_display').prop('checked', false);
        ActionRow.find('.checkbox_active').prop('checked', false);
        ActionRow.children(':last-child').removeClass(ActionDisplay + ' ' + ActionActive + ' text-shadow').css('color','');
        
        var ActionAllRow = $(CategorySelected + ' .checkbox_super').parent().parent();
        
        ActionAllRow.find('.checkbox_display').prop('checked', false);
        ActionAllRow.find('.checkbox_active').prop('checked', false);
        ActionAllRow.children(':last-child').removeClass(ActionDisplay + ' ' + ActionActive + ' text-shadow').css('color','');
        
   } 
    
});