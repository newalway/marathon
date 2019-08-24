/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.config.allowedContent = true;

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	//config.uiColor = '#AADC6E';
	//config.enterMode = CKEDITOR.ENTER_BR;

  CKEDITOR.addCss( "@font-face {font-family: 'trirong';src: url('/fonts/trirong-regular.woff2') format('woff2'),url('/fonts/trirong-regular.woff') format('woff'),url('/fonts/trirong-regular.ttf') format('truetype'),url('/fonts/trirong-regular.svg#trirongregular') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face { font-family: 'kanit';src: url('/fonts/kanit-regular.woff2') format('woff2'),url('/fonts/kanit-regular.woff') format('woff'),url('/fonts/kanit-regular.ttf') format('truetype'),url('/fonts/kanit-regular.svg#kanitregular') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face {font-family: 'helvetica';src: url('/fonts/Helvetica.woff2') format('woff2'),url('/fonts/Helvetica.woff') format('woff'),url('/fonts/Helvetica.ttf') format('truetype'),url('/fonts/Helvetica.svg#Helvetica') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face {font-family: 'waffle';src: url('/fonts/waffle_regular.woff2') format('woff2'),url('/fonts/waffle_regular.woff') format('woff'),url('/fonts/waffle_regular.ttf') format('truetype'),url('/fonts/waffle_regular.svg#waffle_regularregular') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face {font-family: 'teddy';src:  url('/fonts/teddy.woff') format('woff'),url('/fonts/teddy.ttf') format('truetype'),url('/fonts/teddy.svg') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face {font-family: 'thaisans_neue';src: url('/fonts/thaisansneue-regular.woff2') format('woff2'),url('/fonts/thaisansneue-regular.woff') format('woff'),url('/fonts/thaisansneue-regular.ttf') format('truetype'),url('/fonts/thaisansneue-regular.svg#thaisans_neueregular') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face {font-family: 'kruengprung';src: url('/fonts/kruengprung.woff2') format('woff2'),url('/fonts/kruengprung.woff') format('woff'),url('/fonts/kruengprung.ttf') format('truetype'),url('/fonts/kruengprung.svg#kruengprungregular') format('svg');font-weight: normal;font-style: normal;}" );
  CKEDITOR.addCss( "@font-face {font-family: 'itim';src: url('/fonts/itim-regular.woff2') format('woff2'),url('/fonts/itim-regular.woff') format('woff'),url('/fonts/itim-regular.ttf') format('truetype'),url('/fonts/itim-regular.svg#itimregular') format('svg');font-weight: normal;font-style: normal;}" );

  config.font_names = 'Helvetica/Helvetica;' +
    'Itim/Itim;'+
    'Kruengprung/Kruengprung;'+
    'Kanit/Kanit;'+
    'Trirong/Trirong;' +
    'Teddy/Teddy;'+
    'Thaisans_neue/Thaisans_neue;'+
    'Waffle/Waffle;'+ config.font_names;

  //CKEDITOR.addCss( "@font-face {font-family: 'Trirong';font-style: normal;400: normal;src: local('Trirong'), url('https://fonts.googleapis.com/css?family=Trirong') format('truetype');}" );
  //CKEDITOR.addCss( '.cke_editable h1 { border-bottom: 1px dotted red; }' );
  //CKEDITOR.addCss( '.cke_editable h2,.cke_editable h3 { border-bottom: 1px dotted red; }' );

  //config.contentsCss = window.CKEDITOR_BASEPATH + '/stylesheets/custom/ckeditor_contents.css';
  //config.contentsCss = '../../stylesheets/custom/ckeditor_contents.css';
  //config.contentsCss = CKEDITOR.basePath + '../../stylesheets/custom/ckeditor_contents.css';

  /*
  myFonts = ['Trirong', 'Kanit'];
  //config.font_names = 'serif;sans serif;monospace;cursive;fantasy';
  for(var i = 0; i<myFonts.length; i++){
    config.font_names = config.font_names+';'+myFonts[i];
    myFonts[i] = 'http://fonts.googleapis.com/css?family='+myFonts[i].replace(' ','+');

    $("head").append("<link rel='stylesheet' href='"+ myFonts[i] +"'>");
  }
  config.contentsCss = ['/stylesheets/custom/ckeditor_contents.css'].concat(myFonts);
  */
};
