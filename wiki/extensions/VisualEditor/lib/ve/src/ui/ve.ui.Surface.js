/*!
 * VisualEditor UserInterface Surface class.
 *
 * @copyright 2011-2016 VisualEditor Team and others; see http://ve.mit-license.org
 */

/**
 * A surface is a top-level object which contains both a surface model and a surface view.
 *
 * @class
 * @abstract
 * @extends OO.ui.Widget
 *
 * @constructor
 * @param {HTMLDocument|Array|ve.dm.LinearData|ve.dm.Document} dataOrDoc Document data to edit
 * @param {Object} [config] Configuration options
 * @cfg {jQuery} [$scrollContainer] The scroll container of the surface
 * @cfg {ve.ui.CommandRegistry} [commandRegistry] Command registry to use
 * @cfg {ve.ui.SequenceRegistry} [sequenceRegistry] Sequence registry to use
 * @cfg {string[]|null} [includeCommands] List of commands to include, null for all registered commands
 * @cfg {string[]} [excludeCommands] List of commands to exclude
 * @cfg {Object} [importRules] Import rules
 * @cfg {string} [placeholder] Placeholder text to display when the surface is empty
 * @cfg {string} [inDialog] The name of the dialog this surface is in
 */
ve.ui.Surface = function VeUiSurface( dataOrDoc, config ) {
	var documentModel;

	config = config || {};

	// Parent constructor
	ve.ui.Surface.super.call( this, config );

	// Properties
	this.$scrollContainer = config.$scrollContainer || $( this.getElementWindow() );
	this.inDialog = config.inDialog || '';
	this.globalOverlay = new ve.ui.Overlay( { classes: [ 've-ui-overlay-global' ] } );
	this.localOverlay = new ve.ui.Overlay( { classes: [ 've-ui-overlay-local' ] } );
	this.$selections = $( '<div>' );
	this.$blockers = $( '<div>' );
	this.$controls = $( '<div>' );
	this.$menus = $( '<div>' );
	this.$placeholder = $( '<div>' ).addClass( 've-ui-surface-placeholder' );
	this.commandRegistry = config.commandRegistry || ve.init.target.commandRegistry;
	this.sequenceRegistry = config.sequenceRegistry || ve.init.target.sequenceRegistry;
	this.commands = OO.simpleArrayDifference(
		config.includeCommands || this.commandRegistry.getNames(), config.excludeCommands || []
	);
	this.triggerListener = new ve.TriggerListener( this.commands, this.commandRegistry );
	if ( dataOrDoc instanceof ve.dm.Document ) {
		// ve.dm.Document
		documentModel = dataOrDoc;
	} else if ( dataOrDoc instanceof ve.dm.LinearData || Array.isArray( dataOrDoc ) ) {
		// LinearData or raw linear data
		documentModel = new ve.dm.Document( dataOrDoc );
	} else {
		// HTMLDocument
		documentModel = ve.dm.converter.getModelFromDom( dataOrDoc );
	}
	this.model = this.createModel( documentModel );
	this.view = this.createView( this.model );
	this.dialogs = this.createDialogWindowManager();
	this.importRules = config.importRules || {};
	this.context = this.createContext();
	this.progresses = [];
	this.showProgressDebounced = ve.debounce( this.showProgress.bind( this ) );
	this.filibuster = null;
	this.debugBar = null;
	this.setPlaceholder( config.placeholder );

	this.toolbarHeight = 0;
	this.toolbarDialogs = new ve.ui.ToolbarDialogWindowManager( this, {
		factory: ve.ui.windowFactory,
		modal: false
	} );

	// Events
	this.getView().connect( this, { keyup: 'scrollCursorIntoView' } );
	this.getModel().getDocument().connect( this, { transact: 'onDocumentTransact' } );

	// Initialization
	this.$menus.append( this.context.$element );
	this.$element
		.addClass( 've-ui-surface' )
		.append( this.view.$element );
	this.view.$element.after( this.localOverlay.$element );
	this.localOverlay.$element.append( this.$selections, this.$blockers, this.$controls, this.$menus );
	this.globalOverlay.$element.append( this.dialogs.$element );
};

/* Inheritance */

OO.inheritClass( ve.ui.Surface, OO.ui.Widget );

/* Events */

/**
 * When a surface is destroyed.
 *
 * @event destroy
 */

/**
 * The surface was scrolled programmatically
 *
 * @event scroll
 */

/* Static Properties */

/**
 * The surface is for use on mobile devices
 *
 * @static
 * @inheritable
 * @property {boolean}
 */
ve.ui.Surface.static.isMobile = false;

/* Methods */

/**
 * Destroy the surface, releasing all memory and removing all DOM elements.
 *
 * @method
 * @chainable
 * @fires destroy
 */
ve.ui.Surface.prototype.destroy = function () {
	// Stop periodic history tracking in model
	this.model.stopHistoryTracking();

	// Destroy the ce.Surface, the ui.Context and window managers
	this.context.destroy();
	this.dialogs.destroy();
	this.toolbarDialogs.destroy();
	this.view.destroy();
	if ( this.debugBar ) {
		this.debugBar.destroy();
	}

	// Remove DOM elements
	this.$element.remove();
	this.globalOverlay.$element.remove();

	// Let others know we have been destroyed
	this.emit( 'destroy' );

	return this;
};

/**
 * Initialize surface.
 *
 * This must be called after the surface has been attached to the DOM.
 *
 * @chainable
 */
ve.ui.Surface.prototype.initialize = function () {
	// Attach globalOverlay to the global <body>, not the local frame's <body>
	$( 'body' ).append( this.globalOverlay.$element );

	if ( ve.debug ) {
		this.setupDebugBar();
	}

	// The following classes can be used here:
	// ve-ui-surface-dir-ltr
	// ve-ui-surface-dir-rtl
	this.$element.addClass( 've-ui-surface-dir-' + this.getDir() );

	this.getView().initialize();
	this.getModel().initialize();
	return this;
};

/**
 * Get the DOM representation of the surface's current state.
 *
 * @return {HTMLDocument} HTML document
 */
ve.ui.Surface.prototype.getDom = function () {
	return ve.dm.converter.getDomFromModel( this.getModel().getDocument() );
};

/**
 * Get the HTML representation of the surface's current state.
 *
 * @return {string} HTML
 */
ve.ui.Surface.prototype.getHtml = function () {
	return ve.properInnerHtml( this.getDom().body );
};

/**
 * Create a context.
 *
 * @method
 * @abstract
 * @return {ve.ui.Context} Context
 */
ve.ui.Surface.prototype.createContext = null;

/**
 * Create a dialog window manager.
 *
 * @method
 * @abstract
 * @return {ve.ui.WindowManager} Dialog window manager
 */
ve.ui.Surface.prototype.createDialogWindowManager = null;

/**
 * Create a surface model
 *
 * @param {ve.dm.Document} doc Document model
 * @return {ve.dm.Surface} Surface model
 */
ve.ui.Surface.prototype.createModel = function ( doc ) {
	return new ve.dm.Surface( doc );
};

/**
 * Create a surface view
 *
 * @param {ve.dm.Surface} model Surface model
 * @return {ve.ce.Surface} Surface view
 */
ve.ui.Surface.prototype.createView = function ( model ) {
	return new ve.ce.Surface( model, this );
};

/**
 * Check if the surface is for use on mobile devices
 *
 * @return {boolean} The surface is for use on mobile devices
 */
ve.ui.Surface.prototype.isMobile = function () {
	return this.constructor.static.isMobile;
};

/**
 * Set up the debug bar and insert it into the DOM.
 */
ve.ui.Surface.prototype.setupDebugBar = function () {
	this.debugBar = new ve.ui.DebugBar( this );
	this.debugBar.$element.insertAfter( this.$element );
};

/**
 * Get the bounding rectangle of the surface, relative to the viewport.
 *
 * @return {Object|null} Object with top, bottom, left, right, width and height properties.
 *  Null if the surface is not attached.
 */
ve.ui.Surface.prototype.getBoundingClientRect = function () {
	// We would use getBoundingClientRect(), but in iOS7 that's relative to the
	// document rather than to the viewport
	return this.$element[ 0 ].getClientRects()[ 0 ] || null;
};

/**
 * Get vertical measurements of the visible area of the surface viewport
 *
 * @return {Object|null} Object with top, left, bottom, and height properties. Null if the surface is not attached.
 */
ve.ui.Surface.prototype.getViewportDimensions = function () {
	var top, bottom,
		rect = this.getBoundingClientRect();

	if ( !rect ) {
		return null;
	}

	top = Math.max( this.toolbarHeight - rect.top, 0 );
	bottom = $( this.getElementWindow() ).height() - rect.top;

	return {
		top: top,
		left: rect.left,
		bottom: bottom,
		height: bottom - top
	};
};

/**
 * Check if editing is enabled.
 *
 * @deprecated Use #isDisabled
 * @method
 * @return {boolean} Editing is enabled
 */
ve.ui.Surface.prototype.isEnabled = function () {
	return !this.isDisabled();
};

/**
 * Get the surface model.
 *
 * @method
 * @return {ve.dm.Surface} Surface model
 */
ve.ui.Surface.prototype.getModel = function () {
	return this.model;
};

/**
 * Get the surface view.
 *
 * @method
 * @return {ve.ce.Surface} Surface view
 */
ve.ui.Surface.prototype.getView = function () {
	return this.view;
};

/**
 * Get the context menu.
 *
 * @method
 * @return {ve.ui.Context} Context user interface
 */
ve.ui.Surface.prototype.getContext = function () {
	return this.context;
};

/**
 * Get dialogs window set.
 *
 * @method
 * @return {ve.ui.WindowManager} Dialogs window set
 */
ve.ui.Surface.prototype.getDialogs = function () {
	return this.dialogs;
};

/**
 * Get toolbar dialogs window set.
 *
 * @return {ve.ui.WindowManager} Toolbar dialogs window set
 */
ve.ui.Surface.prototype.getToolbarDialogs = function () {
	return this.toolbarDialogs;
};

/**
 * Get the local overlay.
 *
 * Local overlays are attached to the same frame as the surface.
 *
 * @method
 * @return {ve.ui.Overlay} Local overlay
 */
ve.ui.Surface.prototype.getLocalOverlay = function () {
	return this.localOverlay;
};

/**
 * Get the global overlay.
 *
 * Global overlays are attached to the top-most frame.
 *
 * @method
 * @return {ve.ui.Overlay} Global overlay
 */
ve.ui.Surface.prototype.getGlobalOverlay = function () {
	return this.globalOverlay;
};

/**
 * @inheritdoc
 */
ve.ui.Surface.prototype.setDisabled = function ( disabled ) {
	if ( disabled !== this.disabled && this.disabled !== null ) {
		if ( disabled ) {
			this.view.disable();
			this.model.disable();
		} else {
			this.view.enable();
			this.model.enable();
		}
	}
	// Parent method
	return ve.ui.Surface.super.prototype.setDisabled.call( this, disabled );
};

/**
 * Disable editing.
 *
 * @deprecated Use #setDisabled
 * @method
 * @chainable
 */
ve.ui.Surface.prototype.disable = function () {
	return this.setDisabled( true );
};

/**
 * Enable editing.
 *
 * @deprecated Use #setDisabled
 * @method
 * @chainable
 */
ve.ui.Surface.prototype.enable = function () {
	return this.setDisabled( false );
};

/**
 * Handle transact events from the document model
 *
 * @param {ve.dm.Transaction} Transaction
 */
ve.ui.Surface.prototype.onDocumentTransact = function () {
	if ( this.placeholder ) {
		this.updatePlaceholder();
	}
};

/**
 * Scroll the cursor into view.
 *
 * This is required when the cursor disappears under the floating toolbar.
 */
ve.ui.Surface.prototype.scrollCursorIntoView = function () {
	var view, nativeRange, clientRect, cursorTop, scrollTo, toolbarBottom;

	if ( !this.toolbarHeight ) {
		return;
	}

	view = this.getView();
	nativeRange = view.getNativeRange();
	if ( !nativeRange ) {
		return;
	}

	if ( OO.ui.contains( view.$pasteTarget[ 0 ], nativeRange.startContainer, true ) ) {
		return;
	}

	clientRect = RangeFix.getBoundingClientRect( nativeRange );
	if ( !clientRect ) {
		return;
	}

	cursorTop = clientRect.top - 5;
	toolbarBottom = this.toolbarHeight;

	if ( cursorTop < toolbarBottom ) {
		scrollTo = this.$scrollContainer.scrollTop() + cursorTop - toolbarBottom;
		this.scrollTo( scrollTo );
	}
};

/**
 * Scroll the scroll container to a specific offset
 *
 * @param {number} offset Scroll offset
 * @fires scroll
 */
ve.ui.Surface.prototype.scrollTo = function ( offset ) {
	this.$scrollContainer.scrollTop( offset );
	this.emit( 'scroll' );
};

/**
 * Set placeholder text
 *
 * @param {string} [placeholder] Placeholder text, clears placeholder if not set
 */
ve.ui.Surface.prototype.setPlaceholder = function ( placeholder ) {
	this.placeholder = placeholder;
	if ( this.placeholder ) {
		this.$placeholder.prependTo( this.$element );
		this.updatePlaceholder();
	} else {
		this.$placeholder.detach();
	}
};

/**
 * Update placeholder rendering
 */
ve.ui.Surface.prototype.updatePlaceholder = function () {
	var firstNode, $wrapper,
		hasContent = this.getModel().getDocument().data.hasContent();

	this.$placeholder.toggleClass( 'oo-ui-element-hidden', hasContent );
	if ( !hasContent ) {
		// Use a clone of the first node in the document so the placeholder
		// styling matches the text the users sees when they start typing
		firstNode = this.getView().documentView.documentNode.getNodeFromOffset( 1 );
		if ( firstNode ) {
			$wrapper = firstNode.$element.clone();
			if ( ve.debug ) {
				// In debug mode a background colour from the render animation may be present
				$wrapper.removeAttr( 'style' );
			}
		} else {
			$wrapper = $( '<p>' );
		}
		this.$placeholder.empty().append( $wrapper.text( this.placeholder ) );
	}
};

/**
 * Get list of commands available on this surface.
 *
 * @return {string[]} Commands
 */
ve.ui.Surface.prototype.getCommands = function () {
	return this.commands;
};

/**
 * Execute an action or command.
 *
 * @method
 * @param {ve.ui.Trigger|string} triggerOrAction Trigger or symbolic name of action
 * @param {string} [method] Action method name
 * @param {...Mixed} [args] Additional arguments for action
 * @return {boolean} Action or command was executed
 */
ve.ui.Surface.prototype.execute = function ( triggerOrAction, method ) {
	var command, obj, ret;

	if ( this.isDisabled() ) {
		return;
	}

	if ( triggerOrAction instanceof ve.ui.Trigger ) {
		command = this.triggerListener.getCommandByTrigger( triggerOrAction.toString() );
		if ( command ) {
			// Have command call execute with action arguments
			return command.execute( this );
		}
	} else if ( typeof triggerOrAction === 'string' && typeof method === 'string' ) {
		// Validate method
		if ( ve.ui.actionFactory.doesActionSupportMethod( triggerOrAction, method ) ) {
			// Create an action object and execute the method on it
			obj = ve.ui.actionFactory.create( triggerOrAction, this );
			ret = obj[ method ].apply( obj, Array.prototype.slice.call( arguments, 2 ) );
			return ret === undefined || !!ret;
		}
	}
	return false;
};

/**
 * Execute a command by name
 *
 * @param {string} commandName Command name
 * @return {boolean} The command was executed
 */
ve.ui.Surface.prototype.executeCommand = function ( commandName ) {
	var command = this.commandRegistry.lookup( commandName );
	if ( command ) {
		return command.execute( this );
	}
	return false;
};

/**
 * Set the current height of the toolbar.
 *
 * Used for scroll-into-view calculations.
 *
 * @param {number} toolbarHeight Toolbar height
 */
ve.ui.Surface.prototype.setToolbarHeight = function ( toolbarHeight ) {
	this.toolbarHeight = toolbarHeight;
};

/**
 * Create a progress bar in the progress dialog
 *
 * @param {jQuery.Promise} progressCompletePromise Promise which resolves when the progress action is complete
 * @param {jQuery|string|Function} label Progress bar label
 * @return {jQuery.Promise} Promise which resolves with a progress bar widget and a promise which fails if cancelled
 */
ve.ui.Surface.prototype.createProgress = function ( progressCompletePromise, label ) {
	var progressBarDeferred = $.Deferred();

	this.progresses.push( {
		label: label,
		progressCompletePromise: progressCompletePromise,
		progressBarDeferred: progressBarDeferred
	} );

	this.showProgressDebounced();

	return progressBarDeferred.promise();
};

ve.ui.Surface.prototype.showProgress = function () {
	var dialogs = this.dialogs,
		progresses = this.progresses;

	dialogs.openWindow( 'progress', { progresses: progresses } );
	this.progresses = [];
};

/**
 * Get sanitization rules for rich paste
 *
 * @return {Object} Import rules
 */
ve.ui.Surface.prototype.getImportRules = function () {
	return this.importRules;
};

/**
 * Surface 'dir' property (GUI/User-Level Direction)
 *
 * @return {string} 'ltr' or 'rtl'
 */
ve.ui.Surface.prototype.getDir = function () {
	return this.$element.css( 'direction' );
};

ve.ui.Surface.prototype.initFilibuster = function () {
	var surface = this;
	this.filibuster = new ve.Filibuster()
		.wrapClass( ve.EventSequencer )
		.wrapNamespace( ve.dm, 've.dm', [
			// blacklist
			ve.dm.LinearSelection.prototype.getDescription,
			ve.dm.TableSelection.prototype.getDescription,
			ve.dm.NullSelection.prototype.getDescription
		] )
		.wrapNamespace( ve.ce, 've.ce' )
		.wrapNamespace( ve.ui, 've.ui', [
			// blacklist
			ve.ui.Surface.prototype.startFilibuster,
			ve.ui.Surface.prototype.stopFilibuster
		] )
		.setObserver( 'dm doc', function () {
			return JSON.stringify( ve.Filibuster.static.clonePlain(
				surface.model.documentModel.data.data
			) );
		} )
		.setObserver( 'dm selection', function () {
			var selection = surface.model.selection;
			if ( !selection ) {
				return 'null';
			}
			return selection.getDescription();
		} )
		.setObserver( 'DOM doc', function () {
			return ve.serializeNodeDebug( surface.view.$element[ 0 ] );
		} )
		.setObserver( 'DOM selection', function () {
			var nativeRange,
				nativeSelection = surface.view.nativeSelection;
			if ( nativeSelection.rangeCount === 0 ) {
				return 'null';
			}
			nativeRange = nativeSelection.getRangeAt( 0 );
			return JSON.stringify( {
				startContainer: ve.serializeNodeDebug( nativeRange.startContainer ),
				startOffset: nativeRange.startOffset,
				endContainer: (
					nativeRange.startContainer === nativeRange.endContainer ?
					'(=startContainer)' :
					ve.serializeNodeDebug( nativeRange.endContainer )
				),
				endOffset: nativeRange.endOffset
			} );
		} );
};

ve.ui.Surface.prototype.startFilibuster = function () {
	if ( !this.filibuster ) {
		this.initFilibuster();
	} else {
		this.filibuster.clearLogs();
	}
	this.filibuster.start();
};

ve.ui.Surface.prototype.stopFilibuster = function () {
	this.filibuster.stop();
};

/**
 * Get the name of the dialog this surface is in
 *
 * @return {string} The name of the dialog this surface is in
 */
ve.ui.Surface.prototype.getInDialog = function () {
	return this.inDialog;
};
