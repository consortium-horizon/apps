/*!
 * VisualEditor user interface MWTemplatePage class.
 *
 * @copyright 2011-2016 VisualEditor Team and others; see AUTHORS.txt
 * @license The MIT License (MIT); see LICENSE.txt
 */

/**
 * MediaWiki transclusion dialog template page.
 *
 * @class
 * @extends OO.ui.PageLayout
 *
 * @constructor
 * @param {ve.dm.MWTemplateModel} template Template model
 * @param {string} name Unique symbolic name of page
 * @param {Object} [config] Configuration options
 */
ve.ui.MWTemplatePage = function VeUiMWTemplatePage( template, name, config ) {
	var title;

	// Configuration initialization
	config = ve.extendObject( {
		scrollable: false
	}, config );

	// Parent constructor
	OO.ui.PageLayout.call( this, name, config );

	// Properties
	this.template = template;
	this.spec = template.getSpec();
	this.$more = $( '<div>' );
	this.$description = $( '<div>' );
	this.removeButton = new OO.ui.ButtonWidget( {
		framed: false,
		icon: 'remove',
		title: ve.msg( 'visualeditor-dialog-transclusion-remove-template' ),
		flags: [ 'destructive' ],
		classes: [ 've-ui-mwTransclusionDialog-removeButton' ]
	} )
		.connect( this, { click: 'onRemoveButtonClick' } );
	this.infoFieldset = new OO.ui.FieldsetLayout( {
		label: this.spec.getLabel(),
		icon: 'template'
	} );
	this.addButton = new OO.ui.ButtonWidget( {
		framed: false,
		icon: 'parameter',
		label: ve.msg( 'visualeditor-dialog-transclusion-add-param' ),
		tabIndex: -1
	} )
		.connect( this, { click: 'onAddButtonFocus' } );

	// Initialization
	this.$description.addClass( 've-ui-mwTemplatePage-description' );
	if ( this.spec.getDescription() ) {
		this.$description.text( this.spec.getDescription() );
	} else {
		title = this.template.getTitle();
		// The transcluded page may be dynamically generated or unspecified in the DOM
		// for other reasons (bug 66724). In that case we can't tell the user what
		// the template is called nor link to the template page.
		if ( title ) {
			title = mw.Title.newFromText( title );
		}
		if ( title ) {
			this.$description
				.addClass( 've-ui-mwTemplatePage-description-missing' )
				.html( mw.message(
					'visualeditor-dialog-transclusion-no-template-description',
					title.getPrefixedText(),
					// FIXME: Drop this no-longer needed second parameter after January 2016
					mw.user
				).parse() )
				.find( 'a' ).attr( 'target', '_blank' );
		}
	}

	this.infoFieldset.$element.append( this.$description );
	this.$more
		.addClass( 've-ui-mwTemplatePage-more' )
		.append( this.addButton.$element );
	this.$element
		.addClass( 've-ui-mwTemplatePage' )
		.append( this.infoFieldset.$element, this.removeButton.$element, this.$more );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWTemplatePage, OO.ui.PageLayout );

/* Methods */

/**
 * @inheritdoc
 */
ve.ui.MWTemplatePage.prototype.setOutlineItem = function ( outlineItem ) {
	// Parent method
	OO.ui.PageLayout.prototype.setOutlineItem.call( this, outlineItem );

	if ( this.outlineItem ) {
		this.outlineItem
			.setIcon( 'template' )
			.setMovable( true )
			.setRemovable( true )
			.setLabel( this.spec.getLabel() );
	}
};

ve.ui.MWTemplatePage.prototype.onRemoveButtonClick = function () {
	this.template.remove();
};

ve.ui.MWTemplatePage.prototype.onAddButtonFocus = function () {
	this.template.addParameter( new ve.dm.MWParameterModel( this.template ) );
};
