$(document).ready(function() {

    // Show the form if user wanna join
    $("#iWannaJoin").change(function() {

        if ($(this).prop('checked')) {
            $("#lchForm").slideDown();
            $("#iWannaJoinNotif").addClass("success");
            $( ".required" ).prop('required',true);
        }
        else {
            $("#lchForm").slideUp();
            $("#iWannaJoinNotif").removeClass("success");
            $( ".required" ).prop('required',false);
        }
    });

    // Change form when user select a game
    $("#gamelist").change(function() {

        // First we hide the opened sections
        $(".registrationSection").hide();
        $( "input[class*='CustomField']" ).prop('required',false);

        // Then we reveal the one selected
        switch ($(this).val()) {
            case "Planetside 2" :
            $("#planetsideRegistrationSection").slideDown();
            $(".planetsideCustomField").prop('required',true);
            break;

            case "Autre" :
            $("#otherRegistrationSection").slideDown();
            $(".otherCustomField").prop('required',true);
            break;
        }
    });

    // Add secondary game
    var gameCount = 0;
    var gameLimit = 6;

    $("#addGame").click(function() {
        if (gameCount>=gameLimit) return false;
        $('#moreGames').append('<input type="text" class="otherGameInput" name="secondaryGame'+ gameCount +'" id="something" required/>');
        gameCount++;
        $('#moreGamesCount').val(gameCount);
    });

    $("#removeGame").click(function() {
        if (gameCount<=0) return false;
        $('#moreGames input').last().remove();
        gameCount--;
        $('#moreGamesCount').val(gameCount);
    });

    // How did you find us focus/blur actions
    $( "#howDidYouFindUsInput" ).focus(function() {
        $( "#howDidYouFindUsKO" ).fadeIn();
    });
    $( "#howDidYouFindUsInput" ).blur(function() {
        $( "#howDidYouFindUsKO" ).fadeOut();
    });
    

    // More about you focus/blur actions
    $( "#moreAboutYouInput" ).blur(function() {
        var minLength = 50;
        var goodLength = 100;

        if($(this).val().split(/\s+/).length < minLength) {
            $( "#descriptionKO" ).fadeIn();
            return false;
        }
        else if(goodLength < $(this).val().split(/\s+/).length) {
            $( "#descriptionOK" ).fadeIn();
            return false;
        }
    });

    $( "#moreAboutYouInput" ).focus(function() {
        $( "#descriptionOK" ).fadeOut();
        $( "#descriptionKO" ).fadeOut();
    });
});
