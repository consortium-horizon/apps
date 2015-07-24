jQuery(document).ready(function(){
    var hiddenRoles = $.parseJSON(gdn.definition('HiddenRoles'));
    $("#RoleTable tr#0").append('<th>'+gdn.definition("Hidden")+'</th>');
    $.each(hiddenRoles,function(i,v){
        var hr = $("<input />").attr({
            "type":"checkbox",
            "class":"HiddenRole",
            "name":"HiddenRole_"+i,
            "style":"vertical-align:middle;"
        });
        if(v.hidden==1){
            hr.attr("checked","checked");
            if(v.name){
                $("#RoleTable tr#"+i+' td:first-child strong').text(v.name);
            }
        }
        var hiddenRoleOps = $("<td class=\"HiddenRoleOps\" />");
        hiddenRoleOps.append(hr);
        $("#RoleTable tr#"+i).append(hiddenRoleOps);
    });
    $('.HiddenRole').live('click',function(){
        var on = $(this).is(':checked')?1:0;
        if(on && !confirm(gdn.definition('Hide','Caution! Are you sure you want to hide this role?'))){
            $(this).removeAttr('checked');
            return;
        }
        if(!on && !confirm(gdn.definition('Unhide','Caution! Are you sure you want to unhide this role?'))){
            $(this).attr('checked','checked');
            return;
        }
        var c = $(this).attr('name').split('_');
        var id = c[1];
        
        $.ajax({
            type: "POST",
            url: gdn.url('/settings/hiddenrole/'+id+'/'+on),
            data: 'DeliveryType=BOOL&DeliveryMethod=JSON',
            dataType: 'json',         
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $.popup({}, XMLHttpRequest.responseText);
            },
            success: function(json) {
                
            }
        });
    });
});
