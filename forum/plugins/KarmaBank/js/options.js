jQuery(document).ready(function($){
    var ops = $.parseJSON(gdn.definition('KBOperationsOptions'));
    $('#Form_Operation').change(function(){
        $('span.OpOps').remove();
        $('#Form_Option').remove();
        if(typeof ops[$(this).val()]!='undefined'){
            var optionsOps = $.map(ops[$(this).val()],function(v,i){
                return '<option value="'+v+'" >'+v+'</option>';
                
            }).join('');
            var selectOps = $('<select id="Form_Option" name="Form/Option"></select>').append(optionsOps);
            $('#Form_Condition').after(selectOps);
        }
            
    });
    $('#Form_Operation').trigger('change');

    $('#Form_Condition').change(function(){
        cond = $(this).val();
        $('#KarmaBankMetaDescriptions dd, #KarmaBankMetaDescriptions dt').hide()
            $('#KarmaBankMetaDescriptions dt').each(function(){
                if($(this).text()==cond+':'){
                    $(this).show();
                    $(this).next('dd').show();
                }
            });
    });
    $('#Form_Condition').trigger('change');
});
