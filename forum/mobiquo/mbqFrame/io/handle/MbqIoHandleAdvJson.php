<?php

defined('MBQ_IN_IT') or exit;

/**
 * io handle for Json class
 * 
 * @since  2012-8-2
 * @author Jayeley Yang <jayeley@gmail.com>
 */
Class MbqIoHandleAdvJson {
    
    protected $cmd;   /* action command name,must unique in all action. */
    protected $input;   /* input params array */
    
    public function __construct() {
        $this->cmd = $_GET['do'];
        $this->input = array('get' => $_GET, 'post' => $_POST);
    }
    
    /**
     * return current command
     *
     * @return string
     */
    public function getCmd() {
        return $this->cmd;
    }
    
    /**
     * return current input
     *
     * @return array
     */
    public function getInput() {
        return $this->input;
    }
    
    public function output(&$data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * output error/success message
     *
     * @param  String  $message
     * @param  Boolean  $result
     * @patam  Integer  $errorCode
     * @return string default as json
     */
    public static function alert($message, $result = false, $errorCode = NULL) {
        header('Content-Type: application/json');
        $response = array(
            'result'        => $result,
            'error'   => $message
        );
        if (!is_null($errorCode)) {
            $response['code'] = $errorCode;
        }
        echo json_encode($response);
        exit;
    }
}

?>