
$(document).ready(function(){
   
   //alert("asfa");
   //$(document).on('click', CategorySelected + ' input[type=checkbox].checkbox_super', function(){//.live('click',function(){
   $('#System-Messages-Data').on('click', '.System-Messages-Item', function(){
    
        
        alert($(this).text());
   });
   
   $('#System-Messages-Title').click(function(){
        if($('#System-Messages-Data').css('display') == 'none')
            $('#System-Messages-Data').slideDown();
        else
            $('#System-Messages-Data').slideUp();
   });
   
    
});