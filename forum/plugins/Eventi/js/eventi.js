jQuery(document).ready(function(){
   $('div.EventiPopup').mouseleave(function(event){
      console.log('hide');
      $(event.target).css('display','none');
   });
   $('a.Eventi').each(function(iter,link){
      $(link).mouseenter(function(event){
         var Popup = $(link).find('div.EventiPopup').first();
         var IsPopped = (Popup.css('display') == 'block');
         if (IsPopped) return;
         if (!Popup.length) return;
         
         var aPos = $(link).offset();
         var xDiff = (aPos.left - event.pageX) +32;
         var yDiff = (aPos.top - event.pageY) +32;
         
         Popup.css({
            'display':'block',
            'left':xDiff+'px',
            'top':yDiff+'px'
         });
      });
      $(link).mouseleave(function(event){
         var Popup = $(link).find('div.EventiPopup').first();
         var IsPopped = (Popup.css('display') == 'block');
         if (!IsPopped) return;
         Popup.css('display','none');
      });
   });
});