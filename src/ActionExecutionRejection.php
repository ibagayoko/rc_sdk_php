<?php 
namespace IBagayoko;

class ActionExecutionRejection extends Exception 
{
    protected $action_name;

    protected $message;

    public function __construct($action_name, $message=null)
    {
        $this->action_name = $action_name;
        $this->message = $message ? $message : "Custom action '$action_name' rejected execution of"
                        ;
    }

    public function __toString()
   {
      return $this->message;
   }


}
