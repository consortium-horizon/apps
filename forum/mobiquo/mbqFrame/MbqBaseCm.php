<?php

defined('MBQ_IN_IT') or exit;

/**
 * common method base class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Abstract Class MbqBaseCm {
  
    public function __construct() {
    }
    
    /**
     * transform timestamp to iso8601 format
     */
    public function datetimeIso8601Encode() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * get short content
     *
     * @param  String  $str
     * @param  Integer  $length
     * @return  String
     */
    public function getShortContent($str, $length = 200) {
        /* get short content standard code begin */
        $str = preg_replace('/\<font [^\>]*?\>(.*?)\<\/font\>/is', '$1', $str);
        $str = preg_replace('/\<font\>(.*?)\<\/font\>/is', '$1', $str);
        $str = preg_replace('/\[quote[^\]]*?\].*?\[\/quote\]/is', '[quote]', $str);
        $str = preg_replace_callback('/\[url\=(.*?)\](.*?)\[\/url\]/is', create_function('$matches','return ($matches[1] == $matches[2]) ? "[url]" : $matches[2];'), $str);
        $str = preg_replace('/\[url\](.*?)\[\/url\]/is', '[url]', $str);
        $str = preg_replace_callback('/\[email\=(.*?)\](.*?)\[\/email\]/is', create_function('$matches','return ($matches[1] == $matches[2]) ? "[url]" : $matches[2];'), $str);
        $str = preg_replace('/\[email\](.*?)\[\/email\]/is', '[url]', $str);
        $str = preg_replace_callback('/\[iurl\=(.*?)\](.*?)\[\/iurl\]/is', create_function('$matches','return ($matches[1] == $matches[2]) ? "[url]" : $matches[2];'), $str);
        $str = preg_replace('/\[iurl\](.*?)\[\/iurl\]/is', '[url]', $str);
        $str = preg_replace('/\[img[^\]]*?\].*?\[\/img\]/is', '[img]', $str);
        $str = preg_replace('/\[video[^\]]*?\].*?\[\/video\]/is', '[V]', $str);
        $str = preg_replace('/\[flash[^\]]*?\].*?\[\/flash\]/is', '[V]', $str);
        $str = preg_replace('/\[media[^\]]*?\].*?\[\/media\]/is', '[V]', $str);
        $str = preg_replace('/\[attachment[^\]]*?\].*?\[\/attachment\]/is', '[attach]', $str);
        $str = preg_replace('/\[attach[^\]]*?\].*?\[\/media\]/is', '[attach]', $str);
        $str = preg_replace('/\[php[^\]]*?\].*?\[\/php\]/is', '[php]', $str);
        $str = preg_replace('/\[html[^\]]*?\].*?\[\/html\]/is', '[html]', $str);
        $str = preg_replace('/\[spoiler[^\]]*?\].*?\[\/spoiler\]/is', '[spoiler]', $str);
        $str = preg_replace('/\[thread[^\]]*?\].*?\[\/thread\]/is', '[thread]', $str);
        $str = preg_replace('/\[topic[^\]]*?\].*?\[\/topic\]/is', '[topic]', $str);
        $str = preg_replace('/\[post[^\]]*?\].*?\[\/post\]/is', '[post]', $str);
        $str = preg_replace('/\[ftp[^\]]*?\].*?\[\/ftp\]/is', '[ftp]', $str);
        $str = preg_replace('/\[sql[^\]]*?\].*?\[\/sql\]/is', '[sql]', $str);
        $str = preg_replace('/\[xml[^\]]*?\].*?\[\/xml\]/is', '[xml]', $str);
        $str = preg_replace('/\[hide[^\]]*?\].*?\[\/hide\]/is', '[hide]', $str);
        $str = preg_replace('/\[confidential[^\]]*?\].*?\[\/confidential\]/is', '[hide]', $str);
        $str = preg_replace('/\[ebay[^\]]*?\].*?\[\/ebay\]/is', '[ebay]', $str);
        $str = preg_replace('/\[map[^\]]*?\].*?\[\/map\]/is', '[map]', $str);
        $str = preg_replace('/[\n|\r|\t]/', '', $str);
        //remove useless bbcode begin
        $str = preg_replace_callback('/\[([^\/]*?)\]/i', create_function('$matches','
        $v = strtolower($matches[1]);
        if (strpos($v, "quote") === 0 || strpos($v, "url") === 0 || strpos($v, "img") === 0 || strpos($v, "v") === 0 || strpos($v, "attach") === 0 || strpos($v, "php") === 0 || strpos($v, "html") === 0 || strpos($v, "spoiler") === 0 || strpos($v, "thread") === 0 || strpos($v, "topic") === 0 || strpos($v, "post") === 0 || strpos($v, "ftp") === 0 || strpos($v, "sql") === 0 || strpos($v, "xml") === 0 || strpos($v, "hide") === 0 || strpos($v, "ebay") === 0 || strpos($v, "map") === 0) {
            return "[$matches[1]]";
        } else {
            return "";
        }
        '), $str);
        $str = preg_replace('/\[\/[^\]]*?\]/i', '', $str);
        //remove useless bbcode end
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        $str = function_exists('mb_substr') ? mb_substr($str, 0, $length) : substr($str, 0, $length);
        $str = strip_tags($str);
        /* get short content standard code end */
        return $str;
    }
    
    /**
     * get attachment ids from content
     */
    public function getAttIdsFromContent() {
        MbqError::alert('', __METHOD__ . ',line:' . __LINE__ . '.' . MBQ_ERR_INFO_NEED_ACHIEVE_IN_INHERITED_CLASSE);
    }
    
    /**
     * change script work dir
     *
     * @param  String  $relativePath  .. or folder name separated by / or \. for example:../../folder1/folder2
     * @param  String  $basePath  the base script work dir,default is the mobiquo folder absolute path
     */
    public function changeWorkDir($relativePath, $basePath = MBQ_PATH) {
        chdir($basePath.$relativePath);
    }
    
    /**
     * write log into a file for debug
     */
    public function writeLog($logContent, $add = false) {
        if (defined('MBQ_PATH')) {
            $filePath = MBQ_PATH.'mbqDebug.log';
            if ($add) {
                if ($handle = fopen($filePath, 'ab')) {
                    fwrite($handle, $logContent);
                    fclose($handle);
                }
            } else {
                file_put_contents($filePath, $logContent);
            }
        }
    }
    
    /**
     * write memory log for debug
     */
    public function writeMemLog() {
        if (defined('MBQ_PATH')) {
            $filePath = MBQ_PATH.'mbqDebugMem.log';
            $content = memory_get_usage().','.memory_get_usage(true).','.MbqMain::$cmd."\n";
            if ($handle = fopen($filePath, 'ab')) {
                fwrite($handle, $content);
                fclose($handle);
            }
        }
    }
    
    /**
     * change array leaf value to string
     * now only support 3 dimensional array
     *
     * @param  Array  $arr
     * @return  Array
     */
    public function changeArrValueToString($arr) {
        foreach ($arr as &$v) {
            if (is_array($v)) {
                foreach ($v as &$v1) {
                    if (is_array($v1)) {
                        foreach ($v1 as &$v2) {
                            if (!is_array($v2)) {
                                $v2 = (string) $v2;
                            }
                        }
                    } else {
                        $v1 = (string) $v1;
                    }
                } 
            } else {
                $v = (string) $v;
            }
        }
        return $arr;
    }
    
    /**
     * remove array key
     *
     * @param  Array  $arr
     * @return  Array
     */
    public function removeArrayKey($arr) {
        $retArr = array();
        foreach ($arr as $v) {
            $retArr[] = $v;
        }
        return $retArr;
    }
    
    /**
     * return sql in  string
     *
     * @param  Mixed  $arr
     * @param  Boolean  $addslashesFlag
     * @return  Mixed
     */
    public function getSqlIn($arr, $addslashesFlag = true) {
        $sqlIn = '';
        if (is_array($arr)) {
            if (count($arr) > 0) {
                $flag = true;
                foreach ($arr as $value) {
                    if ($flag) {
                        if ($addslashesFlag)
                            $sqlIn .= "'".addslashes($value)."'";
                        else
                            $sqlIn .= "'$value'";
                        $flag = false;
                    } else {
                        if ($addslashesFlag)
                            $sqlIn .= ", '".addslashes($value)."'";
                        else
                            $sqlIn .= ", '$value'";
                    }
                }
                return $sqlIn;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Get all request headers
     * 
     * @return array
     */
    public function getAllRequestHeaders() {
        static $_cached_headers = false;
        if($_cached_headers !== false) {
            return $_cached_headers;
        }
        $headers = array();
        if(function_exists('getallheaders')) {
            foreach( getallheaders() as $name => $value ) {
                $headers[strtolower($name)] = $value;
            }
        } else {
            foreach($_SERVER as $name => $value) {
                if(substr($name, 0, 5) == 'HTTP_') {
                    $headers[strtolower(str_replace(' ', '-', str_replace('_', ' ', substr($name, 5))))] = $value;
                }
            }
        }
        return $_cached_headers = $headers;
    }
    
    /**
     * Get a request header
     *
     * @param  string $name the requested header title
     * @return string|false
     */
    public function getRequestHeader($name) {
        $headers = $this->getAllRequestHeaders();
        if (isset($headers[strtolower($name)])) {
            return $headers[strtolower($name)];
        }
        return false;
    }
    
    /**
     * mobile color convert
     *
     * @param  String  $color
     * @param  String  $str  content
     */
    public function mbColorConvert($color, $str) {
        static $colorlist;
        if (preg_match('/#[\da-fA-F]{6}/is', $color)) {
            if (!$colorlist) {
                $colorlist = array(
                    '#000000' => 'Black',             '#708090' => 'SlateGray',       '#C71585' => 'MediumVioletRed', '#FF4500' => 'OrangeRed',
                    '#000080' => 'Navy',              '#778899' => 'LightSlateGrey',  '#CD5C5C' => 'IndianRed',       '#FF6347' => 'Tomato',
                    '#00008B' => 'DarkBlue',          '#778899' => 'LightSlateGray',  '#CD853F' => 'Peru',            '#FF69B4' => 'HotPink',
                    '#0000CD' => 'MediumBlue',        '#7B68EE' => 'MediumSlateBlue', '#D2691E' => 'Chocolate',       '#FF7F50' => 'Coral',
                    '#0000FF' => 'Blue',              '#7CFC00' => 'LawnGreen',       '#D2B48C' => 'Tan',             '#FF8C00' => 'Darkorange',
                    '#006400' => 'DarkGreen',         '#7FFF00' => 'Chartreuse',      '#D3D3D3' => 'LightGrey',       '#FFA07A' => 'LightSalmon',
                    '#008000' => 'Green',             '#7FFFD4' => 'Aquamarine',      '#D3D3D3' => 'LightGray',       '#FFA500' => 'Orange',
                    '#008080' => 'Teal',              '#800000' => 'Maroon',          '#D87093' => 'PaleVioletRed',   '#FFB6C1' => 'LightPink',
                    '#008B8B' => 'DarkCyan',          '#800080' => 'Purple',          '#D8BFD8' => 'Thistle',         '#FFC0CB' => 'Pink',
                    '#00BFFF' => 'DeepSkyBlue',       '#808000' => 'Olive',           '#DA70D6' => 'Orchid',          '#FFD700' => 'Gold',
                    '#00CED1' => 'DarkTurquoise',     '#808080' => 'Grey',            '#DAA520' => 'GoldenRod',       '#FFDAB9' => 'PeachPuff',
                    '#00FA9A' => 'MediumSpringGreen', '#808080' => 'Gray',            '#DC143C' => 'Crimson',         '#FFDEAD' => 'NavajoWhite',
                    '#00FF00' => 'Lime',              '#87CEEB' => 'SkyBlue',         '#DCDCDC' => 'Gainsboro',       '#FFE4B5' => 'Moccasin',
                    '#00FF7F' => 'SpringGreen',       '#87CEFA' => 'LightSkyBlue',    '#DDA0DD' => 'Plum',            '#FFE4C4' => 'Bisque',
                    '#00FFFF' => 'Aqua',              '#8A2BE2' => 'BlueViolet',      '#DEB887' => 'BurlyWood',       '#FFE4E1' => 'MistyRose',
                    '#00FFFF' => 'Cyan',              '#8B0000' => 'DarkRed',         '#E0FFFF' => 'LightCyan',       '#FFEBCD' => 'BlanchedAlmond',
                    '#191970' => 'MidnightBlue',      '#8B008B' => 'DarkMagenta',     '#E6E6FA' => 'Lavender',        '#FFEFD5' => 'PapayaWhip',
                    '#1E90FF' => 'DodgerBlue',        '#8B4513' => 'SaddleBrown',     '#E9967A' => 'DarkSalmon',      '#FFF0F5' => 'LavenderBlush',
                    '#20B2AA' => 'LightSeaGreen',     '#8FBC8F' => 'DarkSeaGreen',    '#EE82EE' => 'Violet',          '#FFF5EE' => 'SeaShell',
                    '#228B22' => 'ForestGreen',       '#90EE90' => 'LightGreen',      '#EEE8AA' => 'PaleGoldenRod',   '#FFF8DC' => 'Cornsilk',
                    '#2E8B57' => 'SeaGreen',          '#9370D8' => 'MediumPurple',    '#F08080' => 'LightCoral',      '#FFFACD' => 'LemonChiffon',
                    '#2F4F4F' => 'DarkSlateGrey',     '#9400D3' => 'DarkViolet',      '#F0E68C' => 'Khaki',           '#FFFAF0' => 'FloralWhite',
                    '#2F4F4F' => 'DarkSlateGray',     '#98FB98' => 'PaleGreen',       '#F0F8FF' => 'AliceBlue',       '#FFFAFA' => 'Snow',
                    '#32CD32' => 'LimeGreen',         '#9932CC' => 'DarkOrchid',      '#F0FFF0' => 'HoneyDew',        '#FFFF00' => 'Yellow',
                    '#3CB371' => 'MediumSeaGreen',    '#9ACD32' => 'YellowGreen',     '#F0FFFF' => 'Azure',           '#FFFFE0' => 'LightYellow',
                    '#40E0D0' => 'Turquoise',         '#A0522D' => 'Sienna',          '#F4A460' => 'SandyBrown',      '#FFFFF0' => 'Ivory',
                    '#4169E1' => 'RoyalBlue',         '#A52A2A' => 'Brown',           '#F5DEB3' => 'Wheat',           '#FFFFFF' => 'White',
                    '#4682B4' => 'SteelBlue',         '#A9A9A9' => 'DarkGrey',        '#F5F5DC' => 'Beige',
                    '#483D8B' => 'DarkSlateBlue',     '#A9A9A9' => 'DarkGray',        '#F5F5F5' => 'WhiteSmoke',
                    '#48D1CC' => 'MediumTurquoise',   '#ADD8E6' => 'LightBlue',       '#F5FFFA' => 'MintCream',
                    '#4B0082' => 'Indigo',            '#ADFF2F' => 'GreenYellow',     '#F8F8FF' => 'GhostWhite',
                    '#556B2F' => 'DarkOliveGreen',    '#AFEEEE' => 'PaleTurquoise',   '#FA8072' => 'Salmon',
                    '#5F9EA0' => 'CadetBlue',         '#B0C4DE' => 'LightSteelBlue',  '#FAEBD7' => 'AntiqueWhite',
                    '#6495ED' => 'CornflowerBlue',    '#B0E0E6' => 'PowderBlue',      '#FAF0E6' => 'Linen',
                    '#66CDAA' => 'MediumAquaMarine',  '#B22222' => 'FireBrick',       '#FAFAD2' => 'LightGoldenRodYellow',
                    '#696969' => 'DimGrey',           '#B8860B' => 'DarkGoldenRod',   '#FDF5E6' => 'OldLace',
                    '#696969' => 'DimGray',           '#BA55D3' => 'MediumOrchid',    '#FF0000' => 'Red',
                    '#6A5ACD' => 'SlateBlue',         '#BC8F8F' => 'RosyBrown',       '#FF00FF' => 'Fuchsia',
                    '#6B8E23' => 'OliveDrab',         '#BDB76B' => 'DarkKhaki',       '#FF00FF' => 'Magenta',
                    '#708090' => 'SlateGrey',         '#C0C0C0' => 'Silver',          '#FF1493' => 'DeepPink',
                );
            }
            if (isset($colorlist[strtoupper($color)])) { 
                $color = $colorlist[strtoupper($color)];
            } else {
                $r = hexdec(substr($color, 1, 2)) ? hexdec(substr($color, 1, 2)) : 1;
                $g = hexdec(substr($color, 3, 2)) ? hexdec(substr($color, 3, 2)) : 1;
                $b = hexdec(substr($color, 5, 2)) ? hexdec(substr($color, 5, 2)) : 1;
                $arr = array();
                foreach ($colorlist as $code => $colorName) {
                    $r1 = substr($code, 1, 2) ? hexdec(substr($code, 1, 2)) : 0;
                    $g1 = substr($code, 3, 2) ? hexdec(substr($code, 3, 2)) : 0;
                    $b1 = substr($code, 5, 2) ? hexdec(substr($code, 5, 2)) : 0;
                    $arr[] = array(
                        'rRatio' => $r1 / $r,
                        'gRatio' => $g1 / $g,
                        'bRatio' => $b1 / $b,
                        'colorName' => $colorName,
                        'code' => $code
                    );
                }
                $arrR = array();
                foreach ($arr as $item) {
                    if (!$arrR || (count($arrR) < 30)) {
                        $arrR[] = $item;
                    } else {
                        $key = $this->getTheMostDifferent($arrR, 'rRatio');
                        if (abs($item['rRatio'] - 1) < abs($arrR[$key]['rRatio'] - 1)) {
                            $arrR[$key] = $item;
                        }
                    }
                }
                $arr = $arrR;
                $arrG = array();
                foreach ($arr as $item) {
                    if (!$arrG || (count($arrG) < 15)) {
                        $arrG[] = $item;
                    } else {
                        $key = $this->getTheMostDifferent($arrG, 'gRatio');
                        if (abs($item['gRatio'] - 1) < abs($arrG[$key]['gRatio'] - 1)) {
                            $arrG[$key] = $item;
                        }
                    }
                }
                $arr = $arrG;
                $arrB = array();
                foreach ($arr as $item) {
                    if (!$arrB || (count($arrB) < 8)) {
                        $arrB[] = $item;
                    } else {
                        $key = $this->getTheMostDifferent($arrB, 'bRatio');
                        if (abs($item['bRatio'] - 1) < abs($arrB[$key]['bRatio'] - 1)) {
                            $arrB[$key] = $item;
                        }
                    }
                }
                foreach ($arrB as $item) {
                    if (isset($retItem)) {
                        if (abs($retItem['rRatio'] + $retItem['gRatio'] + $retItem['bRatio'] - 3) > abs($item['rRatio'] + $item['gRatio'] + $item['bRatio'] - 3)) {
                            $retItem = $item;
                        }
                    } else {
                        $retItem = $item;
                    }
                }
                $color = $retItem['colorName'];
            }
        }
        return "<font color=\"$color\">$str</font>";
    }
    
    /**
     * get the most defferent item key with $value
     * this method only uesed for self::mbColorConvert()
     *
     * @param  Array  $items
     * @param  Array  $compareKey
     * @param  Float  $value
     */
    protected function getTheMostDifferent($items, $compareKey, $value = 1) {
        foreach ($items as $key => $item) {
            if (isset($ret)) {
                if (abs($item[$compareKey] - $value) > abs($items[$ret][$compareKey] - $value)) {
                    $ret = $key;
                }
            } else {
                $ret = $key;
            }
        }
        return $ret;
    }
    
    /**
     * get file name extension
     *
     * @param  String  $fileName  file full name
     * @return  String
     */
    public function getFileExtension($fileName) {
        return strtolower(substr(strrchr($fileName, "."), 1));
    }
    
    /**
     * merge api data
     *
     * @param  Array  $apiData
     * @param  Array  $addApiData
     */
    public function mergeApiData(&$apiData, $addApiData) {
        foreach ($addApiData as $k => $v) {
            $apiData[$k] = $v;
        }
    }
    
    /**
     * replace codes in content
     *
     * @param  String  $content
     * @param  String  $strNeedReplaced  separated by |
     * @param  String  $type  replacement type.'bbcodeName' means replace bbcode name for our rules.
     */
    public function replaceCodes($content, $strNeedReplaced, $type = 'bbcodeName') {
        switch ($type) {
            case 'bbcodeName':
                $arr = explode('|', $strNeedReplaced);
                foreach ($arr as $v) {
                    $content = $this->replaceCode($content, $v);
                }
            break;
            default:
            break;
        }
        return $content;
    }
    
    /**
     * replace code in content
     *
     * @param  String  $content
     * @param  String  $strNeedReplaced
     * @param  String  $type  replacement type.'bbcodeName' means replace bbcode name for our rules.
     */
    public function replaceCode($content, $strNeedReplaced = 'quote', $type = 'bbcodeName') {
        switch ($type) {
            case 'bbcodeName':
                switch ($strNeedReplaced) {
                    case 'quote':
                    $newName = MBQ_RUNNING_NAMEPRE.'quote';
                    $content = preg_replace('/\[quote(.*?)\]/i', ",,,,,,,,$newName$1,,,,,,,,", $content);
                    $content = preg_replace('/\[\/quote\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    case 'email':
                    $newName = MBQ_RUNNING_NAMEPRE.'email';
                    $content = preg_replace('/\[email\]/i', ",,,,,,,,$newName,,,,,,,,", $content);
                    $content = preg_replace('/\[\/email\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    case 'ebay':
                    $newName = MBQ_RUNNING_NAMEPRE.'ebay';
                    $content = preg_replace('/\[ebay\]/i', ",,,,,,,,$newName,,,,,,,,", $content);
                    $content = preg_replace('/\[\/ebay\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    case 'map':
                    $newName = MBQ_RUNNING_NAMEPRE.'map';
                    $content = preg_replace('/\[map\]/i', ",,,,,,,,$newName,,,,,,,,", $content);
                    $content = preg_replace('/\[\/map\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    case 'code':
                    $newName = MBQ_RUNNING_NAMEPRE.'code';
                    $content = preg_replace('/\[code\]/i', ",,,,,,,,$newName,,,,,,,,", $content);
                    $content = preg_replace('/\[\/code\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    case 'html':
                    $newName = MBQ_RUNNING_NAMEPRE.'html';
                    $content = preg_replace('/\[html\]/i', ",,,,,,,,$newName,,,,,,,,", $content);
                    $content = preg_replace('/\[\/html\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    case 'php':
                    $newName = MBQ_RUNNING_NAMEPRE.'php';
                    $content = preg_replace('/\[php\]/i', ",,,,,,,,$newName,,,,,,,,", $content);
                    $content = preg_replace('/\[\/php\]/i', ",,,,,,,,/$newName,,,,,,,,", $content);
                    break;
                    default:
                    break;
                }
            break;
            default:
            break;
        }
        return $content;
    }
    
    /**
     * upreplace codes in content
     *
     * @param  String  $content
     * @param  String  $strNeedReplaced  separated by |
     * @param  String  $type  replacement type.'bbcodeName' means replace bbcode name for our rules.
     */
    public function unreplaceCodes($content, $strNeedReplaced, $type = 'bbcodeName') {
        switch ($type) {
            case 'bbcodeName':
                $arr = explode('|', $strNeedReplaced);
                foreach ($arr as $v) {
                    $content = $this->unreplaceCode($content, $v);
                }
            break;
            default:
            break;
        }
        return $content;
    }
    
    /**
     * upreplace code in content
     *
     * @param  String  $content
     * @param  String  $strNeedReplaced
     * @param  String  $type  replacement type.'bbcodeName' means replace bbcode name for our rules.
     */
    public function unreplaceCode($content, $strNeedReplaced = 'quote', $type = 'bbcodeName') {
        switch ($type) {
            case 'bbcodeName':
                switch ($strNeedReplaced) {
                    case 'quote':
                    $curName = MBQ_RUNNING_NAMEPRE.'quote';
                    $content = preg_replace('/,,,,,,,,'.$curName.'(.*?),,,,,,,,/i', "[quote$1]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/quote]", $content);
                    break;
                    case 'email':
                    $curName = MBQ_RUNNING_NAMEPRE.'email';
                    $content = preg_replace('/,,,,,,,,'.$curName.',,,,,,,,/i', "[email]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/email]", $content);
                    break;
                    case 'ebay':
                    $curName = MBQ_RUNNING_NAMEPRE.'ebay';
                    $content = preg_replace('/,,,,,,,,'.$curName.',,,,,,,,/i', "[ebay]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/ebay]", $content);
                    break;
                    case 'map':
                    $curName = MBQ_RUNNING_NAMEPRE.'map';
                    $content = preg_replace('/,,,,,,,,'.$curName.',,,,,,,,/i', "[map]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/map]", $content);
                    break;
                    case 'code':
                    $curName = MBQ_RUNNING_NAMEPRE.'code';
                    $content = preg_replace('/,,,,,,,,'.$curName.',,,,,,,,/i', "[code]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/code]", $content);
                    break;
                    case 'html':
                    $curName = MBQ_RUNNING_NAMEPRE.'html';
                    $content = preg_replace('/,,,,,,,,'.$curName.',,,,,,,,/i', "[html]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/html]", $content);
                    break;
                    case 'php':
                    $curName = MBQ_RUNNING_NAMEPRE.'php';
                    $content = preg_replace('/,,,,,,,,'.$curName.',,,,,,,,/i', "[php]", $content);
                    $content = preg_replace('/,,,,,,,,\/'.$curName.',,,,,,,,/i', "[/php]", $content);
                    break;
                    default:
                    break;
                }
            break;
            default:
            break;
        }
        return $content;
    }
    
    /**
     * get file mime type(Example value: image/png)
     *
     * @param  String  $fileName
     * @return  String
     */
    public function getMimeType($fileName) {
        preg_match("|\.([a-z0-9]{2,4})$|i", $fileName, $fileSuffix);

        switch(strtolower($fileSuffix[1])) {
            case "js" :
                return "application/x-javascript";

            case "json" :
                return "application/json";

            case "jpg" :
            case "jpeg" :
            case "jpe" :
                return "image/jpg";

            case "png" :
            case "gif" :
            case "bmp" :
            case "tiff" :
                return "image/".strtolower($fileSuffix[1]);

            case "css" :
                return "text/css";

            case "xml" :
                return "application/xml";

            case "doc" :
            case "docx" :
                return "application/msword";

            case "xls" :
            case "xlt" :
            case "xlm" :
            case "xld" :
            case "xla" :
            case "xlc" :
            case "xlw" :
            case "xll" :
                return "application/vnd.ms-excel";

            case "ppt" :
            case "pps" :
                return "application/vnd.ms-powerpoint";

            case "rtf" :
                return "application/rtf";

            case "pdf" :
                return "application/pdf";

            case "html" :
            case "htm" :
            case "php" :
                return "text/html";

            case "txt" :
                return "text/plain";

            case "mpeg" :
            case "mpg" :
            case "mpe" :
                return "video/mpeg";

            case "mp3" :
                return "audio/mpeg3";

            case "wav" :
                return "audio/wav";

            case "aiff" :
            case "aif" :
                return "audio/aiff";

            case "avi" :
                return "video/msvideo";

            case "wmv" :
                return "video/x-ms-wmv";

            case "mov" :
                return "video/quicktime";

            case "zip" :
                return "application/zip";

            case "tar" :
                return "application/x-tar";

            case "swf" :
                return "application/x-shockwave-flash";

            default :
                return "unknown/" . trim($fileSuffix[0], ".");
        }
    }
    
    /**
     * add slash for url if it do not end of slash
     *
     * @param  String  $url
     * @return  String
     */
    public function addSlashForUrl($url) {
        if (substr($url, strlen($url) -1, 1) == '/') {
        } else {
            $url .= '/';
        }
        return $url;
    }
    
    /**
     * remove slash for url if it end of slash
     *
     * @param  String  $url
     * @return  String
     */
    public function removeSlashForUrl($url) {
        if (substr($url, strlen($url) -1, 1) == '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        return $url;
    }
    
    /**
     * au_reg_verify
     *
     * @param  String  $token
     * @param  String  $code
     * @param  String  $key  api key
     * @param  String  $forumUrl
     * @return  Mixed
     */
    public function auRegVerify($token, $code, $key, $forumUrl) {   //ref vb3x function_push.php->getEmailFromScription()
        $forumUrl = $this->removeSlashForUrl($forumUrl);
        $auUrl = 'http://directory.tapatalk.com/au_reg_verify.php';
        $verification_url = $auUrl.'?token='.$token.'&'.'code='.$code.'&key='.$key.'&url='.urlencode($forumUrl);
        $response = $this->getContentFromRemoteServer($verification_url, 10, $error);
        if($response)
            $result = json_decode($response, true);
        if(isset($result) && isset($result['result']))
            return $result;
        else
        {
            $data = array(
                'token' => urlencode($token),
                'code'  => urlencode($code),
                'key'   => urlencode($key),
                'url'   => urlencode($forumUrl)
            );
            $response = $this->getContentFromRemoteServer($auUrl, 10, $error, 'POST', $data);
            if($response)
                $result = json_decode($response, true);
            if(isset($result) && isset($result['result']))
                return $result;
            else
                return false; //No connection to Tapatalk Server.
        }
    }
    
    /**
     * Get content from remote server
     *
     * @param string $url      NOT NULL          the url of remote server, if the method is GET, the full url should include parameters; if the method is POST, the file direcotry should be given.
     * @param string $holdTime [default 0]       the hold time for the request, if holdtime is 0, the request would be sent and despite response.
     * @param string $error_msg                  return error message
     * @param string $method   [default GET]     the method of request.
     * @param string $data     [default array()] post data when method is POST.
     *
     * @exmaple: getContentFromRemoteServer('http://push.tapatalk.com/push.php', 0, $error_msg, 'POST', $ttp_post_data)
     * @return string when get content successfully|false when the parameter is invalid or connection failed.
    */
    public function getContentFromRemoteServer($url, $holdTime = 0, &$error_msg, $method = 'GET', $data = array()) {
        //Validate input.
        $vurl = parse_url($url);
        if ($vurl['scheme'] != 'http' && $vurl['scheme'] != 'https')
        {
            $error_msg = 'Error: invalid url given: '.$url;
            return false;
        }
        if($method != 'GET' && $method != 'POST')
        {
            $error_msg = 'Error: invalid method: '.$method;
            return false;//Only POST/GET supported.
        }
        if($method == 'POST' && empty($data))
        {
            $error_msg = 'Error: data could not be empty when method is POST';
            return false;//POST info not enough.
        }
    
        if(!empty($holdTime) && function_exists('file_get_contents') && $method == 'GET')
        {
            $opts = array(
                $vurl['scheme'] => array(
                    'method' => "GET",
                    'timeout' => $holdTime,
                )
            );
    
            $context = stream_context_create($opts);
            $response = file_get_contents($url,false,$context);
        }
        else if (@ini_get('allow_url_fopen'))
        {
            if(empty($holdTime))
            {
                // extract host and path:
                $host = $vurl['host'];
                $path = $vurl['path'];
    
                if($method == 'POST')
                {
                    $fp = fsockopen($host, 80, $errno, $errstr, 5);
    
                    if(!$fp)
                    {
                        $error_msg = 'Error: socket open time out or cannot connet.';
                        return false;
                    }
    
                    $data = http_build_query($data, '', '&');
    
                    fputs($fp, "POST $path HTTP/1.1\r\n");
                    fputs($fp, "Host: $host\r\n");
                    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
                    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
                    fputs($fp, "Connection: close\r\n\r\n");
                    fputs($fp, $data);
                    fclose($fp);
                    return 1;
                }
                else
                {
                    $error_msg = 'Error: 0 hold time for get method not supported.';
                    return false;
                }
            }
            else
            {
                if($method == 'POST')
                {
                    $params = array(
                        $vurl['scheme'] => array(
                            'method' => 'POST',
                            'content' => http_build_query($data, '', '&'),
                        )
                    );
                    $ctx = stream_context_create($params);
                    $old = ini_set('default_socket_timeout', $holdTime);
                    $fp = @fopen($url, 'rb', false, $ctx);
                }
                else
                {
                    $fp = @fopen($url, 'rb', false);
                }
                if (!$fp)
                {
                    $error_msg = 'Error: fopen failed.';
                    return false;
                }
                ini_set('default_socket_timeout', $old);
                stream_set_timeout($fp, $holdTime);
                stream_set_blocking($fp, 0);
    
                $response = @stream_get_contents($fp);
            }
        }
        elseif (function_exists('curl_init'))
        {
            $ch = curl_init($url);
            $data = http_build_query($data, '', '&');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            if($method == 'POST')
            {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            if(empty($holdTime))
            {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT,1);
            }
            $response = curl_exec($ch);
            curl_close($ch);
        }
        else
        {
            $error_msg = 'CURL is disabled and PHP option "allow_url_fopen" is OFF. You can enable CURL or turn on "allow_url_fopen" in php.ini to fix this problem.';
            return false;
        }
        return $response;
    }
    
}
    
/**
 * shutdown handle
 */
function mbqShutdownHandle() {
    $error = error_get_last();
    if(!empty($error)){
        $errorInfo1 = "Server error occurred: '{$error['message']} (".$error['file'].":{$error['line']})'";
        $errorInfo2 = "Server error occurred: '{$error['message']} (".basename($error['file']).":{$error['line']})'";
        //MbqError::alert('', $errorInfo2);
        //MbqMain::$oMbqCm->writeLog($errorInfo1, true);
        switch($error['type']){
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
                MbqError::alert('', $errorInfo2);
                break;
        }
    }
}

?>