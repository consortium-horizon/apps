

var client = new ZeroClipboard( document.getElementById("d_clip_button") );

client.on( "ready", function( readyEvent ) {
    // alert( "ZeroClipboard SWF is ready!" );
    +0.

    client.on( "aftercopy", function( event ) {
        // `this` === `client`
        // `event.target` === the element that was clicked
        event.target.style.display = "none";
        alert("Copied text to clipboard: " + event.data["text/plain"] );
    } );
} );
