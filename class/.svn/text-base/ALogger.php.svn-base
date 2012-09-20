<?php 
  
/** 
  * Class for creating basic web application logs. 
  * 
  *  $logger = new Logger(dirname(__FILE__)); 
  *  $logger->log("hello world 1"); 
  *  $logger->log("hello world 2"); 
  *  $logger->log("hello world 3"); 
  *  $logger->log("hello world 4"); 
  *  // need to write at the end of script for example after </html> 
  *  // if you need to write all logs at the end of script else you can pass $writeLogAtEnd as false in the constructor
  *  // also need to write in the error handler class if fatal exception occured 
  *  $logger->writeLog(); 
  * OR 
  * $logger->log($message); 
  *  
  * chauhansudhir@gmail.com 
  */ 
class ALogger { 
  
  //severity levels 
  const DEBUG = 0; 
  const ERROR = 1; 
  const WARN  = 2; 
  const INFO  = 3; 
  
  /** 
   * Log file resource. 
   */ 
  protected $logFileHandler = null; 
  
  /** 
   * Path to the file. 
   */ 
  protected $logFilePath; 
  
  protected $logFileName; 
  /** 
   * Date format that'll be used in log file. 
   */ 
  public $date_format = "m-d-Y H:i:s"; 
  
  private $writeLogAtEnd = true; 
  private static $logHash = array(); 
  
  /** 
   * @param string Path to log file 
   * @param string $logFileName log file name by default name will be log.txt 
   * @param bool $writeLogAtEnd whether to write all logs once or all in the end of script 
   */ 
  public function __construct($logFilePath, $logFileName = 'log.txt', $writeLogAtEnd = true) { 
    $this->logFilePath = $logFilePath; 
    $this->writeLogAtEnd = $writeLogAtEnd; 
    $this->logFileName = $logFileName; 
  
    $this->openFile(); // check whether file is writeable 
    if ($this->writeLogAtEnd === true) { 
      $this->closeFile(); 
    } 
  } 
  
  /** 
   * open file pointer if not already opened 
   */ 
  private function openFile() { 
    if ($this->logFileHandler) { 
      return true; 
    } 
  
    $fileName = $this->logFilePath . ((substr($this->logFilePath, -1) == "/") ? "" : "/") . $this->logFileName; 
  
    $this->logFileHandler = fopen($fileName, 'a+'); 
    if (!$this->logFileHandler) { 
      throw new Exception('Logger :: Cannot open file on path: ' . $fileName); 
    } 
  } 
  
  /** 
   * close file pointer 
   */ 
  private function closeFile() { 
    @fclose($this->logFileHandler); 
    $this->logFileHandler = null; 
  }     
  
  /** 
   * method to add messages in the log file method adds log in the file 
   * every time if log needs to created on every call else populate an array 
   * @param string $message that needs to be logged 
   * @param int severity of log message 
   */ 
  public function log($message, $severity = self::DEBUG) { 
    $log = $this->prepareLog($message, $severity); 
    self::$logHash[] = $log; 
    if ($this->writeLogAtEnd === false) { 
      $this->writeLog(); 
    } 
  } 
  
    /** 
     * return readable severity name for severity level 
     * @param int $severity level 
     * return string nice severity level name else return Invalid severity 
     */ 
  private function getSeverityNiceName($severity) { 
    $names = array (self::DEBUG   => "Debug", 
                    self::ERROR   => "Error", 
                    self::WARN => "Warning", 
                    self::INFO    => "Info" 
                   ); 
    if (array_key_exists($severity, $names)) { 
      return $names[$severity]; 
    } 
    else { 
      return "Invalid Severity {$severity}"; 
    } 
  } 
  
  /** 
   * format log message with date and serverity level 
   * @param string $message message that needs to be logged 
   * @param int $severity severity of log message 
   * return string return formatted log message 
   */ 
  protected function prepareLog($message, $severity) { 
    return date($this->date_format) . ' ' . $this->getSeverityNiceName($severity) . ': ' . $message; 
  } 
  
  /** 
   * write log messages in the file in append mode 
   * return false if there is no log message in the array 
   */ 
  public function writeLog() { 
    if (empty(self::$logHash)) { 
      return false; 
    } 
  
    $this->openFile(); 
    $text = implode("\n", self::$logHash); 
    if (fwrite($this->logFileHandler, $text . "\n") == false) { 
      throw new Exception("Logger :: Unable to write to log file."); 
    } 
    self::$logHash = array(); 
  } 
  
  /** 
   * attempt to call write message and close file pointer if not explicitly called 
   * not sure its working or not 
   */ 
  public function __destructor() { 
    $this->writeLog(); 
    @fclose($this->logFileHandler); 
  } 
} 
?>
