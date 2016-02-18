//==================================================================================================
//                                          SETTINGS
//==================================================================================================
var csh_settings = {
    defaults: {
        'auto-links':    true,      // Allows you to turn detection of links in the highlighted element on and off. If the option is turned off, URLs won’t be clickable.
        'class-name':    '',        // Allows you to add a custom class (or multiple classes) to every highlighter element that will be created on the page.
        'collapse':      false,     // Allows you to force highlighted elements on the page to be collapsed by default.
        'first-line':    1,         // Allows you to change the first (starting) line number.
        'gutter':        true,      // Allows you to turn gutter with line numbers on and off.
        'highlight':     null,	    // Allows you to highlight one or more lines to focus user’s attention. When specifying as a parameter, you have to pass an array looking value, like [1, 2, 3] or just an number for a single line. If you are changing SyntaxHighlighter.defaults['highlight'], you can pass a number or an array of numbers.
        'html-script':   false,	    // Allows you to highlight a mixture of HTML/XML code and a script which is very common in web development. Setting this value to true requires that you have shBrushXml.js loaded and that the brush you are using supports this feature.
        'smart-tabs':    true,	    // Allows you to turn smart tabs feature on and off.
        'tab-size':      4,	        // Allows you to adjust tab size.
        'toolbar':       false      // Toggles toolbar on/off.
    },
    config: {
        'bloggerMode':   false,     // Blogger integration. If you are hosting on blogger.com, you must turn this on.
        'space':         '&nbsp;',  // Space char
        'strings': {                // Allows you to change default messages
            'aboutDialog':        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>About SyntaxHighlighter</title></head><body style="font-family:Geneva,Arial,Helvetica,sans-serif;background-color:#fff;color:#000;font-size:1em;text-align:center;"><div style="text-align:center;margin-top:1.5em;"><div style="font-size:xx-large;">SyntaxHighlighter</div><div style="font-size:.75em;margin-bottom:3em;"><div>version 3.0.83 (July 02 2010)</div><div><a href="http://alexgorbatchev.com/SyntaxHighlighter" target="_blank" style="color:#005896">http://alexgorbatchev.com/SyntaxHighlighter</a></div><div>JavaScript code syntax highlighter.</div><div>Copyright 2004-2010 Alex Gorbatchev.</div></div><div>If you like this script, please <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2930402" style="color:#005896">donate</a> to <br/>keep development active!</div></div></body></html>',
            'alert':              'SyntaxHighlighter\n\n',
            'brushNotHtmlScript': 'Brush wasn\'t configured for html-script option: ',
            'expandSource':       'expand source',
            'help':               '?',
            'noBrush':            'Can\'t find brush for: '
        },
        'stripBrs':      false,     // If your software adds <br /> tags at the end of each line, this option allows you to ignore those.
        'tagName':       'pre',     // Facilitates using a different tag.
        'useScriptTags': true
    }
}
//==================================================================================================

function initSyntaxHighlighter(){

    $(csh_settings.config.tagName).each(function(){
        if($(this).attr("lang")!=""){
            var codeText=$(this).html();
            codeText = codeText.replace(/<br>/g, "\n");
            $(this).html(codeText);
            $(this).attr("class","brush:"+$(this).attr("lang")+';'+$(this).attr("class"));
        }
    });

    SyntaxHighlighter.autoloader.apply(null, path(
        "applescript            @shBrushAppleScript.js",
        "actionscript3 as3      @shBrushAS3.js",
        "bash shell             @shBrushBash.js",
        "coldfusion cf          @shBrushColdFusion.js",
        "cpp c                  @shBrushCpp.js",
        "c# c-sharp csharp      @shBrushCSharp.js",
        "css                    @shBrushCss.js",
        "delphi pascal          @shBrushDelphi.js",
        "diff patch pas         @shBrushDiff.js",
        "erl erlang             @shBrushErlang.js",
        "groovy                 @shBrushGroovy.js",
        "java                   @shBrushJava.js",
        "jfx javafx             @shBrushJavaFX.js",
        "js jscript javascript  @shBrushJScript.js",
        "perl pl                @shBrushPerl.js",
        "php                    @shBrushPhp.js",
        "text plain             @shBrushPlain.js",
        "py python              @shBrushPython.js",
        "ruby rails ror rb      @shBrushRuby.js",
        "sass scss              @shBrushSass.js",
        "scala                  @shBrushScala.js",
        "sql                    @shBrushSql.js",
        "vb vbnet               @shBrushVb.js",
        "xml xhtml xslt html    @shBrushXml.js"
    ));

    $.extend(SyntaxHighlighter.config,csh_settings.config);
    $.extend(SyntaxHighlighter.defaults,csh_settings.defaults);

    SyntaxHighlighter.all();
    SyntaxHighlighter.vars.discoveredBrushes=null;

    // if error, restore the old code visibility
    setTimeout(function(){
        $(csh_settings.config.tagName).addClass('visible');
    }, 1000);

}

$(function(){

    // HIDES THE PRE CODE BEFORE THE SYNTAX HIGHLIGHTER IS LOADED
    $('head').append('<style type="text/css">'+csh_settings.config.tagName+' { visibility: hidden !important; } '+csh_settings.config.tagName+'.visible { visibility: visible !important; }</style>');

    initSyntaxHighlighter();

    // ADD THE SYNTAX HIGHLIGHTER ON THE DISCUSSION AFTER A COMMENT HAS BEEN ADDED
    $(document).on('CommentAdded', function() {
        initSyntaxHighlighter();
    });

    // ADD THE SYNTAX HIGHLIGHTER ON THE DISCUSSION WHEN PREVIEWING A NEW COMMENT
    $(document).on('PreviewLoaded', function(frm) {
        window.checker = setInterval(function(){
            if($('#Form_Comment .Preview').length){
                clearInterval(window.checker);
                initSyntaxHighlighter();
            }
        },50);
    });

    // ADD THE SYNTAX HIGHLIGHTER ON THE NEW DISCUSSION WHEN PREVIEWING THE NEW DISCUSSION
    $(document).on('popupReveal', function(frm) {
        if($('#DiscussionFormPreview .Message').length){
            initSyntaxHighlighter();
        }
    });

});

function path(){

    var args = arguments, result = [];

    for(var i = 0; i < args.length; i++)
        result.push(args[i].replace("@", gdn.definition('WebRoot', '')+"/plugins/CreativeSyntaxHighlighter/js/"));

    return result
};