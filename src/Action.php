<?php 
namespace IBagayoko;

abstract class Action{

    /**
     * abstract
     * Unique identifier of this simple action.
     * @access	public
     * @return	string
     */
   abstract public function name();
 
   /**
    * Execute the side effects of this action.
    * @access	public
    *
    * @param	CollectingDispatcher	$dispatcher	
    * @param	Tracker	$tracker   	
    * @param	mixed	$domain    	
    */
   abstract public function run($dispatcher, $tracker, $domain);

   public function __toString()
   {
      return "Action('$this->name()')";
   }
}