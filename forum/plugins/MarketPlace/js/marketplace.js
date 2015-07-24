jQuery(document).ready(function($){
    
    if(window.location.pathname.toLowerCase().indexOf('marketplace/listings')==-1){
        var tabs = $('h2').map(function(){
            return '<li><input class="SmallButton" value="'+$(this).text()+'" type="button" /></li>';
        }).get().join('');
    
    
        if($('.Errors').length){
            $('form, .ProductListings').not($('.Errors:first').closest('form, .ProductListings')).hide();
        }else{
            $('form, .ProductListings').not(':first').hide();
        }
        $('#Content div:first').after('<div class="Tabs"><ul>'+tabs+'</ul></div>');
        
        
        $('.Tabs .SmallButton').click(function(){
            $('form, .ProductListings').hide();
            $('h2:contains('+$(this).val()+')').closest('form, .ProductListings').show();
        });
    }
    
    var types = $.parseJSON(gdn.definition('ProductTypes',[]));
    var currencies = $.parseJSON(gdn.definition('Currencies',[]));
    
    function PriceLine(){
        var priceline = $('.PriceLine:last').clone();
        $('.PriceLine .Currency').unbind('change');
        $('.PriceLine:last .Currency').bind('change',function(){
            //if(!$(priceline).children('option[value!=""]').length) return;
            $('.PriceLine:last').after(priceline.clone());
            $('.PriceLine:last .Currency option[value="'+$(this).val()+'"]').remove();
                if($(this).val()!=""){
                    $(this).unbind('change');
                    priceline = $('.PriceLine:last').clone();
                    PriceLine();
                }
            var last = $('.PriceLine:last .Currency');
            while(last.length){
                $('.PriceLine .Currency').not(last).each(function(){
                    $(this).children('option[value="'+$(last).val()+'"]').remove();
                });
                last=$(last).parent('.PriceLine').prev('.PriceLine').children('.Currency');
            }
        });
    }
    
    function MetaLine(){
        var metaline = $('.MetaLine:last').clone();
        $('.MetaLine .MetaValue').unbind('keyup');
        $('.MetaLine:last .MetaValue').bind('keyup',function(){
            $('.MetaLine:last').after(metaline.clone());
            $(this).unbind('keyup');
            MetaLine();
        });
    }
    
    
    $('#Form_ProductType').change(function(){
        
        $('.PriceLine:last .Ammount').val('');
        $('.PriceLine').not(':last').remove();
        $('.MetaLine').not(':last').remove();
        $('.MetaLine input').val('');
        $('.MetaLine .MetaName').removeAttr('readonly');
        $('.MetaLine .MetaValue').replaceWith('<input type="text" class="InputBox MetaValue" name="Form/MetaValue[]"/>');
        $('.MetaLine:last .MetaAny').attr('readonly','readonly');

        $.each(currencies,function(i,v){
            if(!$('.PriceLine:last .Currency option[value="'+v+'"]').length)
                $('.PriceLine:last .Currency').append($('<option value="'+v+'" >'+v+'</option>'));    

        });
        if($(this).val() in types){
            $.each($.makeArray(types[$(this).val()]['Options']['Meta']),function(i,v){
                var metaline = $('.MetaLine:last').clone();
                $('.MetaLine:last .MetaName').val(v);
                
                if($.inArray(v,$.makeArray(types[$('#Form_ProductType').val()]['Options']['RequiredMeta']))>-1)
                    $('.MetaLine:last .MetaName').attr('readonly','readonly');
                if($.inArray(v,$.makeArray(types[$('#Form_ProductType').val()]['Options']['VariableMeta']))>-1){
                    $('.MetaLine:last .MetaAny').removeAttr('readonly');
                    $('.MetaLine:last .MetaAny').css({'visibility':'visible'});
                    $('.MetaLine:last .MetaAny').prevAll('span:first').css({'visibility':'visible'});
                }
                if($.isArray(types[$('#Form_ProductType').val()]['Options']['ValidateMeta'][v])){ 
                        var selectenum = $.map(types[$('#Form_ProductType').val()]['Options']['ValidateMeta'][v],function(ev, ei){
                           return '<option value="'+ev+'">'+ev+'</option>';
                        }).join('');
                        selectenum = $('<select class="MetaValue" name="Form/MetaValue[]"></select>').html(selectenum);
                        $('.MetaLine:last .MetaValue').replaceWith(selectenum);
                }
                $('.MetaLine:last').after(metaline);
            });
        }
        PriceLine();
        MetaLine();
        
    });
    
    $('#Form_ProductType').trigger('change');
    
    $('.EditProduct').mousedown(function(){
        var slug = $(this).attr('id').split('_')[1];
            $.ajax({
                url: gdn.url(gdn.definition('ItemURL')+'/'+slug),
                data: 'DeliveryType=DATA&DeliveryMethod=JSON',
                dataType: 'json',  
                success: function(data) {
                    $('.Tabs .SmallButton[value="Add/Edit Product"]').trigger('click');
                    var product = data.MarketProduct;
                    $.each(product,function(i,v){
                        $('#AddEditProduct #Form_'+i).val(v);
                    });
                    $('.MetaLine .MetaName').removeAttr('readonly');
                    $('#Form_ProductType').trigger('change');
                    $.each(product.PriceDenominations,function(i,v){
                        $('.PriceLine:last .Amount').val(v);
                        $('.PriceLine:last .Currency').val(i);
                        PriceLine();
                        $('.PriceLine:last .Currency').trigger('change');
                    });
                    $('GatewayDrop').val(1);
                    $.each(product.EnabledGateways,function(i,v){
                        $('#Form_Gateway'+i).val(v);
                    });    
                    $.each(product.Meta,function(i,v){
                        var fi = $.inArray(i,$('.MetaLine .MetaName').map(function(){return $(this).val()}));
                        var any = false;
                        var anyon = false;
                        if($.inArray(i,$.makeArray(types[$('#Form_ProductType').val()]['Options']['VariableMeta']))>-1){
                            any=true;
                        }
                        if(v.substring(v.length-4)=='/any'){
                            v=v.substring(0,v.length-4);
                            any=true;
                            anyon=true;
                        }
                        
                        if(fi>-1){
                            
                            $('.MetaLine .MetaName:eq('+fi+')').val(i);
                            $('.MetaLine .MetaName:eq('+fi+')').nextAll('.MetaValue:first').val(v);
                            if(any){
                                $('.MetaLine .MetaAny:eq('+fi+')').css({'visibility':'visible'});
                                $('.MetaLine .MetaAny:eq('+fi+')').prevAll('span:first').css({'visibility':'visible'});
                                $('.MetaLine .MetaAny:eq('+fi+')').val(anyon?1:0).removeAttr('readonly');
                            }else{
                                $('.MetaLine .MetaAny:eq('+fi+')').css({'visibility':'hidden'});
                                $('.MetaLine .MetaAny:eq('+fi+')').prevAll('span:first').css({'visibility':'hidden'});
                                $('.MetaLine .MetaAny:eq('+fi+')').val(0).attr('readonly','readonly');
                            }
                        }else{
                            $('.MetaLine:last .MetaName').val(i);
                            $('.MetaLine:last .MetaValue').val(v);
                            if(any){
                                $('.MetaLine:last .MetaAny').css({'visibility':'visible'});
                                $('.MetaLine:last .MetaAny').prevAll('span:first').css({'visibility':'visible'});
                                $('.MetaLine:last .MetaAny').val(anyon?1:0).removeAttr('readonly');
                            }else{
                                $('.MetaLine:last .MetaAny').css({'visibility':'hidden'});
                                $('.MetaLine:last .MetaAny').prevAll('span:first').css({'visibility':'hidden'});
                                $('.MetaLine:last .MetaAny').val(0).attr('readonly','readonly');
                            }

                            if($.inArray(v,$.makeArray(types[$('#Form_ProductType').val()]['Options']['RequiredMeta']))>-1);
                                $('.MetaLine:last .MetaName').attr('readonly','readonly')
                            MetaLine();
                            $('.MetaLine:last .MetaValue').trigger('keyup');
                            $('.MetaLine:last .MetaName').val('');
                            $('.MetaLine:last .MetaValue').val('');
                        }
                    });            
                    
                }
            });
        return false;
    });
    
    
});
