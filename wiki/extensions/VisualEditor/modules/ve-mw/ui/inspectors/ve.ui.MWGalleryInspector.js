/*!
 * VisualEditor UserInterface MWGalleryInspector class.
 *
 * @copyright 2011-2016 VisualEditor Team and others; see AUTHORS.txt
 * @license The MIT License (MIT); see LICENSE.txt
 */

/**
 * Inspector for editing MediaWiki galleries.
 *
 * @class
 * @extends ve.ui.MWExtensionInspector
 *
 * @constructor
 * @param {Object} [config] Configuration options
 */
ve.ui.MWGalleryInspector = function VeUiMWGalleryInspector() {
	// Parent constructor
	ve.ui.MWGalleryInspector.super.apply( this, arguments );

	this.$element.addClass( 've-ui-mwGalleryInspector' );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWGalleryInspector, ve.ui.MWExtensionInspector );

/* Static properties */

ve.ui.MWGalleryInspector.static.name = 'gallery';

ve.ui.MWGalleryInspector.static.size = 'large';

ve.ui.MWGalleryInspector.static.title =
	OO.ui.deferMsg( 'visualeditor-mwgalleryinspector-title' );

ve.ui.MWGalleryInspector.static.modelClasses = [ ve.dm.MWGalleryNode ];

/* Methods */

/** */
ve.ui.MWGalleryInspector.prototype.getInputPlaceholder = function () {
	// 'File:' is always in content language
	return mw.config.get( 'wgFormattedNamespaces' )[ mw.config.get( 'wgNamespaceIds' ).file ] + ':' +
		ve.msg( 'visualeditor-mwgalleryinspector-placeholder' );
};

/* Registration */

ve.ui.windowFactory.register( ve.ui.MWGalleryInspector );
