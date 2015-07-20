jQuery(document).ready(function($) {

   // Handle this new unread option button...
   // 7. Unread discussion
   $('a.UnreadDiscussion').livequery('click', function() {
      var btn = this;
      var row = $(btn).parents('li.Item');
      $.ajax({
         type: "POST",
         url: $(btn).attr('href'),
         data: 'DeliveryType=BOOL&DeliveryMethod=JSON',
         dataType: 'json',
         error: function(XMLHttpRequest, textStatus, errorThrown) {
            $.popup({}, XMLHttpRequest.responseText);
         },
         success: function(json) {
            gdn.inform(json.StatusMessage);
            if (json.State)
               // Add new class to li.Item element
               $(row).addClass('New');
               // Add "all new" info next to the number of comments
               var commentcount = $(row).find('span.CommentCount');
               $(commentcount).after('<strong>all new</strong>');
            if (json.LinkText)
               $(sender).text(json.LinkText);
            if (json.RedirectUrl)
              setTimeout("document.location='" + json.RedirectUrl + "';", 300);
         }
      });
      return false;
   });
   
});
