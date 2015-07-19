var SpoilersPlugin = {
   FindAndReplace: function() {
      
      $('div.UserSpoiler').each(function(i, el) {
         SpoilersPlugin.ReplaceSpoiler(el);
      });
   },
   
   ReplaceComment: function(Comment) {
      $(Comment).find('div.UserSpoiler').each(function(i,el){
         SpoilersPlugin.ReplaceSpoiler(el);
      },this);
   },
   
   ReplaceSpoiler: function(Spoiler) {
      if (Spoiler.SpoilerFunctioning) return;
      Spoiler.SpoilerFunctioning = true;
      Spoiler = $(Spoiler);
      var SpoilerTitle = Spoiler.find('div.SpoilerTitle');
      var SpoilerButton = document.createElement('input');
      SpoilerButton.type = 'button';
      SpoilerButton.value = 'show';
      $(SpoilerButton).click(jQuery.proxy(function(event){
         $(this).find('div.SpoilerText').css('display','block');
         $(event.target).remove();
      },Spoiler));
      SpoilerTitle.append(SpoilerButton);

   }
};

// Events!

jQuery(document).ready(function(){
   SpoilersPlugin.FindAndReplace();
});

jQuery(document).bind('CommentPagingComplete',function() {
   SpoilersPlugin.FindAndReplace();
});

jQuery(document).bind('CommentAdded', function() {
   SpoilersPlugin.FindAndReplace();
});