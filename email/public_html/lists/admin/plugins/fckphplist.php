<?php

/*
 * plugin for phpList to provide the FCKeditor in the compose campaign page
 * 
 * works with the FCKeditor version 2.6.8
 * 
 */


class fckphplist extends phplistPlugin {
  public $name = "FCKeditor plugin for phpList";
  public $coderoot = "fckphplist/";
  public $editorProvider = true;
  public $version = "0.1";
  public $authors = 'Michiel Dethmers';
  public $enabled = 1;
  public $description = 'The original WYSIWYG editor for phpList';
  
  public $settings = array(
    "fckeditor_width" => array (
      'value' => 600,
      'description' => 'Width in px of FCKeditor Area',
      'type' => "integer",
      'allowempty' => 0,
      'min' => 100,
      'max' => 800,
      'category'=> 'composition',
    ),
    "fckeditor_height" => array (
      'value' => 600,
      'description' => 'Height in px of FCKeditor Area',
      'type' => "integer",
      'allowempty' => 0,
      'min' => 100,
      'max' => 800,
      'category'=> 'composition',
    ),
    "fckeditortoolbar_row2" => array (
      'value' => '',
      'description' => 'Second row of toolbar elements in the editor',
      'type' => "text",
      'allowempty' => 1,
      'category'=> 'composition',
    ),
    "fckeditor_path" => array (
      'value' => 'plugins/fckphplist/fckeditor/',
      'description' => 'Public path to the FCKeditor',
      'type' => "text",
      'allowempty' => 0,
      'category'=> 'composition',
    ),
  );

  function fckphplist() {
    parent::phplistplugin();
    $this->coderoot = dirname(__FILE__).'/fckphplist/';
  }

  function adminmenu() {
    return array(
    );
  }
  
  function editor($fieldname,$content) {
    if (!is_file($this->coderoot.'/fckeditor/fckeditor.php')) {
      return '<textarea name="'.$fieldname.'">'.htmlspecialchars($content).'</textarea>';
    }
    include_once $this->coderoot.'/fckeditor/fckeditor.php';
    if (!class_exists('FCKeditor')) return 'Editor class not found';
    $oFCKeditor = new FCKeditor($fieldname) ;
    $fckPath = getConfig("fckeditor_path");
    $oFCKeditor->BasePath = $fckPath;
    $oFCKeditor->ToolbarSet = 'Default' ;
    $oFCKeditor->Value = $content;
    $w = getConfig("fckeditor_width");
    $h = getConfig("fckeditor_height");
    if (isset($_SESSION["fckeditor_height"])) {
      $h = sprintf('%d',$_SESSION["fckeditor_height"]);
    }

    # for version 2.0
    if ($h < 400) {
      $h = 400;
    }
    $oFCKeditor->Height = $h;
    $oFCKeditor->Width = $w;
    return $oFCKeditor->CreateHtml();
  }

}
