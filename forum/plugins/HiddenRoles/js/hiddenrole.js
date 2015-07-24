jQuery(document).ready(function(){
	if($("li.RolePermissions").length){
		 hr = $("<input />").attr({
			"type":"checkbox",
			"id":"Form_HiddenRole",
			"name":"Role/Hidden",
			"value":1
		});
		if(gdn.definition("IsHidden")==1)
			hr.attr("checked","checked");
		 hri = $("<input />").attr({
			"type":"hidden",
			"name":"Checkboxes[]",
			"value":'Hidden'
		});
		$("li.RolePermissions").before('<li class="HiddenRole"><label for="Form_Hidden">'+gdn.definition("Hidden")+'</label></li>');
		$("li.HiddenRole").append(hr);
		$("li.HiddenRole").append(hri);
	
		if(gdn.definition("IsHidden")==1 && gdn.definition("HiddenName")){
			var name = $('#Form_Name').val();
			$('#Form_Name').val(gdn.definition("HiddenName"));
			$('#Form_Name').attr(
				'name',
				$('#Form_Name').attr('name').replace('Name','HiddenName')
			);
			$('#Form_Name').after(
				'<input type="hidden" id="HiddenName" value='+name+' name="Role/Name" />'
			);
		}
		$('#Form_HiddenRole').click(function(){
			var on = $(this).is(':checked')?1:0;
			var remember = gdn.definition('Remember','Remember you still have to save the role to apply.');
			if(on && !confirm(gdn.definition('Hide','Caution! Are you sure you want to hide this role?')+"\n"+remember)){
				$(this).removeAttr('checked');
				return;
			}
			if(!on && !confirm(gdn.definition('Unhide','Caution! Are you sure you want to unhide this role?')+"\n"+remember)){
				$(this).attr('checked','checked');
				return;
			}
			if(on){
				$('#Form_Name').attr(
					'name',
					$('#Form_Name').attr('name').replace('Name','HiddenName')
				);
				var name = 'hr'+String(Math.floor( Math.random()*99999))+String(Date.now());
				$('#Form_Name').after(
					'<input type="hidden" id="HiddenName" value='+name+' name="Role/Name" />'
				);
			}else{
				$('#HiddenName').remove();
				$('#Form_Name').attr(
					'name',
					$('#Form_Name').attr('name').replace('HiddenName','Name')
				);
			}
		});
	}
	

	

});
