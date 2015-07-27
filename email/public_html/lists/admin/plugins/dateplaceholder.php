<?php

/**
 * date placeholder plugin
 * 
 * Add date related placeholders to phpList
 * @author Michiel Dethmers
 * 
 * v 0.1 22 June 2015
 * 
 * License GPLv3+
 */ 


class dateplaceholder extends phplistPlugin {
  public $name = "Date placeholder";
  public $coderoot = 'dateplaceholder/';
  public $version = "0.1";
  public $authors = 'Michiel Dethmers';
  public $enabled = 1;
  public $description = 'Adds date related placeholders';
  public $documentationUrl = 'https://resources.phplist.com/plugin/dateplaceholder';
  public $commandlinePluginPages= array (
  );

  public $settings = array(
    "dateplaceholder_defaultdateformat" => array (
      'value' => "Y-m-d",
      'description' => 'Default date format for date related placeholders',
      'type' => "text",
      'allowempty' => 1,
      'category'=> 'general',
    ),
  );
  
  private $datePlaceholders = array(
    'TODAY' => array(0,0,0,0,0,0), // offsets for day, month, year, hour minute second
    'YESTERDAY' => array(-1,0,0,0,0,0), 
    'TOMORROW' => array(1,0,0,0,0,0),
    'NEXTWEEK' => array(7,0,0,0,0,0),
    'LASTWEEK' => array(-7,0,0,0,0,0),
    'NEXTMONTH' => array(0,1,0,0,0,0),
    'LASTMONTH' => array(0,-1,0,0,0,0),
    // expand here
  );
  
  private $moment = array();

  function __construct() {
    parent::phplistplugin();
  }
 
  function upgrade($previous) {
    parent::upgrade($previous);
    return true;
  }
  
  function sendReport($subject,$message)
  {
   # return true;
  }
  
  function momentInTime($offSets = array()) {
      if (empty($this->moment[0])) {
          $this->moment[0] = date('d');
          $this->moment[1] = date('m');
          $this->moment[2] = date('Y');
          $this->moment[3] = date('H');
          $this->moment[4] = date('i');
          $this->moment[5] = date('s');
      }
      return mktime(
        $this->moment[3] + $offSets['hour'],
        $this->moment[4] + $offSets['minute'],
        $this->moment[5] + $offSets['second'], 
        $this->moment[1] + $offSets['month'], 
        $this->moment[0] + $offSets['day'], 
        $this->moment[2] + $offSets['year']
      );          
  }
  
  function dateReplacement($placeholder,$format) {
      if (empty($format)) {
          $format = getConfig('dateplaceholder_defaultdateformat');
      }
      $placeholder = strtoupper($placeholder);

      $momentInTime = $this->momentInTime(
        array(
        'day' => $this->datePlaceholders[$placeholder][0],
        'month' => $this->datePlaceholders[$placeholder][1],
        'year' => $this->datePlaceholders[$placeholder][2],
        'hour' => $this->datePlaceholders[$placeholder][3],
        'minute' => $this->datePlaceholders[$placeholder][4],
        'second' => $this->datePlaceholders[$placeholder][5]
        )
      );
      return date($format,$momentInTime);
  }
  
  function parseAll($placeholder,$text) {
      preg_match_all('/\['.strtoupper($placeholder).':?(.*)\]/',$text,$matches);
      for ($i = 0; $i<sizeof($matches[0]); $i++) {          
          $text = str_replace($matches[0][$i],$this->dateReplacement($placeholder,$matches[1][$i]),$text);
      }
      return $text;
    }
      
  
  function dateParse($text) 
  {
      
      foreach (array_keys($this->datePlaceholders) as $plH) {
         $text = $this->parseAll($plH,$text);
      } 
      
      return $text;
  }
  
  function parseOutgoingTextMessage($messageid,$textmessage,$destinationemail, $userdata = NULL) 
  {
      return $this->dateParse($textmessage);
  }
  
  function parseOutgoingHTMLMessage($messageid,$htmlmessage,$destinationemail, $userdata = NULL) 
  {
      return $this->dateParse($htmlmessage);
  }

  	public function messageHeaders($mail)
  	{
        $mail->Subject = $this->dateParse($mail->Subject);
  		return array(); 
  	}
  

}
