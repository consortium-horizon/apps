<?php
require_once dirname(__FILE__).'/accesscheck.php';

# interface functions


class UIPanel {
  private $header = '';
  private $nav = '';
  private $content = '';
  private $id = '';

  function UIPanel($header,$content,$nav = '') {
    $this->header = $header;
    $this->nav = $nav;
    $this->content = $content;
  }

  function setID($id) {
    $this->id = $id;
  }

  function display() {
    $html = '<div class="panel"';
    if (!empty($this->id)) {
      $html .= ' id="'.$this->id.'"';
    }
    $html .= '>';
    $html .= '<div class="header"><h2>'.$this->header.'</h2>';
    $nav = '';
    if ($this->nav) {
      $html .= $this->nav;
    }
    $html .= '</div><!-- ENDOF .header -->';
    $html .= '
<div class="content">

    '.$this->content.'
  </div><!-- ENDOF .content -->';
    $html .= '
<div class="footer">
      '.$this->nav.'
  </div><!-- ENDOF .footer -->
</div><!-- ENDOF .panel -->
    ';
    return $html;
  }
}

class WebblerListing {
  var $title;
  var $help;
  var $elements = array();
  var $columns = array();
  var $sortby = array();
  var $sort = 0;
  var $sortcolumn = '';
  var $buttons = array();
  var $submitbuttons = array();
  var $initialstate = "block";
  var $duplicatebuttons = array();
  var $buttonduplicate = 0;
  private $useShader = true;
  private $usePanel = false;
  private $panelNav = '';
  private $insideNav = '';
  private $suppressHeader = false;
  private $suppressGreenline = false;
  private $buttonsOutsideTable = false;

  function WebblerListing($title,$help = "") {
    $this->title = strip_tags($title);
    $this->help = strip_tags($help);
    ## in phpList don't use the shader
    if (!defined('IN_WEBBLER') && !defined('WEBBLER')) {
      $this->noShader();
      $this->usePanel();
      $this->suppressGreenline();
      $this->buttonsOutsideTable = true;
    }
  }
  
  function noShader() {
    $this->useShader = false;
  }

  function noHeader() {
    $this->suppressHeader = true;
  }

  function suppressGreenline() {
    $this->suppressGreenline = true;
  }

  function usePanel($nav = '') {
    $this->insideNav = $nav;
    $this->usePanel = true;
  }

  function addElement($name,$url = "",$colsize="") {
    if (!isset($this->elements[$name])) {
      $this->elements[$name] = array(
        "name" => $name,
        "url" => $url,
        "colsize" => $colsize,
        "columns" => array(),
        "rows" => array(),
        "class" => "",
      );
    }
  }

  function setClass($name,$class) {
    $this->elements[$name]['class'] = $class;
  }

  function deleteElement($name) {
    unset($this->elements[$name]);
  }

  function addSort() {
    $this->sort = 1;
  }

  function sortBy($colname,$direction = 'desc') {
    $this->sortcolumn = $colname;
  }

  function addColumn($name,$column_name,$value,$url="",$align="") {
    if (!isset($name))
      return;
    $this->columns[$column_name] = $column_name;
        $this->sortby[$column_name] = $column_name;
    # @@@ should make this a callable function
    $this->elements[$name]["columns"]["$column_name"] = array(
      "value" => $value,
      "url" => $url,
      "align"=> $align,
    );
  }

  function renameColumn($oldname,$newname) {
    $this->columns[$oldname] = $newname;
  }

  function deleteColumn($colname) {
    unset($this->columns[$colname]);
  }

  function removeGetParam($remove) {
    $res = "";
    foreach ($_GET as $key => $val) {
      if ($key != $remove) {
        $res .= "$key=".urlencode($val)."&amp;";
      }
    }
    return $res;
  }

  function addRow($name,$row_name,$value,$url="",$align="",$class="") {
    if (!isset($name))
      return;
    $this->elements[$name]["rows"]["$row_name"] = array(
      "name" => $row_name,
      "value" => $value,
      "url" => $url,
      "align"=> $align,
      "class"=> $class,
    );
  }

  function addInput ($name,$value) {
    $this->addElement($name);
    $this->addColumn($name,"value",
      sprintf('<input type="text" name="%s" value="%s" size="40" class="listinginput" />',
      mb_strtolower($name),$value));
  }

  function addButton($name,$url) {
    $this->buttons[$name] = $url;
  }
  
  function addSubmitButton($name,$label) {
    $this->submitbuttons[$name] = $label;
  }

  function duplicateButton($name,$rows) {
    $this->duplicatebuttons[$name] = array(
      "button" => $name,
      "rows" => $rows,
      "rowcount" => 1
    );
    $this->buttonduplicate = 1;
  }

  function listingStart($class="") {
    return '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="listing '.$class.'">';
  }

  function listingHeader() {
    $tophelp = '';
    if (!sizeof($this->columns)) {
      $tophelp = $this->help;
    }
    $html = '<tr valign="top">';
    $html .= sprintf('<th><a name="%s"></a><div class="listinghdname">%s%s</div></th>',str_replace(" ","_",htmlspecialchars(mb_strtolower($this->title))),$tophelp,$this->title);
    $c = 1;
    foreach ($this->columns as $column => $columnname) {
      if ($c == sizeof($this->columns)) {
        $html .= sprintf('<th><div class="listinghdelement">%s%s</div></th>',$columnname,$this->help);
      } else {
        if ($this->sortby[$columnname] && $this->sort) {
          $display = sprintf('<a href="./?%s&amp;sortby=%s" title="%s">%s</a>',$this->removeGetParam("sortby"),urlencode($columnname),sprintf($GLOBALS['I18N']->get('sort by %s'),$columnname),$columnname);
        } else {
          $display = $columnname;
        }
        $html .= sprintf('<th><div class="listinghdelement">%s</div></th>',$display);
      }
      $c++;

    }
  #  $html .= sprintf('<td align="right"><span class="listinghdelementright">%s</span></td>',$lastelement);
    $html .= '</tr>';
    return $html;
  }

  function listingElement($element) {
    if (!empty($element["colsize"]))
      $width = 'width='.$element["colsize"];
    else 
      $width = "";
    if (isset($element['class'])) {
      $html = '<tr class="'.$element['class'].'">';
    } else {
      $html = '<tr>';
    }

    if (!empty($element["url"])) {
      $html .= sprintf('<td %s class="listingname"><span class="listingname"><a href="%s" class="listingname" title="%s">%s</a></span></td>',$width,$element["url"],htmlspecialchars(strip_tags($element["name"])),$element["name"]);
    } else {
      $html .= sprintf('<td %s class="listingname"><span class="listingname">%s</span></td>',$width,$element["name"]);
    }
    foreach ($this->columns as $column) {
      if (isset($element["columns"][$column]) && $element["columns"][$column]["value"]) {
        $value = $element["columns"][$column]["value"];
      } else {
        $value = $column;
      }
      if (isset($element["columns"][$column]) && $element["columns"][$column]["align"]) {
        $align = $element["columns"][$column]["align"];
      } else {
        $align = '';
      }
      if (!empty($element["columns"][$column]["url"])) {
        $html .= sprintf('<td class="listingelement%s"><span class="listingelement%s"><a href="%s" class="listingelement" title="%s">%s</a></span></td>',$align,$align,$element["columns"][$column]["url"],htmlspecialchars($value),$value);
      } elseif (isset($element["columns"][$column])) {
        $html .= sprintf('<td class="listingelement%s"><span class="listingelement%s">%s</span></td>',$align,$align,$element["columns"][$column]["value"]);
      } else {
        $html .= sprintf('<td class="listingelement%s"><span class="listingelement%s">%s</span></td>',$align,$align,'');
      }
    }
    $html .= '</tr>';
    foreach ($element["rows"] as $row) {
      if ($row["value"]) {
        $value = $row["value"];
      } else {
        $value = "";
      }
      if (isset($row["align"])) {
        $align = $row["align"];
      } else {
        $align = 'left';
      }
      if (!empty($row["url"])) {
        $html .= sprintf('<tr class="rowelement %s"><td class="listingrowname">
          <span class="listingrowname"><a href="%s" class="listinghdname" title="%s">%s</a></span>
          </td><td class="listingelement%s" colspan="%d">
          <span class="listingelement%s">%s</span>
          </td></tr>',$row["class"],$row["url"],htmlspecialchars($row["name"]),$row["name"],$align,sizeof($this->columns),$align,$value);
      } else {
        $html .= sprintf('<tr class="rowelement %s"><td class="listingrowname">
          <span class="listingrowname">%s</span>
          </td><td class="listingelement%s" colspan="%d">
          <span class="listingelement%s">%s</span>
          </td></tr>',$row["class"],$row["name"],$align,sizeof($this->columns),$align,$value);
      }
    }
    if (!$this->suppressGreenline) {
      $html .= sprintf('<!--greenline start-->
        <tr>
        <td colspan="%d" bgcolor="#CCCC99"><img height="1" alt="" src="images/transparent.png" width="1" border="0" /></td>
        </tr>
        <!--greenline end-->
      ',sizeof($this->columns)+2);
    }
    $this->buttonduplicate = 1;
    if ($this->buttonduplicate) {
      $buttons = '';
      foreach ($this->duplicatebuttons as $key => $val) {
        $this->duplicatebuttons[$key]['rowcount']++;
        if ($val['rowcount'] >= $val['rows']) {
          if ($this->buttons[$val['button']]) {
            $buttons .= sprintf('<a class="button" href="%s">%s</a>',$this->buttons[$val['button']],strtoupper($val['button']));
          }
          $this->duplicatebuttons[$key]['rowcount'] = 1;
        }
      }
      if ($buttons) {
          $html .= sprintf('
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="%d" align="right">%s</td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        ',sizeof($this->columns)+2,$buttons);
      }
    }

    return $html;
  }

  function listingEnd() {
    $html = '';$buttons = "";
    if (sizeof($this->buttons)) {
      foreach ($this->buttons as $button => $url) {
        $buttons .= sprintf('<a class="button" href="%s">%s</a>',$url,strtoupper($button));
      }
      if (!$this->buttonsOutsideTable) {
        $html .= sprintf('
      <tr><td colspan="2">&nbsp;</td></tr>
      <tr><td colspan="%d" align="right">%s</td></tr>
      <tr><td colspan="2">&nbsp;</td></tr>
      ',sizeof($this->columns)+2,$buttons);
      }
    }
    $submitbuttons = '';
    if (sizeof($this->submitbuttons)) {
      foreach ($this->submitbuttons as $name => $label) {
        $submitbuttons .= sprintf('<button type="submit" name="%s">%s</button>',$name,strtoupper($label));
      }
      if (!$this->buttonsOutsideTable) {
        $html .= sprintf('
      <tr><td colspan="2">&nbsp;</td></tr>
      <tr><td colspan="%d" align="right">%s</td></tr>
      <tr><td colspan="2">&nbsp;</td></tr>
      ',sizeof($this->columns)+2,$submitbuttons);
      }
    }
    $html .= '</table>';
    if ($this->buttonsOutsideTable) {
      $html .= $buttons.$submitbuttons;
    }
      
    return $html;
  }

  function index() {
    return '<a name="top">Index:</a><br />';
  }

  function cmp($a,$b) {
    if (isset($_GET["sortby"])) {
      $sortcol = urldecode($_GET["sortby"]);
    } elseif (!empty($this->sortcolumn)) {
      $sortcol = $this->sortcolumn;
    }
    if (!is_array($a) || !is_array($b)) return 0;
    $val1 = strip_tags($a["columns"][$sortcol]["value"]);
    $val2 = strip_tags($b["columns"][$sortcol]["value"]);
    if ($val1 == $val2) return 0;
    return $val1 < $val2 ? -1 : 1;
  }

  function collapse() {
    $this->initialstate = "none";
  }

  function display($add_index = 0, $class = "") {
    $html = "";
    if (!sizeof($this->elements))
      return "";
#   if ($add_index)
#     $html = $this->index();

    $html .= $this->listingStart($class);
    if (!empty($this->insideNav)) {
      $html .= sprintf('<tr><td colspan="%d">%s</td></tr>',sizeof($this->columns)+1,$this->insideNav);
    }
    if (!$this->suppressHeader) {
      $html .= $this->listingHeader();
    }

#    global $float_menu;
#    $float_menu .= "<a style=\"display: block;\" href=\"#".htmlspecialchars($this->title)."\">$this->title</a>";
    if ($this->sort) {
      usort($this->elements,array($this,"cmp"));
    }
    if ($this->sortcolumn) {
      usort($this->elements,array($this,"cmp"));
    }

    foreach ($this->elements as $element) {
      $html .= $this->listingElement($element);
    }
    $html .= $this->listingEnd();

    if ($this->usePanel) {
      $p = new UIPanel($this->title,$html,$this->panelNav);
      return $p->display();
    }
    
    if (!$this->useShader) {
      return $html;
    }

    $shader = new WebblerShader($this->title);
    $shader->addContent($html);
    $shader->display = $this->initialstate;
    $html = $shader->shaderStart();
    $html .= $shader->header();
    $html .= $shader->dividerRow();
    $html .= $shader->contentDiv();
    $html .= $shader->footer();
    return $html;
  }

  function plainText($text) {
    $text = strip_tags($text);
    $text = str_ireplace('&nbsp;',' ',$text);
    return $text;
  }

  function tabDelimited() {
    print $this->title."\t";
    foreach ($this->columns as $column => $columnname) {
      print $this->plainText($column) . "\t";
    }
    print "\n";
    foreach ($this->elements as $element) {
      print $this->plainText($element['name'])."\t";
      foreach ($this->columns as $column) {
        if (isset($element["columns"][$column]) && $element["columns"][$column]["value"]) {
          print $this->plainText($element["columns"][$column]["value"]);
        }
        print "\t";
      }
      print "\n";
    }
    exit;
  }
}

class WebblerListing2 extends WebblerListing {

  function listingStart($class="") {
    return '<div class="listing '.$class.'">';
  }

  function listingHeader() {
    return '
<div class="header"> 	
	<a href="#">Test list d2e53c3bd01</a>     
<input type="text" name="listorder[3]" value="1" size="5">     
</div><!--ENDOF .header -->      ';
    $tophelp = '';
    if (!sizeof($this->columns)) {
      $tophelp = $this->help;
    }
    $html = '<tr valign="top">';
    $html .= sprintf('<th><a name="%s"></a><div class="listinghdname">%s%s</div></th>',str_replace(" ","_",htmlspecialchars(mb_strtolower($this->title))),$tophelp,$this->title);
    $c = 1;
    foreach ($this->columns as $column => $columnname) {
      if ($c == sizeof($this->columns)) {
        $html .= sprintf('<th><div class="listinghdelement">%s%s</div></th>',$columnname,$this->help);
      } else {
        if ($this->sortby[$columnname] && $this->sort) {
          $display = sprintf('<a href="./?%s&amp;sortby=%s" title="sortby">%s</a>',$this->removeGetParam("sortby"),urlencode($columnname),$columnname);
        } else {
          $display = $columnname;
        }
        $html .= sprintf('<th><div class="listinghdelement">%s</div></th>',$display);
      }
      $c++;

    }
  #  $html .= sprintf('<td align="right"><span class="listinghdelementright">%s</span></td>',$lastelement);
    $html .= '</tr>';
    return $html;
  }

  function listingElement($element) {
/*
    return '<div class="column members">     
	<a href="#"><span class="label">Members</span> <span class="value">55425</span></a>
	<a class="button" href="#" title="add">Add</a>
</div><!--ENDOF .column --> ';
*/
    
    if (!empty($element["colsize"]))
      $width = 'width='.$element["colsize"];
    else 
      $width = "";
    if (isset($element['class'])) {
      $html = '<tr class="'.$element['class'].'">';
    } else {
      $html = '<tr>';
    }

    $html = '<div class="column '.$element['class'].'">';

    foreach ($this->columns as $column) {
      if (isset($element["columns"][$column]) && $element["columns"][$column]["value"]) {
        $value = $element["columns"][$column]["value"];
      } else {
        $value = $column;
      }
      if (!empty($element["columns"][$column]["url"])) {
        $url = $element["columns"][$column]["url"];
      } else {
        $url = '#';
      }
      $html .= sprintf('
          <a href="%s" title="%s">
            <span class="label">%s</span>
            <span class="value">%s</span>
          </a>',$url,htmlspecialchars(strip_tags($value)),$column,$value);
    }
    return $html;
    foreach ($element["rows"] as $row) {
      if ($row["value"]) {
        $value = $row["value"];
      } else {
        $value = "";
      }
      if (isset($row["align"])) {
        $align = $row["align"];
      } else {
        $align = 'left';
      }
      if (!empty($row["url"])) {
        $html .= sprintf('<tr><td class="listingrowname">
          <span class="listingrowname"><a href="%s" class="listinghdname" title="%s">%s</a></span>
          </td><td class="listingelement%s" colspan="%d">
          <span class="listingelement%s">%s</span>
          </td></tr>',$row["url"],htmlspecialchars(strip_tags($row["name"])),$row["name"],$align,sizeof($this->columns),$align,$value);
      } else {
        $html .= sprintf('<tr><td class="listingrowname">
          <span class="listingrowname">%s</span>
          </td><td class="listingelement%s" colspan="%d">
          <span class="listingelement%s">%s</span>
          </td></tr>',$row["name"],$align,sizeof($this->columns),$align,$value);
      }
    }
    $this->buttonduplicate = 1;
    if ($this->buttonduplicate) {
      $buttons = '';
      foreach ($this->duplicatebuttons as $key => $val) {
        $this->duplicatebuttons[$key]['rowcount']++;
        if ($val['rowcount'] >= $val['rows']) {
          if ($this->buttons[$val['button']]) {
            $buttons .= sprintf('<a class="button" href="%s">%s</a>',$this->buttons[$val['button']],strtoupper($val['button']));
          }
          $this->duplicatebuttons[$key]['rowcount'] = 1;
        }
      }
      if ($buttons) {
          $html .= sprintf('
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="%d" align="right">%s</td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        ',sizeof($this->columns)+2,$buttons);
      }
    }

    return $html;
  }

  function listingEnd() {
    return '</div><!--ENDOF .listing -->  ';
    $html = '';$buttons = "";
    if (sizeof($this->buttons)) {
      foreach ($this->buttons as $button => $url) {
        $buttons .= sprintf('<a class="button" href="%s">%s</a>',$url,strtoupper($button));
      }
      $html .= sprintf('
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="%d" align="right">%s</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    ',sizeof($this->columns)+2,$buttons);
    }
    $buttons = '';
    if (sizeof($this->submitbuttons)) {
      foreach ($this->submitbuttons as $name => $label) {
        $buttons .= sprintf('<button type="submit" name="%s">%s</button>',$name,strtoupper($label));
      }
      $html .= sprintf('
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="%d" align="right">%s</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    ',sizeof($this->columns)+2,$buttons);
    }
    $html .= '</table>';
    return $html;
  }
}
 	
        
         
/*
<div class="column bounces">
	<a href="#"><span class="label">Bounces</span>
	<span class="value">10910</span></a>
</div><!--ENDOF .column -->
<div class="column settings">
	<div class="listingfield">
	<label for="checkbox" class="inline">Public</label>
	<input type="checkbox" name="active[3]" value="1" checked="checked">
	</div>
	<div class="listingfield">
	<span class="label inline">Owner</span> <span class="title">Admin<span class="title"> 	
	</div>     
</div><!--ENDOF .settings -->
<div class="content">
<p>This is a text description of this list decribing it in as much detail as required by the administartor that creates the list</p> 	
</div><!--ENDOF .content --> 

*/

class DomTab {

  var $tabs = array();
  var $domtabcluster = '';

  function DomTab($name = '') {
    $this->domtabcluster = $name;
  }

  function addTab($title,$content) {
    $this->tabs[strip_tags($title)] = $content;
  }

  function header() {
    return '
	<script type="text/javascript">
		document.write(\'<style type="text/css">\');    
		document.write(\'div.domtab div{display:visible;}<\');
		document.write(\'/s\'+\'tyle>\');    
    </script>
  ';
  }  

  function display() {
    $html = '
      <div class="domtab">
        <ul class="domtabs">
        ';
    foreach ($this->tabs as $title => $content) {
      $html .= sprintf('<li><a href="#%s" title="%s">%s</a></li>',$this->domtabcluster.urlencode(mb_strtolower($title)),htmlspecialchars($title),$title);
    }
    $html .= '</ul>';

    foreach ($this->tabs as $title => $content) {
      $html .= '<div style="display: none;">';
      $html .= sprintf('<h4><a name="%s" id="%s"><span class="hide">%s</span></a></h4>',$this->domtabcluster.mb_strtolower($title),$this->domtabcluster.urlencode(strtolower($title)),$title);
      $html .= $content;
      $html .= '</div>';
    }
    $html .= '</div>';
    return $this->header().$html;
  }
}


class topBar {
  var $type = '';

  function topBar($type) {
    $this->type = $type;
  }

  function display($lid,$bid) {
    if ($this->type == "admin") {
      return $this->adminBar($lid,$bid);
    } else {
      return $this->defaultBar();
    }
  }

  function defaultBar() {
    return '';
  }

  function adminBar($lid,$bid) {
    global $config;
    $uri = "http://".$config["websiteurl"].'/?lid='.$lid.'&validate=1';
    if ($config["validator"] && in_array($_SESSION["me"]["loginname"],$config["validator_users"])) {
      $validate = sprintf ('<li><a href="http://%s/check?uri=%s" target="_validate" title="use this link to validate this page" target="_validate">validate</a></li>',
      $config["validator"],urlencode($uri));
    } else {
      $validate = '';
    }
    return '
<STYLE TYPE="text/css">

#adminnavcontainer {margin: 0 0 0 0; padding: 0; background-color: #DEDEB6; position: absolute; top: 0px; left:0px; z-index: 2000;}

div.adminwebblerid { float: right; margin: 3px 5px 0 0;
padding: 0;
font: 11px arial, sans-serif;
font-weight: bold;
color: #C2C283
}

#adminnavcontainer div {padding: 15px 0 0 0;}
#adminnavcontainer ul {margin: 0 0 0 0; padding: 10px 0 0 0 ;}
#adminnavlist
{
padding: 3px 0 3px 0;
margin-left: 0;
border-bottom: 2px solid #CCCC99;
font: 11px arial, sans-serif;
}

#adminnavlist li
{
list-style: none;
margin: 0;
display: inline;
}

#adminnavlist li a
{
padding: 3px 0.5em;
margin-left: 3px;
border: 1px solid #CCCC99;
border-bottom: none;
background: #FFCC66;
text-decoration: none;
}

#adminnavlist li a:link,
#adminnavlist li a:visited { color: #000; }

#adminnavlist li a:hover
{
color: #000;
background: #CCCC99;
border-color: #CCCC99;
}
#unhideadminbar {
  text-align: right;
  display: block;
  text-decoration: none;
}
</style>

<script language="Javascript" type="text/javascript" src="/codelib/js/cookielib.js"></script>
<script language="Javascript" type="text/javascript">
var expDays = 3;
var exp = new Date();
exp.setTime(exp.getTime() + (expDays*24*60*60*1000));

function hideadminbar() {
  if (document.getElementById) {
    var el = document.getElementById(\'adminnavcontainer\');
    el.style.visibility="hidden";
  } else {
    alert("To hide the bar, you need to logout");
  }
}

function unhideadminbar() {
  if (document.getElementById) {
    var el = document.getElementById(\'adminnavcontainer\');
    el.style.visibility="visible";
  }
  SetCookie("webbleradminbar","",exp);
}

function closeadminbar() {
  if (document.getElementById) {
    var el = document.getElementById(\'adminnavcontainer\');
    document.getElementById(\'adminnavcontainer\').style.visibility="hidden";
    SetCookie("webbleradminbar","closed",exp);
  } else {
    alert("To hide the bar, you need to logout");
  }
}
</script>

<div id="adminnavcontainer">
<div class="adminwebblerid">[webbler admin bar]</div>
<div></div>
<ul id="adminnavlist">
<li>&nbsp;</li>
<li><a href="/'.$config["uploader_dir"]."/?page=edit&b=$bid&id=$lid".'" title="use this link to edit this page">edit page</a></li>
<li><a href="/'.$config["uploader_dir"].'/?page=list&id='.$bid.'" title="use this link to edit this branch">branch</a></li>
<li><a href="/'.$config["uploader_dir"].'/?page=sitemap" title="use this link to view the sitemap">sitemap</a></li>
<li><a href="/'.$config["uploader_dir"].'/" title="use this link to go to the webbler admin homepage">webbler home</a></li>
'.$validate.'
<li><a href="/'.$config["uploader_dir"]."/?page=logout&return=".urlencode("lid=$lid").'" title="use this link to logout of the webbler">logout</a></li>
<li><a href="javascript:hideadminbar();" title="use this link to hide this admin bar on this page">hide bar</a></li>
<li><a href="javascript:closeadminbar();" title="use this link to hide the admin bar for this session">close bar</a></li>
<li>&nbsp;TEMPLATE:&nbsp; <b>'.getLeafTemplate($lid).'</b></li>
</ul>

</div>
<script language="Javascript" type="text/javascript">
var state = GetCookie("webbleradminbar");
if (state == "closed") {
  hideadminbar();
  document.write(\'<div id="unhideadminbar"><a href="#" onclick="unhideadminbar()">&sect;</a></div>\');
}

</script>
';
  }
}

class WebblerTabs {
  private $tabs = array();
  private $tablabels = array();
  private $current = "";
  private $previous = "";
  private $next = "";
  private $linkcode = "";
  private $liststyle = 'ul';
  private $addTabNo = false;
  private $class = '';
  private $addprevnext = false;
  private $id = 'webblertabs';

  function addTab($label,$url = "",$name = '') {
    if (empty($name)) {
      $name = $label;
    }
    $this->tabs[$name] = $url;
    $this->tablabels[$name] = $label;
  }

  function addPrevNext() {
    $this->addprevnext = true;
  }

  function addTabNo() {
    $this->addTabNo = true;
  }

  function listStyle($style) {
    $this->liststyle = $style;
  }

  function setId($id) {
    $this->id = $id;
  }

  function tabTitle() {
    return $this->current;
  }

  function setListClass($class) {
    $this->class = $class;
  }

  function setCurrent($name) {
    $this->current = mb_strtolower($name);
  }

  function previousLink() {
    if (!empty($this->previous)) {
      return sprintf('<a href="%s" %s title="%s">',$this->tabs[$this->previous],$this->linkcode,$GLOBALS['I18N']->get('Previous'));
    }
    return '';
  }
  
  function previous() {
    if (!empty($this->previous)) {
      return $this->tabs[$this->previous];
    }
    return '';
  }

  function next() {
    if (!empty($this->next)) {
      return $this->tabs[$this->next];
    }
    return '';
  }

  function addLinkCode($code) {
    $this->linkcode = $code;
  }

  function prevNextNav() {
    $html = '<div class="step-nav">';

    $previousTab = $this->previous();
    $nextTab = $this->next();
    if (!empty($previousTab)) {
      $html .= '<a class="back savechanges" href="'.$previousTab.'">'.$GLOBALS['I18N']->get('Back').'</a>';
    } else {
    #  $html .= '<a class="back">'.$GLOBALS['I18N']->get('Back').'</a>';
    }
    if (!empty($nextTab)) {
      $html .= '<a class="next savechanges" href="'.$nextTab.'">'.$GLOBALS['I18N']->get('Next').'</a>';
    } else {
    #  $html .= '<a class="next">'.$GLOBALS['I18N']->get('Next').'</a>';
    }
    $html .= '</div>';
    return $html;
  }

  function display() {
    $html = '';
    if (empty($GLOBALS['design']) && empty($GLOBALS['ui'])) {
      $html = '<style type=text/css media=screen>@import url( styles/tabs.css );</style>';
    }
    $html .= '<div id="'.$this->id.'">';
    $html .= '<'.$this->liststyle;
    if (!empty($this->class)) {
      $html .= ' class="'.$this->class.'"';
    }
    $html .= '>';
    reset($this->tabs);
    $previous = $next = "";
    $gotcurrent = false;
    $count = 0;
    foreach ($this->tabs as $tab => $url) {
      $count++;
      if (mb_strtolower(strip_tags($tab)) == $this->current) {
        $this->previous = $previous;
        $gotcurrent = true;
        $html .= '<li class="current" id="'.$count.'">';
      } else {
        if ($gotcurrent && empty($this->next)) {
          $this->next = $tab;
        }
        $html .= '<li>';
      }
      $html .= sprintf('<a href="%s" %s title="%s">',$url,$this->linkcode,htmlspecialchars(strip_tags($this->tablabels[$tab])));
      if ($this->addTabNo) {
        $html .= sprintf('<span class="tabno">%d</span> ',$count);
      }
      $html .= sprintf('<span class="title">%s</span></a>',ucfirst($this->tablabels[$tab]));
      $html .= '</li>';
      $previous = $tab;
    }
    $html .= '</'.$this->liststyle.'>';

    if ($this->addprevnext) {
      $html .= $this->prevNextNav();
    }
    $html .= '</div>';

#    $html .= '<span class="faderight">&nbsp;</span>';
  #  $html .= '<br clear="all" />';
    return $html;
 }
}

class pageInfo {
  private $noteid = '';
  private $ajaxed = false;
  private $page = '';
  private $infocontent = '';
  private $addhide = true;

  function pageInfo($id = '') {
    $this->ajaxed = isset($_GET['ajaxed']);
    $this->noteid = $id;
    $this->page = $GLOBALS['page'];
  }

  function setContent($content) {
    $this->infocontent = $content;
  }

  function suppressHide() {
    $this->addhide = false;
  }

  function fetchInfoContent($include) {
    ## pages to not allow hiding the info for
    if (in_array($include,array('login.php','logout.php'))) {
      $this->addhide = false;
    }
    ## import has too much in the info and needs replacing
    if (in_array($include,array('import.php'))) {
      return '';
    }
    ## community should not show in the global help, but main page
    if (in_array($include,array('community.php'))) {
      return '';
    }    
    $this->noteid = substr(md5(basename($include,'.php')),0,15);
    $this->page = $this->noteid;
    $buffer = ob_get_contents();
    ob_end_clean();
    ob_start();

    # include some information
    if (empty($_GET['pi'])) {
      if (is_file("info/".$_SESSION['adminlanguage']['info']."/$include")) {
        @include "info/".$_SESSION['adminlanguage']['info']."/$include";
      } elseif (is_file("info/en/$include")) {
        @include "info/en/$include";
      } else {
        print $buffer;
        return '';#'No file: '."info/en/$include";
      }

    } elseif (isset($_GET['pi']) && !empty($GLOBALS['plugins'][$_GET['pi']]) && is_object($GLOBALS['plugins'][$_GET['pi']])) {
      if (is_file($GLOBALS['plugins'][$_GET['pi']]->coderoot.'/info/'.$_SESSION['adminlanguage']['info']."/$include")) {
        @include $GLOBALS['plugins'][$_GET['pi']]->coderoot .'/info/'.$_SESSION['adminlanguage']['info']."/$include";
      }
    } elseif (is_file("info/en/$include")) {
      @include "info/en/$include";
    #  print "Not a file: "."info/".$adminlanguage["info"]."/$include";
    } else {
      print $buffer;
      return '';
    }
    $this->infocontent = ob_get_contents();
    ob_end_clean();
    ob_start();
    print $buffer;
  }
  
  function show() {
    $html = '';
    if ($this->ajaxed || !empty($_SESSION['suppressinfo'][$this->noteid])) {
      return '';
    }
    if (empty($this->infocontent)) return '';
    if (isset($_GET['action']) && $_GET['action'] == 'hidenote' && isset($_GET['note']) && $_GET['note'] == $this->noteid) {
      if (!isset($_SESSION['suppressinfo']) || !is_array($_SESSION['suppressinfo'])) {
        $_SESSION['suppressinfo'] = array();
      }
      $_SESSION['suppressinfo'][$this->noteid] = 'hide';
    }
    
    $html = '<div class="note '.$this->noteid.'">';
    if ($this->addhide) {
      $html .= 
      '<a href="./?page='
      .$GLOBALS['page']
      .'&amp;action=hidenote&amp;note='
      .$this->noteid
      .'" class="hide ajaxable" title="'
      .$GLOBALS['I18N']->get('Close this box')
      .'">'
      .$GLOBALS['I18N']->get('Hide')
      .'</a>';
    }
    $html .= $this->infocontent;
    $html  .= '</div>'; ## end of info div
    return $html;
  }
  
  public function content() {
    /* return current content, and then clear it, so it won't show twice */
    $return = $this->infocontent;
    $this->infocontent = '';
    return $return;
  }
}

class button {
  protected $link;
  protected $title;
  protected $linktext;
  protected $linkhtml = '';
  protected $js = '';

  function button($link,$linktext,$title = '') {
    $this->link = $link;
    $this->linktext = $linktext;
    $this->title = $title;
  }

  function showA() {
    $html = '<a href="'.$this->link.'" '.$this->linkhtml;
    if ($this->title) {
      $html .= ' title="'.htmlspecialchars($this->title).'"';
    } else {
      $html .= ' title="'.htmlspecialchars($this->linktext).'"';
    }
    $html .= '>';
    $html .= $this->linktext;
    return $html;
  }

  function showAend() {
    $html = '</a>'.$this->js;
    return $html;
  }

  function show() {
    return $this->showA().$this->showAend();
  }
}

class confirmButton extends button {
  protected $link;
  protected $title;
  protected $linktext;
  protected $linkhtml = '';
  protected $js;
  
  function confirmButton($confirmationtext,$link,$linktext,$title = '') {
    if (!isset($GLOBALS['confirmButtonCount'])) $GLOBALS['confirmButtonCount'] = 0;
    $GLOBALS['confirmButtonCount']++;
    $this->linkhtml = ' class="confirm" id="confirm'.$GLOBALS['confirmButtonCount'].'"';
    $this->link = '#';
    $this->linktext = $linktext;
    $GLOBALS['pagefooter']['confirmbutton'.$GLOBALS['confirmButtonCount']] = '
    <script type="text/javascript">
    $("#confirm'.$GLOBALS['confirmButtonCount'].'").click(function(){ if (confirm("'.htmlspecialchars(strip_tags($confirmationtext)).'")){window.location="'.$link.'"};});
    </script>';
    
    $this->title = $title;
  }
}

class buttonGroup {
  private $buttons = array();
  private $topbutton = '';

  function buttonGroup($topbutton = '') {
    $this->topbutton = $topbutton;
  }

  function addButton($button) {
    if (empty($this->topbutton)) {
      $this->topbutton = $button;
    } else {
      $this->buttons[] = $button;
    }
  }

  function show() {
    $html = '<div class="dropButton">';

    $html .= $this->topbutton->showA();

    if (sizeof($this->buttons)) {
      $html .= '<img height="18" width="18" align="top" class="arrow" src="ui/'.$GLOBALS['ui'].'/images/menuarrow.png" />';
    }
    
    $html .= $this->topbutton->showAend();
    
    if (sizeof($this->buttons)) {
      $html .= '<div class="submenu" style="display: none;">';

      foreach ($this->buttons as $button) {
        $html .= $button->show();
      }

      $html .= '</div>';
    }

    $html .= '</div>';
    return $html;
  }
}

class WebblerShader {
  var $name = "Untitled";
  var $content = "";
  var $num = 0;
  var $isfirst = 0;
  var $display = "block";
  var $initialstate = "open";

  function WebblerShader($name) {
    $this->name = $name;
    if (!isset($GLOBALS["shadercount"])) {
      $GLOBALS["shadercount"] = 0;
      $this->isfirst = 1;
    }
    $this->num = $GLOBALS["shadercount"];
    $GLOBALS["shadercount"]++;
  }

  function addContent($content) {
    $this->content = $content;
  }

  function hide() {
    $this->display = 'none';
  }

  function show() {
    $this->display = 'block';
  }

  function shaderJavascript() {
    if ($_SERVER["QUERY_STRING"]) {
      $cookie = "WS?".$_SERVER["QUERY_STRING"];
    } else {
      $cookie = "WS";
    }
    if (!isset($_COOKIE[$cookie])) {
      $_COOKIE[$cookie] = '';
    }

    return '
  <script language="Javascript" type="text/javascript">

  <!--
  var states = Array("'.join('","',explode(",",$_COOKIE[$cookie])).'");
  var cookieloaded = 0;
  var expireDate = new Date;
  expireDate.setDate(expireDate.getDate()+365);

  function cookieVal(cookieName) {
    var thisCookie = document.cookie.split("; ")
    for (var i = 0; i < thisCookie.length; i++) {
      if (cookieName == thisCookie[i].split("=")[0]) {
        return thisCookie[i].split("=")[1];
      }
    }
    return 0;
  }

  function saveStates() {
    document.cookie = "WS"+escape(this.location.search)+"="+states+";expires=" + expireDate.toGMTString();
  }

  var agt = navigator.userAgent.toLowerCase();
  var is_major = parseInt(navigator.appVersion);
  var is_nav = ((agt.indexOf(\'mozilla\') != -1) && (agt.indexOf(\'spoofer\') == -1) && (agt.indexOf(\'compatible\') == -1) && (agt.indexOf(\'opera\') == -1) && (agt.indexOf(\'webtv\') == -1));
  var is_nav4up = (is_nav && (is_major >= 4));
  var is_ie = (agt.indexOf("msie") != -1);
  var is_ie3  = (is_ie && (is_major < 4));
  var is_ie4  = (is_ie && (is_major == 4) && (agt.indexOf("msie 5") == -1) && (agt.indexOf("msie 6") == -1));
  var is_ie4up = (is_ie && (is_major >= 4));
  var is_ie5up  = (is_ie  && !is_ie3 && !is_ie4);
  var is_mac = (agt.indexOf("mac") != -1);
  var is_gecko = (agt.indexOf("gecko") != -1);
  var view;

  function getItem (id) {
    var view;
    if (is_ie4) {
      view = eval(id);
    }
    if (is_ie5up || is_gecko) {
      view = document.getElementById(id);
    }
    return view;
  }

  function shade(id) {
    if(is_ie4up || is_gecko) {

      var shaderDiv = getItem(\'shader\'+id);
      var shaderSpan = getItem(\'shaderspan\'+id);
  //    var shaderImg = getItem(\'shaderimg\'+id);
      var shaderImg = false;
      var footerTitle = getItem(\'title\'+id);
      if(shaderDiv.style.display == \'block\') {
        states[id] = "closed";
        shaderDiv.style.display = \'none\';
        shaderSpan.innerHTML = \'<span class="shadersmall">'.$GLOBALS['I18N']->get('open').'&nbsp;</span><img alt="" src="images/shaderdown.gif" height="9" width="9" border="0" />\';
        footerTitle.style.visibility = \'visible\';
        if (shaderImg)
          shaderImg.src = \'images/expand.gif\';
      } else {
        states[id] = "open";
        shaderDiv.style.display = \'block\';
        footerTitle.style.visibility = \'hidden\';
        shaderSpan.innerHTML = \'<span class="shadersmall">'.$GLOBALS['I18N']->get('close').'&nbsp;</span><img alt="" src="images/shaderup.gif" height="9" width="9" border="0" />\';
        if (shaderImg)
          shaderImg.src = \'images/collapse.gif\';
      }
    }
    saveStates();
  }

  function getPref(number) {
    if (states[number] == "open") {
      return "block";
    } else if (states[number] == "closed") {
      return "none";
    }
    return "";
  }

  function start_div(number, default_status) {
    if (is_ie4up || is_gecko) {
      var pref = getPref(number);
      if (pref) {
        default_status = pref;
      }

      document.writeln("<div id=\'shader" + number + "\' name=\'shader" + number + "\' class=\'shader\' style=\'display: " + default_status + ";\'>");
    }
  }


  function end_div(number, default_status) {
    if (is_ie4up || is_gecko) {
      document.writeln("</div>");
    }
  }
  var title_text = "";
  var span_text = "";
  var title_class = "";

  function open_span(number, default_status) {
    if (is_ie4up || is_gecko) {
      var pref = getPref(number);
      if (pref) {
        default_status = pref;
      }
      if(default_status == \'block\') {
        span_text = \'<span class="shadersmall">'.$GLOBALS['I18N']->get('close').'&nbsp;</span><img src="images/shaderup.gif" alt="" height="9" width="9" border="0" />\';
      } else {
        span_text = \'<span class="shadersmall">'.$GLOBALS['I18N']->get('open').'&nbsp;</span><img src="images/shaderdown.gif" alt="" height="9" width="9" border="0" />\';
      }
      document.writeln("<a href=\'javascript: shade(" + number + ");\'><span id=\'shaderspan" + number + "\' class=\'shadersmalltext\'>" + span_text + "</span></a>");
    }
  }

  function title_span(number,default_status,title) {
    if (is_ie4up || is_gecko) {
      var pref = getPref(number);
      if (pref) {
        default_status = pref;
      }
      if(default_status == \'none\') {
        title_text = \'<img src="images/expand.gif" alt="" height="9" width="9" border="0" />  \'+title;
        title_class = "shaderfootertextvisible";
      } else {
        title_text = \'<img src="images/collapse.gif" alt="" height="9" width="9" border="0" />   \'+title;
        title_class = "shaderfootertexthidden";
      }
      document.writeln("<a href=\'javascript: shade(" + number + ");\'><span id=\'title" + number + "\' class=\'"+title_class+"\'>" + title_text + "</span></a>");
    }
  }
//-->
</script>
    ';
  }

  function header() {
    $html = sprintf('
<div class="tablewrapper">
<table width="98%%" align="center" cellpadding="0" cellspacing="0" border="0">');
    return $html;
  }

  function shadeIcon() {
    return sprintf('
<a href="javascript:shade(%d);" style="text-decoration:none;">&nbsp;<img id="shaderimg%d" src="images/collapse.gif" alt="" height="9" width="9" border="0" />
    ',$this->num,$this->num);
  }

  function titleBar() {
    return sprintf('
  <tr>
      <td colspan="4" class="shaderheader">%s
          <span class="shaderheadertext">&nbsp;%s</span>
         </a>
    </td>
  </tr>',$this->shadeIcon(),$this->name);
  }

  function dividerRow() {
    return '
  <tr>
      <td colspan="4" class="shaderdivider"><img src="images/transparent.png" height="1" alt="" border="0" width="1" /></td>
  </tr>
    ';
  }

  function footer() {
    $html = sprintf('

  <tr>
    <td class="shaderborder"><img src="images/transparent.png" alt="" height="1" border="0" width="1" /></td>
    <td class="shaderfooter"><script language="javascript"  type="text/javascript">title_span(%d,\'%s\',\'%s\');</script>&nbsp;</td>
    <td class="shaderfooterright"><script language="javascript" type="text/javascript">open_span(%d,\'%s\');</script>&nbsp;</td>
    <td class="shaderborder"><img src="images/transparent.png" alt="" height="1" border="0" width="1" /></td>
  </tr>
'.$this->dividerRow().'
</table><!-- End table from header -->
</div><!-- End tablewrapper -->
    ',$this->num,$this->display,addslashes($this->name),$this->num,$this->display);
    return $html;
  }

  function contentDiv() {
    $html = sprintf('
  <tr>
      <td class="shaderdivider"><img src="images/transparent.png" alt="" height="1" border="0" width="1" /></td>
      <td colspan="2">
      <script language="javascript" type="text/javascript">start_div(%d,\'%s\')</script>',$this->num,$this->display);
    $html .= $this->content;

    $html .= '
    <script language="javascript" type="text/javascript">end_div();</script>
    </td>

    <td class="shaderdivider"><img src="images/transparent.png" alt="" height="1" border="0" width="1" /></td>
  </tr>';
    return $html;
  }

  function shaderStart() {
    if (!isset($GLOBALS["shaderJSset"])) {
      $html = $this->shaderJavascript();
      $GLOBALS["shaderJSset"] = 1;
    } else {
      $html = "";
    }
    return $html;
  }

  function display() {
    $html = $this->shaderStart();
    $html .= $this->header();
    $html .= $this->titleBar();
    $html .= $this->dividerRow();
    $html .= $this->contentDiv();
    $html .= $this->footer();
    return $html;
  }

}

?>
