<?php
/**
 * Modification of CodeIgniter Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.  Most of
 * this class is borrowed from Paul's Extension class in ExpressionEngine.
 *
 */
class Hooks{

 // --------------------------------------------------------------------
  var $enabled    = FALSE;
  var $hooks      = array();
  var $in_progress  = FALSE;
  /**
   * Call Hook
   *
   * Calls a particular hook
   *
   * @access  private
   * @param string  the hook name
   * @return  mixed
   */
   function __construct(){
     $this->_initialize();
   }

   function _initialize()
    {

    // If hooks are not enabled in the config file
    // there is nothing else to do

    if (knightlover::conf()->enable_hooks == FALSE)
    {
      return;
    }

    // Grab the "hooks" definition file.
    // If there are no hooks, we're done.

    include_once(__SITE__ROOT.'/hooks.php');
    if ( ! isset($hook) OR ! is_array($hook))
    {
      return;
    }

    $this->hooks =& $hook;
    $this->enabled = TRUE;
    }

  function _call_hook($which = '',$params = null)
  {
    if ( ! $this->enabled OR ! isset($this->hooks[$which]))
    {
      return FALSE;
    }
    if (isset($this->hooks[$which][0]) AND is_array($this->hooks[$which][0]))
    {
      foreach ($this->hooks[$which] as $val)
      {
	if(!empty($params)){
		$val['params'] = $params;
	}
        $this->_run_hook($val);
      }
    }
    else
    {
	if (!empty($params)) {
                $this->hooks[$which]['params'] = $params;
	}
      $this->_run_hook($this->hooks[$which]);
    }

    return TRUE;
  }

  // --------------------------------------------------------------------
 /**
   * Run Hook
   *
   * Runs a particular hook
   *
   * @access  private
   * @param array the hook details
   * @return  bool
   */
  function _run_hook($data)
  {
    if ( ! is_array($data))
    {
      return FALSE;
    }
    // -----------------------------------
    // Safety - Prevents run-away loops
    // -----------------------------------

    // If the script being called happens to have the same
    // hook call within it a loop can happen

    if ($this->in_progress == TRUE)
    {
      return;
    }

    // -----------------------------------
    // Set file path
    // -----------------------------------

    if ( ! isset($data['filename']))
    {
      return FALSE;
    }
    $filepath = __SITE__ROOT.'class/'.$data['filename'];
    if ( ! file_exists($filepath))
    {
      return FALSE;
    }
    // -----------------------------------
    // Set class/function name
    // -----------------------------------
    $class    = FALSE;
    $function = FALSE;
    $params   = '';


    if (isset($data['class']) AND $data['class'] != '')
    {
      $class = $data['class'];
    }

    if (isset($data['function']))
    {
      $function = $data['function'];
    }

    if (isset($data['params']))
    {
      $params = $data['params'];
    }

    if ($class === FALSE AND $function === FALSE)
    {
      return FALSE;
    }
    // -----------------------------------
    // Set the in_progress flag
    // -----------------------------------

    $this->in_progress = TRUE;
    // -----------------------------------
    // Call the requested class and/or function
    // -----------------------------------
 if ($class !== FALSE)
    {
      if ( ! class_exists($class))
      {
        require($filepath);
      }

      $HOOK = new $class;
      //$HOOK->$function($params);
	call_user_func_array(array($HOOK,$function), $params);
    }
    else
    {
      if ( ! function_exists($function))
      {
        require($filepath);
      }

      //$function($params);
	call_user_func_array($function,$params);
    }

    $this->in_progress = FALSE;
    return TRUE;
  }

}

?>
