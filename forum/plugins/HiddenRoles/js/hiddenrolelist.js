jQuery(document).ready(function(){
    var hiddenRoles = $.parseJSON(gdn.definition('HiddenRoles'));
    $('.User .CheckBoxList').livequery(function(){
        $('.User .CheckBoxList li label').each(function(){
            var id = $(this).find('input:checkbox').val();
            if(id in hiddenRoles){
                var v = hiddenRoles[id];
                if(v.hidden==1){
                    var txt = $(this).contents().filter(function(){return this.nodeType === 3});
                    txt.replaceWith(document.createTextNode(v.name));
                }
            }
        });

    });

    if($('table#Users').length){
        $('table#Users tr td:nth-child(3) a').each(function(){
            var that = this;
            $.each(hiddenRoles,function(i,v){
                if(v.hidden==1){
                    if($(that).text()==v.id){
                        $(that).text(v.name);
                    }
                }
            });
        });
    }

    if($('.CategoryPermissions').length){
        $('.CategoryPermissions .CheckBoxGrid tr th a').each(function(){
            var that = this;
            $.each(hiddenRoles,function(i,v){
                if(v.hidden==1){
                    if($(that).text()==v.id){
                        $(that).text(v.name);
                    }
                }
            });
        });
    }
});
