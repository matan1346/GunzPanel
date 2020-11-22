
$(document).ready(function(){
    
    function SetSelectionOptions(){
        $(".Select-Options-Pick").multiselect({
           multiple: false,
           header: false,
           //height: 'auto',
           minWidth: 210,
           maxHeight: 130,
           noneSelectedText: NONE_SELECTED,
           selectedList: 1
        });
    }
    //SetSelectionOptions();
    
    $('.Translation-Box-Container table tbody tr > *:not(:last-child)').click(function(){
        //alert('hey');
        $(this).parent('tr').find('input').focus();
    });
    
    $('.Slider-Options-Button').click(function(){
        //alert("sad");
         //$(this).parents('.Selection-Table-Raw').siblings('.Option-Table-Raw[data-id='+$(this).parents('.Selection-Table-Raw').attr('data-id')+']').show();
        
        if($(this).hasClass('Slider-Open'))
        {
            $(this).removeClass('Slider-Open').text(ACTIONS_OPEN);
            
            $('.Option-Table-Holder[data-id='+$(this).parents('.Selection-Table-Raw').attr('data-id')+']').removeClass("open_status").slideUp('slow');
        }
        else
        {
            $('.open_status').slideUp('slow').removeClass('open_status');
            $('.Slider-Open').text(ACTIONS_OPEN).removeClass('Slider-Open');
             $(this).addClass('Slider-Open').text(ACTIONS_CLOSE);
            $('.Option-Table-Holder[data-id='+$(this).parents('.Selection-Table-Raw').attr('data-id')+']').addClass('open_status').slideDown('slow');
            
            //$(this).addClass('Slider-Open').text('Close').parents('.Selection-Table-Raw').siblings('.Option-Table-Raw[data-id='+$(this).parents('.Selection-Table-Raw').attr('data-id')+']').children().slideDown('slow');
        }
        //$(this).parents('.Selection-Table-Raw').siblings('.Option-Table-Raw[data-id='+$(this).parents('.Selection-Table-Raw').attr('data-id')+']').slideDown();
    
        //$(this).parents('.Selection-Table-Raw').siblings('.Option-Table-Raw[data-id='+$(this).parents('.Selection-Table-Raw').attr('data-id')+']').slideUp();
    });
    
    $('.Select-Options-Pick').change(function(){
       alert("asd");
       
       
    });
    
    $('.Translation-Submit').click(function(){
        //alert("submit");
        //console.log($(this).parent().siblings('.Translate-Box-Table'));
        var TranslateBoxTable = $(this).parent().siblings('.Translate-Box-Table');
        
        
        saveTranslation(
            TranslateBoxTable.parent().attr('data-translation-id'),
            prepareTanslationSubmit(TranslateBoxTable)
        );
    });
    
    
    $('.Translation-Reset').click(function(){
        //alert("cancel");
        $(this).parent().siblings('table').find('form input[type=reset]').click();
        //$(this).parent().siblings('table').each('form').validate().resetForm();
    });
    
    $('.Tranlate-Button-OPEN').click(function(){
        
        //alert("clicked!");
        //OpenTranlationBox();
        //alert($(this).parents('tr').attr('data-translation-id'));
        var translate_id = $(this).parents('tr').attr('data-translation-id');
        if($('.Translation-Box-Container[data-translation-id='+translate_id+']').hasClass('Translation-Open'))
        {
            $('.Translation-Box-Container[data-translation-id='+translate_id+']').removeClass('Translation-Open').slideUp('slow');
            return false;
        }
            
        
        getTranslationData(translate_id);
        
    });
    
    function prepareTanslationSubmit(data_holder)
    {
        //console.log(data_holder);
        //alert(data_holder.find('form').length);
        var LangUpdate = {};
        $.each(data_holder.find('form'), function(item){
            
            LangUpdate[$(this).find('input[name=translate_target]').val()] = $(this).find('input[name=translate]').val();
            
            //alert("translate_target: " + $(this).find('input[name=translate_target]').val() );
            //alert("translate: " + $(this).find('input[name=translate]').val() );
            /*$.each($(this).find('input'), function(item2){
                alert($(this).attr('name')+": " + $(this).val() );
            })*/
            
        //}) data_holder.children('form').each(function(){
           //alert("hey"); 
        });
        
        console.log(LangUpdate);
        //alert(LangUpdate);
        
        return LangUpdate;
    }
    
    function setNewDefaultValue(translate_id)
    {
        //$('.Translation-Box-Container[data-translation-id='+translation_id+']');
        $.each($('.Translation-Box-Container[data-translation-id='+translate_id+']').find('input'), function(){
            alert("input");
            if (!$(this).data('default')) {
                alert("sad");
                $(this).attr('value', $(this).val());
                //jQuery.data($(this),'default', $(this).val());
            } 
        });
    }
    
    function saveTranslation(translate_id, data_save)
    {
        //alert(data_save);
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_set_translation.php',
            data: {id_trans: translate_id, lang_save: data_save},
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
                {
                    //alert("success");
                    console.log(data);
                    
                    setNewDefaultValue(translate_id);
                    
                    //alert("done!");
                    
                    //CreateTranlationBox(translate_id,'hey its me',data.Data);
                    //alert("asdas");
                }
                    
                
            },
            complete: function(data){
                console.log(data);
            }
        });
        
        
    }
    
    function CreateTranlationBox(translate_id ,title, tranl_data)
    {
        //alert("asdsadasdasda");
        //alert(SELECTION_OPTIONS);
        //console.log("hey ");
        //console.log(SELECTION_OPTIONS);
        //console.log("hey2");
        var str = '';
        /*str += '<div class="Translation-Box-Container box-shadow"><h1>Translation <span>'+title+'</span></h1>';
        str += '<table class="Search-Result-Table">';
        str +=              '<thead>';
        str +=                  '<tr>';
        str +=                      '<th>#</th>';
        str +=                      '<th>Country</th>';
        str +=                      '<th>Language</th>';
        str +=                      '<th>Text</th>';
        str +=                  '</tr>';
        str +=              '</thead>';
        str +=              '<tbody>';*/
        for(var i = 0;i < Object.keys(tranl_data).length;i++)
        {
            str +=              '<tr>';
            str +=                  '<td>'+(i+1)+'</td>';
            str +=                  '<td><img alt="'+tranl_data[i]['lang']['en']['Country']+'" src="public/img/flags/'+tranl_data[i]['type']+'.png" /></td>';
            str +=                  '<td>'+tranl_data[i]['lang']['en']['Language']+'<br />(' + tranl_data[i]['lang'][tranl_data[i]['type']]['Language'] + ')</td>';
            str +=                  '<td><form><input type="hidden" name="translate_target" value="'+tranl_data[i]['type']+'"><input type="text" name="translate" value="'+tranl_data[i]['translatedTarget'][0]+'" data-last-text="'+tranl_data[i]['translatedTarget'][0]+'" placeholder="Translated Text" autocomplete="off"><br/><input type="reset" value="Reset"/></form></td>';
            str +=              '</tr>';
        }
        /*str +=              '</tbody>';
        str +=          '</table>';
        str +=          '<div class="Translation-Box-Buttons">';
        str +=              '<div class="Translation-Box-Button-Item Translation-Cancel">Cancel</div>';
        str +=              '<div class="Translation-Box-Button-Item Translation-Submit">Submit</div>';
        str +=          '</div>';
        str +=      '</div>';*/
       // str += '</div>';
        //$('#Selection-View-Container').append(str);
        
        $('.Translation-Box-Container[data-translation-id='+translate_id+'] tbody').html(str);
        $('.Translation-Box-Container[data-translation-id='+translate_id+']').addClass('Translation-Open').slideDown('slow');
        //console.log(str);
        
    }
    
    function getTranslationData(translate_id)
    {
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_get_translation.php',
            data: {id_trans: translate_id},
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
                {
                    //alert("success");
                    console.log(data);
                    
                    CreateTranlationBox(translate_id,'hey its me',data.Data);
                    //alert("asdas");
                }
                    
                
            },
            complete: function(data){
                console.log(data);
            }
        });
    }
    
});