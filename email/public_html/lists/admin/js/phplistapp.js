
/*
 * application JS library, UI independent code
 *
 */

/* these can be "overwritten" by the UI */
var busyImage = '<img src="images/busy.gif" with="34" height="34" border="0" alt="Please wait" />';
var menuArrowImage = 'ui/dressprow/images/menuarrow.png';
var menuArrowActiveImagesrc = 'ui/dressprow/images/menuarrow_active.png';

function urlParameter( name, link) {
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( link );
  if( results == null )
    return "";
  else
    return results[1];
}

var updateMessages = new Array();
var updateLock = false;
function messagesStatusUpdate() {
  if (updateLock) return;
  updateLock = true;
  for (var i = 0; i < updateMessages.length; i++) {
    messageStatusUpdate(updateMessages[i]);
  }  
  updateLock = false;
}
  
function messageStatusUpdate(msgid) {
   if (msgid) {
      $('#messagestatus'+msgid).load('./?page=pageaction&ajaxed=true&action=msgstatus&id='+msgid,"",function() {});
   } 
}

function getServerTime() {
   $('#servertime').load('./?page=pageaction&ajaxed=true&action=getservertime',"",function() {
   });
   setTimeout("getServerTime()",60100); // just over a minute
}

function autoSave() {
  // in the future, do an auto-save, for now, we want to keep the session alive
  $("#autosave").load('./?page=pageaction&ajaxed=true&action=keepalive');
}


function refreshCriteriaList() {
  var id = urlParameter('id',document.location);
  $("#existingCriteria").html(busyImage);
  $("#existingCriteria").load('./?page=pageaction&ajaxed=true&action=listcriteria&id='+id);
}

function refreshExport() {
 // alert('Refresh '+document.location);
  document.location = document.location;
}

function openHelpDialog(url) {
  $("#dialog").dialog({
    minHeight: 400,
    width: 600,
    modal: true
  });
  $("#dialog").html('<div class="openhelpimage">'+busyImage+'</div>');
  var destpage = urlParameter('page',url);
  url = url.replace(/page=/,'origpage=');
  $("#dialog").load(url+'&ajaxed=true&page=pageaction&action='+destpage);
  $(".ui-widget-overlay").click(function() {
    $("#dialog").dialog('close');
  });
}

function initialiseTranslation(text) {
  $("#dialog").dialog({
    minHeight: 400,
    width: 600,
    modal: true
  });
  $("#dialog").html('<div class="openhelpimage">'+text+'<br/>'+busyImage+'</div>');
  $("#dialog").load('./?ajaxed=true&page=pageaction&action=initlanguage');
}


function totalSentUpdate(msgid) {
   $('#totalsent'+msgid).load('./?page=pageaction&ajaxed=true&action=msgsent&id='+msgid,"",function() {
   });
   setTimeout("totalSentUpdate("+msgid+")",5000);
}

$(document).ready(function() {
  $(".note .hide").click(function() {
    $(this).parents('.note').hide();
  });

  $(".configurelink").click(function() {
   // alert(this.href);
    $("#configurecontent").load('./?page=ajaxcall&action=test');
    $("#configurecontent").show();
    return false;
  });

  $("a.ajaxable").click(function() {
    var url = this.href;
    var thispage = urlParameter('page',window.location.href);
    var action = urlParameter('action',url);
    if (action == "") {
      url += '&action='+thispage;
    }
    parent = $(this).parent();
    parent.html(busyImage);
    url = url.replace(/page=/,'origpage=');
  //  alert(url+'&ajaxed=true&page=pageaction');
    parent.load(url+'&ajaxed=true&page=pageaction');
    return false;
  });

  $("input:checkbox.checkallcheckboxes").click(function() {
    if (this.checked) {
      $("input[type=checkbox]:not(:checked)").each(function(){
        this.checked = true;
      });
    } else {
      $("input[type=checkbox]:checked").each(function(){
        this.checked = false;
      });
    }
  });

  var stop = false;

  if ($.fn.accordion) {
    $(".accordion").accordion({
        autoHeight: false,
        navigation: true,
        collapsible: true
      });
  }

  $(".opendialog").click(function() {
    openHelpDialog(this.href);
    return false;
  });
  $(".helpdialog").click(function() {
    openHelpDialog(this.href);
    return false;
  });
  $(".closedialog").click(function() {
    $("#dialog").dialog('close');
  });
                 
  //dropbuttons						   
  $("div.dropButton img.arrow").click(function(){ 					
      submenu = $(this).parent().parent().find("div.submenu");		
      if(submenu.css('display')=="block"){
        submenu.hide(); 		
        $(this).attr('src',menuArrowImagesrc);									
      } else {
        submenu.fadeIn(); 		
        $(this).attr('src',menuArrowActiveImagesrc);	
      }	
      return false;					
  });						   

  /* hmm, doesn't work yet, but would be nice at some point
  $("#emailsearch").autocomplete({
    source: "?page=pageaction&ajaxed=true&action=searchemail",
    minLength: 2,
    select: function(event, ui) {
    log(ui.item ? ("Selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
    }
  });
  */

  if ($.fn.tabs) {
    $(".tabbed").tabs({
      //http://jqueryui.com/upgrade-guide/1.9/#deprecated-ajaxoptions-and-cache-options-added-beforeload-event
        ajaxOptions: {
        error: function(xhr, status, index, anchor) {
          $(anchor.hash).html("Error fetching page");
        }
      }
    });
    $(".tabbed1").tabs();
  }
  
  $("#subjectinput").focus(function() {
    if (this.value == '(no subject)') {
      this.value = "";
    }
  })
  $("#subjectinput").blur(function() {
    if (this.value == "") {
      this.value = "(no subject)";
      return;
    }
  });
  $("#remoteurlinput").focus(function() {
    if (this.value == 'e.g. http://www.phplist.com/testcampaign.html') {
      this.value = "";
    }
  })
  $("#remoteurlinput").blur(function() {
    if (this.value == "") {
      this.value = "e.g. http://www.phplist.com/testcampaign.html";
      return;
    }
    $("#remoteurlstatus").html(busyImage);
    $("#remoteurlstatus").load("./?page=pageaction&action=checkurl&ajaxed=true&url="+this.value);
  });
  $("#filtertext").focus(function() {
    if (this.value == ' --- filter --- ') {
      this.value = "";
    }
  })
  $("#filtertext").blur(function() {
    if (this.value == "") {
      this.value = " --- filter --- ";
      return;
    }
  });  

  $("input:radio[name=sendmethod]").change(function() {
    if (this.value == "remoteurl") {
      $("#remoteurl").show();
      $("#messagecontent").hide();
    } else {
      $("#remoteurl").hide();
      $("#messagecontent").show();
    }
  });
  
  $("a.savechanges").click(function() {
    if (changed) {
      document.sendmessageform.followupto.value = this.href;
      document.location.hash=""
      document.sendmessageform.submit();
      return false;
    }
  });
  
  $("#criteriaSelect").change(function() {
    var val = $("#criteriaSelect").val();
    var operator = '';
    switch (aT[val]) {
      case 'checkbox':
        $("#criteriaAttributeOperator").html('<input type="hidden" name="criteria_operator" value="is" />');
        $("#criteriaAttributeValues").html('CHECKED <input type="radio" name="criteria_values" value="checked" /> UNCHECKED <input type="radio" name="criteria_values" value="unchecked" />');
        break;
      case 'checkboxgroup':
      case 'select':
      case 'radio':
        $("#criteriaAttributeOperator").html('IS <input type="radio" name="criteria_operator" value="is" checked="checked" /> IS NOT <input type="radio" name="criteria_operator" value="isnot" />');
        $("#criteriaAttributeValues").html(busyImage);
        $("#criteriaAttributeValues").load("./?page=pageaction&ajaxed=true&action=attributevalues&name=criteria_values&type=multiselect&attid="+val);
        break;
      case 'date':
        $("#criteriaAttributeOperator").html('IS <input type="radio" name="criteria_operator" value="is" checked="checked" /> IS NOT <input type="radio" name="criteria_operator" value="isnot" /> IS BEFORE <input type="radio" name="criteria_operator" value="isbefore" /> IS AFTER <input type="radio" name="criteria_operator" value="isafter" />');
        $("#criteriaAttributeValues").html('<input type="text" id="datepicker" name="criteria_values" size="30"/>');
        $("#datepicker").datepicker({dateFormat: 'yy-mm-dd' });
        break;
      default:
        $("#criteriaAttributeOperator").html('');
        $("#criteriaAttributeValues").html('');
        break;
    }
  });

  $("#initialadminpassword").keyup(function() {
    if (this.value.length >= 8) {
      $("#initialisecontinue").removeAttr('disabled');
    } else if (this.value.length < 8) {
      $("#initialisecontinue").attr('disabled', 'disabled');
    }
  });
  $("#initialiseform").submit(function() {
    $("#dialog").dialog({
      minHeight: 400,
      width: 600,
      modal: true
    });
    $("#dialog").html('<div class="openhelpimage">Initialising phpList, please wait.<br/>'+busyImage+'</div>');
  });
  
  // export page
  $("input:radio[name=column]").change(function() {
    if (this.value == 'nodate') {
      $("#exportdates").hide();
    } else {
      $("#exportdates").show();
    }
  });
  
  $("#processexport").click(function() {
    // for export, refresh underlying page, to get a new security token
    setTimeout("refreshExport()",10000);
  })

  $("#selectallcheckbox").click(function() {
     $(':checkbox').prop('checked', this.checked);
  })

  //fade out 'actionresult' user feedback
  $('.actionresult').delay(4000).fadeOut(4000); 
  //fade out 'result' user feedback
  $('.result').delay(4000).fadeOut(4000); 


//  $("#processqueueoutput").html('Processing queue, please wait<script type="text/javascript">alert(document.location)</script>');
  $("#spinner").html(busyImage);
  
  $("#stopqueue").click(function() {
    $("#processqueueoutput").html('Processing cancelled');
    $("#spinner").html('&nbsp;');    
    $("#stopqueue").hide();    
    $("#resumequeue").show();    
  });

  var docurl = document.location.search;
  document.cookie="browsetrail="+escape(docurl);

 // setTimeout("autoSave();",60000); // once a minute should suffice
  setTimeout("autoSave();",500); // for testing

/* future dev
  $("#listinvalid").load("./?page=pageaction&action=listinvalid&ajaxed=true",function() {
 //  alert("Loaded")
  });
  $("#refreshCriteria").click(refreshCriteriaList);
  
  $("#addcriterionbutton").click(function() {
    $("#addcriterionbutton").addClass('disabled');
    var request = document.location.search+'&'+$("#sendmessageform").serialize();
    var attr = urlParameter('criteria_attribute',request);
    if (attr == '') {
      alert('Select an attribute to add');
      return false;
    };
    var vals = urlParameter('criteria_values',request);
    var arrVals = urlParameter('criteria_values[]',request);
    if (vals == '' && arrVals) {
      alert('Select a value to add');
      return false;
    };
    
    request = request.replace(/\?/,'');
    request = request.replace(/page=/,'origpage=');
    request = './?page=pageaction&action=storemessage&'+request;
    alert(request);
    $("#existingCriteria").html(busyImage);
 //   $("#hiddendiv").load(request);
 //   $("#sendmessageform").submit();
 //   setTimeout("refreshCriteriaList()",5000);

//    refreshCriteriaList();
    return true;
  });
*/

});

function allDone(text) {
  if (!text) {
    text = 'All done';
  }
  $("#processqueueoutput").html('<span class="info" style="padding: 40px">'+text+'</span>');
  $("#spinner").html('&nbsp;');    
  $("#stopqueue").hide();    
  $("#resumequeue").show();
  $("#progressmeter").hide();   
}

var overallTotal = 0;
var overallSent = 0;

/* this one keeps track of the total between multiple processqueue runs */
$.fn.updateSendProgress = function() {
  var args = arguments[0].split(',') || {}; 
  var total = parseInt(args[1]);
  var done = parseInt(args[0]);
  if (total > overallTotal) {    overallTotal = total;  } else {    total = overallTotal;  }
  if (done > 0) {
    overallSent += done;
  }
  $.fn.updateProgress(overallSent,overallTotal);
}

$.fn.updateProgress = function() {
  if ($.isNumeric(arguments[0])) {
    var total = parseInt(arguments[1]);
    var done = parseInt(arguments[0]);
  } else {
    var args = arguments[0].split(',') || {}; 
    var total = parseInt(args[1]);
    var done = parseInt(args[0]);
  }
  var perc;
  if (total == 0) {
    perc = 0;
  } else {
    perc = parseInt((done / total) * 100);
  }
  $("#progresscount").html(done + ' / '+ total);
  $("#progresscount").show();
  $("#progressbar" ).progressbar({max: 100, value: +perc});
};


/*
 * old library of stuff that needs to be ported to jQuery style JS
 */

function deleteRec(url) {
	if (confirm("Are you sure you want to delete this record?")) {
		document.location = url;
	}
}

// @@TODO rewrite opening in pop-over
function viewImage(url,w,h) {
   alert("needs rewriting");
}
