
$(document).ready(function(){
    
    $(document).on('click', '.Result-Get-Search-Info', function(){
    /*$('.Result-Search-Account').click(function(){*/
        /*if(new RegExp ("^[0-9]\:[a-zA-z0-9_]$").test($(this).attr('href')))
            alert("success");
        else
            alert($(this).attr('href'));*/
        
        var StrSplit = $(this).attr('href').split(':'),
            urlToSend = {
                CategoryName: $(this).attr('data-category'),
                ID: StrSplit[1]
            };
        
        GetSearchInfo(urlToSend);
    });
    
    getSearchInfo = true;
    
    function GetSearchInfo(urlToSend)
    {
        if(!getSearchInfo)
            return false;
        getSearchInfo = false;
        
        //console.log(urlToSend);
        
        $.ajax({
            type: "POST",
            url: 'public/ajax/ajax_get_search_info.php',
            data: urlToSend,
            //contentType: false,"application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr){
                /*$('#lightOff').css('z-index', 9999999999);
                $('#lightOff div').css('display', 'block');
                */
            },
            success: function(data) {
                console.log(data);
                
                CreateSearchInfo(data, true);
                
                alert("get search info");
                
                getSearchInfo = true;
            },
            complete: function(data){
                console.log(data);
            }
        });
    }
    
    
    /*
    <div class="accordion">
      <h3>Account Details</h3>
      <div>
        <table class="Search-Result-Table">
            <thead>
                <th>AID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Grade</th>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>matanasus</td>
                    <td>123123</td>
                    <td>Admin</td>
                </tr>
            </tbody>
        </table>
        <table class="Search-Result-Table">
            <thead>
                <th>Name</th>
                <th>Age</th>
                <th>Email</th>
                <th>Coins</th>
            </thead>
            <tbody>
                <tr>
                    <td>Matan</td>
                    <td>21</td>
                    <td>text@example.com</td>
                    <td>223</td>
                </tr>
            </tbody>
        </table>
        <table class="Search-Result-Table">
            <thead>
                <th>IP Address</th>
                <th>MAC Address</th>
            </thead>
            <tbody>
                <tr>
                    <td>127.0.0.1</td>
                    <td>MC-00-45-34-87-9B</td>
                </tr>
            </tbody>
        </table>
        <table class="Search-Result-Table">
            <thead>
                <th>Regsiter Date</th>
                <th>Last Login</th>
            </thead>
            <tbody>
                <tr>
                    <td>13 April 2014</td>
                    <td>24 May 2015</td>
                </tr>
            </tbody>
        </table>
        <p>
        Hey, i am a text!
        </p>
      </div>
      <h3>Special Items</h3>
      <div>
        <p>
        Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet
        purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor
        velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In
        suscipit faucibus urna.
        </p>
      </div>
      <h3>Storage Items</h3>
      <div>
        <p>
        Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.
        Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero
        ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis
        lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.
        </p>
        <ul>
          <li>List item one</li>
          <li>List item two</li>
          <li>List item three</li>
        </ul>
      </div>
      <h3>User History Logs</h3>
      <div>
        <p>
        Cras dictum. Pellentesque habitant morbi tristique senectus et netus
        et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in
        faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia
        mauris vel est.
        </p>
        <p>
        Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus.
        Class aptent taciti sociosqu ad litora torquent per conubia nostra, per
        inceptos himenaeos.
        </p>
      </div>
    </div>
    */
    
    
    search_info_dialog_id = 1;
   
   function CreateSearchInfo(InfoData, flagMini)
   {
    //alert(flagMini);
        var err_dialog = $('<div id="search_info_dialog_'+ search_info_dialog_id +'" title="Search Info: UserID `' + InfoData.ResultData.UserID + '`"><div class="search_info_dialog_inside_title">Results: '+ (flagMini ? 'Success' : 'Failure') +'</div></div>');
        
        //console.log('asdas');
        //console.log(errorMessages);
        /*jQuery.each(FormMessages, function(i, FormKey) {
            //aler("asdas");
            //console.log(ErrorKey['Translated']);
            err_dialog.append('<h1>' + FormKey['Translated'] + '</h1><ul id="search_info_'+ search_info_dialog_id +'_message_'+ i +'"></ul>')
            jQuery.each(FormKey['List'], function(j, val) {
                err_dialog.children('#search_info_dialog_'+ search_info_dialog_id +'_message_' + i).append('<li>' + val + '</li>');
            });
        });*/
        
        var addData,
            ResultData = InfoData.ResultData;
        
        
        
        addData = ['<div class="accordion">',
      '<h3>Account Details</h3>',
      '<div>',
        '<table class="Search-Result-Table">',
            '<thead>',
                '<th>AID</th>',
                '<th>Username</th>',
                '<th>Password</th>',
                '<th>Grade</th>',
            '</thead>',
            '<tbody>',
                '<tr>',
                    '<td>' + ResultData.AID + '</td>',
                    '<td class="text_copy">' + ResultData.UserID + '</td>',
                    '<td>' + ResultData.Password + '</td>',
                    '<td>' + ResultData.UGradeID + '</td>',
                '</tr>',
            '</tbody>',
        '</table>',
        '<table class="Search-Result-Table">',
            '<thead>',
                '<th>Name</th>',
                '<th>Age</th>',
                '<th>Email</th>',
                '<th>Coins</th>',
            '</thead>',
            '<tbody>',
                '<tr>',
                    '<td>' + ResultData.Name + '</td>',
                    '<td>' + ResultData.Age + '</td>',
                    '<td>' + ResultData.Email + '</td>',
                    '<td>' + ResultData.Coins + '</td>',
                '</tr>',
            '</tbody>',
        '</table>',
        '<table class="Search-Result-Table">',
            '<thead>',
                '<th>IP Address</th>',
                '<th>MAC Address</th>',
            '</thead>',
            '<tbody>',
                '<tr>',
                    '<td>' + ResultData.LastIP + '</td>',
                    '<td>' + ResultData.MAC + '</td>',
                '</tr>',
            '</tbody>',
        '</table>',
        '<table class="Search-Result-Table">',
            '<thead>',
                '<th>Regsiter Date</th>',
                '<th>Last Login</th>',
            '</thead>',
            "<tbody>",
                '<tr>',
                    '<td>' + ResultData.RegDate + '</td>',
                    '<td>' + ResultData.LastConnDate + '</td>',
                '</tr>',
            '</tbody>',
        '</table>',
        '<p>Hey, i am a text!</p>',
      '</div>',
      '<h3>Special Items</h3>',
      '<div>',
        '<p>Test 0</p>',
      '</div>',
      '<h3>Storage Items</h3>',
      '<div>',
        '<p>Test 1</p>',
        '<ul>',
          '<li>List item one</li>',
          '<li>List item two</li>',
          '<li>List item three</li>',
        '</ul>',
      '</div>',
      '<h3>User History Logs</h3>',
      '<div>',
        '<p>Test 2</p>',
      '</div>',
    '</div>'].join('\n');
        
        /*
        addData ='<table class="Search-Result-Table">',
                    '<thead style="background-color: #20475c;color: white;">',
                        '<tr>',
                            '<th>UserID</th>',
                            '<th>AID</th>',
                            '<th>Password</th>',
                            '<th>Name</th>',
                            '<th>Coins</th>',
                            '<th>IP</th>',
                            '<th>MacAddress</th>',
                            '<th>Login</th>',
                        '</tr>',
                    '</thead>',
                    '<tbody style="background-color: #31617a;color: white;">',
                        '<tr>',
                            '<td>matanasus</td>',
                            '<td>1</td>',
                            '<td>123</td>',
                            '<td>MATAN</td>',
                            '<td>567</td>',
                            '<td>127.0.0.1</td>',
                            '<td>12-SDAS--12-12SD</td>',
                            '<td>APRIL 2015</td>',
                        '</tr>',
                    '</tbody>',
                '</table>';*/
        err_dialog.append(addData);
        //err_dialog.append('<table class="Search-Result-Table"><thead style="background-color: #20475c;color: white;"><tr><th>UserID</th><th>AID</th><th>Password</th><th>Name</th><th>Coins</th><th>IP</th><th>MacAddress</th><th>Login</th></tr></thead><tbody style="background-color: #31617a;color: white;"><tr><td>matanasus</td><td>1</td><td>123</td><td>MATAN</td><td>567</td><td>127.0.0.1</td><td>12-SDAS--12-12SD</td><td>APRIL 2015</td></tr></tbody></table>'); 
        
        err_dialog.appendTo('body');
        //dialog_window
        $('#search_info_dialog_' + search_info_dialog_id).dialog({
            z_index: 99999,
            draggable: true,
            resizable: false,
            minWidth: 800,
            /*maxWidth: 800,*/
            dialogClass: 'search_info_dialog',
            /*position: {
                my: 'left',
                at: 'top'
            },*/
            /*buttons: {
                Ok: function(){
                    $(this).dialog('close');
                }
            }*/
        });
        
        $('.accordion').accordion({
                heightStyle: "content"
            });
        
        if(!flagMini)
            $('.search_info_dialog[aria-describedby=search_info_dialog_' + search_info_dialog_id + ']').find('.ui-dialog-titlebar-minimize').hide();
        
        
        //$('.text_copy').copy_text();
        
        $('.Search-Result-Table tbody td').copy_text();
        
        console.log(InfoData);
        
        search_info_dialog_id++;
        //err_dialog.append();
   }
});

