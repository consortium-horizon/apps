// Ajaxify
// v1.0.2 - 31 August, 2013
// https://github.com/TjWallas/ajaxify
(function(window,undefined){
	
	// Prepare our Variables
	var
		History = window.History,
		$ = window.jQuery,
		document = window.document;

	// Check to see if History.js is enabled for our Browser
	if ( !History.enabled ) {
		return false;
	}

	// Wait for Document
	$(function(){
		// Prepare Variables
		var
			/* Application Specific Variables */
			// contentSelector = '#content,article:first,.article:first,.post:first',  // Original
			contentSelector = '#Content',  // LCH
			$content = $(contentSelector).filter(':first'),
			contentNode = $content.get(0),
			// $menu = $('#menu,#nav,#topnav,#nav:first,.nav:first').filter(':first'),  ,#Panel,.SiteMenu  // Original
			$menu = $('.FilterMenu').filter(':first'),  // LCH
			// activeClass = 'active selected current youarehere',  // Original
			activeClass = 'Active',  // LCH
			// activeId = 'active',  // Original
			// activeSelector = '.active,#active,.selected,.current,.youarehere',  // Original
			activeSelector = '.Active',  // LCH
			menuChildrenSelector = '> li,> ul > li',
			completedEventName = 'statechangecomplete',
			/* Application Generic Variables */
			$window = $(window),
			$body = $(document.body),
			rootUrl = History.getRootUrl(),
			scrollOptions = {
				duration: 800,
				easing:'swing'
			};
		NProgress.configure({ showSpinner: false });
		
		// Ensure Content
		if ( $content.length === 0 ) {
			$content = $body;
		}
		
		// Internal Helper
		$.expr[':'].internal = function(obj, index, meta, stack){
			// Prepare
			var
				$this = $(obj),
				url = $this.attr('href')||'',
				isInternalLink;
			
			// Check link
			isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;
			
			// Ignore or Keep
			return isInternalLink;
		};
		
		// HTML Helper
		var documentHtml = function(html){
			// Prepare
			var result = String(html)
				.replace(/<\!DOCTYPE[^>]*>/i, '')
				.replace(/<(html|head|body|title|meta|script)([\s\>])/gi,'<div class="document-$1"$2')
				.replace(/<\/(html|head|body|title|meta|script)\>/gi,'</div>')
			;
			
			// Return
			return $.trim(result);
		};
		
		// Ajaxify Helper
		$.fn.ajaxify = function(){
			// Prepare
			var $this = $(this);
			
			// Ajaxify
			$this.find('a:internal:not(.no-ajaxy,[rel])').click(function(event){
				// Prepare
				var
					$this = $(this),
					url = $this.attr('href'),
					url = url.replace('www.consortium-horizon.com/',''),  // LCH
					title = $this.attr('title')||null;

				
				// Continue as normal for cmd clicks etc
				if ( event.which == 2 || event.metaKey ) { return true; }
				
				// Ajaxify this link
				//console.log('url = '+url+'\ntitle = '+title);  // LCH 'for debuging url'
				History.pushState(null,title,url);
				event.preventDefault();
				return false;
			});
			
			// Chain
			return $this;
		};
		
		// Ajaxify our Internal Links
		$body.ajaxify();
		
		// Hook into State Changes
		$window.bind('statechange',function(){
			// Prepare Variables
			// console.log('url = '+url+'\nrelativeUrl = '+relativeUrl+'\nrootURL = '+rootUrl);  // LCH 'for debuging url'
			var
				State = History.getState(),
				url = State.url,  // Original
				//url = State.url.replace('https://www.consortium-horizon.com/www.consortium-horizon.com/',rootUrl),  // LCH
				relativeUrl = url.replace(rootUrl,'');  // Original



			// LCH 'for debuging url'
			//console.log(State);
			//console.log('url = '+url+'\nrelativeUrl = '+relativeUrl+'\nrootURL = '+rootUrl);
			


			// Set Loading
			$body.addClass('loading');
			if (NProgress != undefined) NProgress.start();

			// Start Fade Out
			// Animating to opacity to 0 still keeps the element's height intact
			// Which prevents that annoying pop bang issue when loading in new content
			$content.animate({opacity:0},800);
			
			// Ajax Request the Traditional Page
			$.ajax({
				url: url,
				success: function(data, textStatus, jqXHR){
					// Prepare
					var
						$data = $(documentHtml(data)),
						$dataBody = $data.find('.document-body:first'),
						$dataContent = $dataBody.find(contentSelector).filter(':first'),
						$menuChildren, contentHtml, $scripts;
					
					// Fetch the scripts
					$scripts = $dataContent.find('.document-script');
					if ( $scripts.length ) {
						$scripts.detach();
					}

					// Fetch the content
					contentHtml = $dataContent.html()||$data.html();
					if ( !contentHtml ) {
						document.location.href = url;
						return false;
					}
					
					// Update the menu
					$menuChildren = $menu.find(menuChildrenSelector);
					$menuChildren.filter(activeSelector).removeClass(activeClass);
					$menuChildren.filter(activeSelector).attr('id','deselected');
					$menuChildren = $menuChildren.has('a[href^="'+relativeUrl+'"],a[href^="/'+relativeUrl+'"],a[href^="'+url+'"]');  // Original
					// $menuChildren = $menuChildren.has('a[href="'+relativeUrl+'"],a[href="/'+relativeUrl+'"],a[href="'+url+'"]');  // LCH
					if ( $menuChildren.length === 1 ) { 
						$menuChildren.addClass(activeClass);  // LCH
						//$menuChildren.attr('id','active');  // Original
					}

					// Update the content
					$content.stop(true,true);
					$content.html(contentHtml).ajaxify().css('opacity',100).show(); /* you could fade in here if you'd like */

					// Update the title
					document.title = $data.find('.document-title:first').text();
					try {
						document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
					}
					catch ( Exception ) { }
					
					// Add the scripts
					$scripts.each(function(){
						var $script = $(this), scriptText = $script.text(), scriptNode = document.createElement('script');
						if ( $script.attr('src') ) {
							if ( !$script[0].async ) { scriptNode.async = false; }
							scriptNode.src = $script.attr('src');
						}
    						scriptNode.appendChild(document.createTextNode(scriptText));
						contentNode.appendChild(scriptNode);
					});
					
					if(NProgress) NProgress.done();

					// if((DISQUS != undefined) && ($content.find('#disqus_thread').length != 0)) {DISQUS.next.host.loader.loadEmbed();console.log('DISQUS Detected')}  // Original


					// Complete the change
					// if ( $body.ScrollTo||false ) { $body.ScrollTo(scrollOptions); } /* http://balupton.com/projects/jquery-scrollto */  // Original
					// if ( $content.ScrollTo||false ) { $content.ScrollTo(scrollOptions); } /* http://balupton.com/projects/jquery-scrollto */  // LCH
					$body.removeClass('loading');
					$window.trigger(completedEventName);
	
					// Inform Google Analytics of the change
					if ( typeof window._gaq !== 'undefined' ) {
						window._gaq.push(['_trackPageview', relativeUrl]);
					}

					// Inform ReInvigorate of a state change
					if ( typeof window.reinvigorate !== 'undefined' && typeof window.reinvigorate.ajax_track !== 'undefined' ) {
						reinvigorate.ajax_track(url);
						// ^ we use the full url here as that is what reinvigorate supports
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
					document.location.href = url;
					return false;
				}
			}); // end ajax

		}); // end onStateChange

	}); // end onDomLoad

})(window); // end closure
