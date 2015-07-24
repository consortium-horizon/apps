<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo $this->Data['Title'];?></h1>
<style>
    table,tr,td,th,tbody,thead{
        padding:0!important;
        margin:0!important;
    }
    
    table td div{
        white-space:normal;
        word-wrap: break-word!important;
        height:200px;
        overflow:auto;
    }
    
    .flexigrid div.nBtn,.flexigrid div.nBtn *{
        display:none!important;
    }
</style>
<div class="Info">
   <?php echo T($this->Data['Description']); ?>
</div>
<div>
<table id="gridContainer" height="100%" width="100%"></table> 

<script> 
jQuery(document).ready(function($) {

var cols =[];
var rows 
$.getJSON(gdn.url('/settings/transactionlog.json'),{page:1,rc:1}, function(log) {

    if(!log || !log.rows.length){
        $('.Errors').append('</div><ul><li><?php echo T('No records yet'); ?></li></ul></div>');
        return;
    }

    $.each(log.rows[0].cell,function(name,value){
        $('gridContainer').html(cols.length)
        if($.inArray(name, ['TransactionID','Name','Email','Photo','Inform'])==-1){
            var w, n;
            switch(name){
                case 'Meta':
                    w=250;
                    break;
                case 'Status':
                    w=100;
                    break;
                case 'PurchaseUserID':
                    n='User';
                    w=100;
                    break;
                case 'GatewayTransactionID':
                    n='TransactionID';
                default:
                    w=150;
            }
            
            cols.push({'name': name,   display:n?n:name, searchable: true,  sortable: true,width :w });
        }
    });
    cols.push({id:'blank',name: 'blank',   display: '',   sortable: false,width : 50});
    cols[0].isdefault= true;
    
    loadGrid();
});

function loadGrid(){
    $('#gridContainer').flexigrid({ 
        dataType: 'json',
        method: 'GET',
        url: gdn.url('/settings/transactionlog.json'),
        sortname:'Date',
        sortorder: 'desc',
        useRp: true,
        rp:20,
        usepager: true,
        colModel:cols,
        searchitems:cols,
        showTableToggleBtn: false,
        height: 200,
        height: $(" #flexigridDiv").innerHeight(),
        resizable:false,
        preProcess:function(log){
            if(!log || !log.rows.length) return log;
            $.each(log.rows,function(index,row){
                log.rows[index].cell['blank']='';
                if(!row.cell.Meta) return;
                log.rows[index].cell.Meta=row.cell.Meta.replace(/[|]/g,'<br />').replace(/[:]/,':<br />').replace(/<br \/>([a-z_]+)(->)/ig,'<br /><b>$1&nbsp;</b>');
                log.rows[index].cell.blank='';
                log.rows[index].cell.PurchaseUserID='<a href="'+gdn.url('/user/'+row.cell.Name)+'" target="_blank">'+row.cell.Name+'</a>';
                colour='';
                
                switch(row.cell.Status){
                    case 'force_complete':
                    case 'payment_complete':
                        colour='green';
                        break;
                    case 'payment_pending':
                        colour='blue';
                        break;
                    case 'payment_incomplete':
                        colour='orange';
                        break;
                    case 'payment_invalid':
                        colour='red';
                        break;
                    case 'account_expired':
                        colour='grey';
                        break;
                }
                if(colour)
                    log.rows[index].cell.Status='<span style="color:'+colour+';">'+row.cell.Status+'</span>';
                if(row.cell.GatewayTransactionID && gdn.definition(row.cell.Gateway+'URL',0))
                    log.rows[index].cell.GatewayTransactionID='<a href="'+gdn.definition(row.cell.Gateway+'URL').replace('%s',row.cell.GatewayTransactionID)+'" target="_blank">'+row.cell.GatewayTransactionID+'</a>';
                
            });
        
        return log;
        },
        
        onSubmit : function(){
        $('#gridContainer').flexOptions({params: [{name:'history', value:$('#history:checked').length?1:0}]});
        return true;
        } 
    });
    
    $('.flexigrid .pDiv .pDiv2').append($('<div class="btnseparator"></div>'+
        '<div class="pGroup"><span class="pcontrol"><input id="history" type="checkbox" />'+
        'Show historical / superseded transaction statuses</span></div>'));
    $('#history').click(function(){$('#gridContainer').flexReload();});

}
 });
</script>
<div class="Messages Errors"></div>
<noscript><div class="Messages Errors"><ul><li><?php echo T('Javascript Required'); ?></li></ul></div></noscript>
</div>
