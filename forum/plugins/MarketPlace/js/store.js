
jQuery(document).ready(function($){
    $.fn.popup = function(options) {
        // IE7 or less gets no popups because they're jerks
        if ($.browser.msie && parseInt($.browser.version, 10) < 8)
            return false;

        // Merge the two settings arrays into a central data store
        var settings = $.extend({}, $.popup.settings, options);
        var sender = this;

        this.live('click', function() {
            settings.sender = this;
            $.extend(settings, { popupType: $(this).attr('popupType') });

            $.popup.init(settings);
            if (!settings.confirm)
                $.popup.loading(settings);

            var target = $.popup.findTarget(settings);
            if (settings.confirm) {
                // Bind to the "Okay" button click
                $('#'+settings.popupId+' .Okay').focus().click(function() {
                    if($(sender).is('input')){
                        var TransID = $(sender).closest('form').next('.TransID').val();
                        saveMeta(TransID)
                        $(sender).closest('form').trigger('submit');
                    }else  if (settings.followConfirm) {
                        // follow the target
                        document.location = target;
                    } else {
                        // request the target via ajax
                        $.ajax(
                            {
                                type: "GET",
                                url: target,
                                data: {'DeliveryType' : settings.deliveryType, 'DeliveryMethod' : 'JSON'},
                                dataType: 'json',
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    $.popup({}, XMLHttpRequest.responseText);
                                },
                                success: function(json) {
                                    json = $.postParseJson(json);

                                    $.popup.close(settings);
                                    settings.afterConfirm(json, settings.sender);
                                    gdn.inform(json);
                                    if (json.RedirectUrl)
                                        setTimeout(function() { document.location.replace(json.RedirectUrl); }, 300);
                                }
                            }
                        );
                    }
                });
            } else {
                if (target) {
                    $.ajax({
                        type: 'GET',
                        url: target,
                        data: {
                            'DeliveryType': settings.deliveryType 
                        },
                        error: function(request, textStatus, errorThrown) {
                            $.popup.reveal(settings, request.responseText);
                        },
                        success: function(data) {
                            $.popup.reveal(settings, data);
                        }
                    });
                    //          $.get(target, {'DeliveryType': settings.deliveryType}, function(data) {
                    //            $.popup.reveal(settings, data)
                    //          });
                }
            }
            return false;
        });
      
        this.mouseover(function() {
            settings.sender = this;
            if ($.popup.findTarget(settings))
                $(this).addClass(settings.mouseoverClass);
        });

        this.mouseout(function() {
            settings.sender = this;
            if ($.popup.findTarget(settings))
                $(this).removeClass(settings.mouseoverClass);
        });

        return this;
    }
   
    function attachEvents(){
        $('.ProductPayButton form').submit(function(){
            $(this).find('.se').each(function(){
                $(this).val($.base64Decode($(this).val()));
                $(this).removeClass('se');
            });
           
            return true;
        });
        
        $('.ProductPayButton form').each(function(){
            $(this).find('input').popup({'confirm' : true});
        });
    }

    function saveMeta(){
        var pst = {
            'Form/TransientKey':gdn.definition('TransientKey'),
            'TransientKey':gdn.definition('TransientKey'),
            'Form/TransactionID':gdn.definition('TransactionID'),
            'TransactionID':gdn.definition('TransactionID')
        };
    
        $('.MetaValue').each(function(i,v){
            var mv = $(v).prev('.MetaIndex').attr('id').split('_')[2];
            pst['Form/MetaName['+i+']'] = pst['MetaName['+i+']'] = mv;
            pst['Form/MetaValue['+i+']'] = pst['MetaValue['+i+']'] = $(v).val()?$(v).val():$(v).text();
        
        });
    
        $('.TransID').each(function(i,v){
            pst['Form/'+$(v).attr('name')] = pst[$(v).attr('name')] = $(v).val();
        });
    
    
        $.post(gdn.definition('MetaTrans'),pst,function(result){
            switch(result.status){
                case 'success':
                    $('.ProductPayButton').html(result.buttons);
                    attachEvents();
                    break;
                case 'error':
                    $('.Message.Errors').replaceWith(result.errormsg);
                    break;
            }
        },'json');

    }
    
    function loadEditMeta(){
        var editMeta = 'span.MetaValue.EditMeta';
        $('.ProductMeta').each(function(){
            $(this).find('.SaveMeta').remove();
            $(this).find('input.MetaValue.EditMeta').each(function(){
                $(this).before('<span class="MetaValue EditMeta">'+$(this).val()+'</span>');
            });
            $(this).find('input.MetaValue.EditMeta').remove();
        });
        $(editMeta).each(function(){
            var a = $('<span class="MetaEdit"> <a href="#" class="Button SmallButton">'+gdn.definition('EditWord','edit')+'</a></span>').click(function(){
                var em = $(this).prev('.MetaValue.EditMeta');
    
                em.replaceWith('<input type="text" class="MetaValue EditMeta InputBox SmallInput" value="'+em.text().replace(/"/g, '&quot;')
+'" /><span class="SaveMeta"> </span>')
            
                var sm = $('<a href="#" class="Button SmallButton">'+gdn.definition('SaveWord','save')+'</a>');
                sm.click(function(){
                    saveMeta();
                    loadEditMeta();
                    $('.ProductPayButton').show();
                });
                $('.SaveMeta').append(sm);
                $(this).remove();
                $('.ProductPayButton').hide();
            });
            
            $(this).after(a);
        
        });
    }
    
    loadEditMeta();
    attachEvents();
    
});
