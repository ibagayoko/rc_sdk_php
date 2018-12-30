<?php

namespace IBagayoko\Executor;

use IBagayoko\Action;
use IBagayoko\Tracker;

class ActionExecutor 
{
    protected $actions;

    public function __construct()
    {
        $this->actions = (object)[];
    }

    public function register_action($action)
    {
        if(class_exists($action))
            $action = new $action();
        if($action instanceof Action)
            $this->register_function($action->name(), array($action, "run"));
        else {
            throw new Exception("You can only register instances or subclasses of 
                                type Action. If you want to directly register 
                                a function, use `register_function` instead.");
            
        }
    }

    public function register_function($name, callable  $f)
    {
        //  logger.info("Registered function for '{}'.".format(name))
        // $fct = new \ReflectionFunction($f);
        $arg_count =3;// $fct->getNumberOfRequiredParameters();
        if ($arg_count < 3)
            throw new Exception("You can only register functions that take 
                            3 parameters as arguments. The three parameters 
                            your function will receive are: dispatcher, 
                            tracker, domain. Your function accepts only $arg_count 
                            parameters.");
        $this->actions->$name = $f;
    }

    public function register_package($package)
    {
        $actions = [];
        if(is_string  ($package))
            $actions = require ($package);
        else if(is_array ($package))
            $actions  = $package;
        else 
        throw new Exception("\$package should a string (path to the actions list file or an array of actions to be register)");
        
        

       foreach ($actions as $action ) {
           $this->register_action($action);
       }


    }

    static public function _create_api_response($events, $messages)
    {
        return (object)[
            "events"=> !is_null($events)? $events : [],
            "responses"=>$messages
        ];
    }

    public function run($action_call)
    {
        $action_name = $action_call->next_action;
        if($action_name){
            // logger.debug("Received request to run '{}'".format(action_name))
            $action = $this->actions->$action_name;
            if ($action){
              $tracker_json = $action_call->tracker;
              $domain = ActionExecutor::get_or_d($action_call->domain, (object)[]);  
              $tracker = Tracker::from_dict($tracker_json);
              $dispatcher = new CollectingDispatcher();

              $events = $action($dispatcher, $tracker, $domain);

            // logger.debug("Successfully ran '{}'".format(action_name))

                return $this->_create_api_response($events, $dispatcher->messages);


            }
            else{
                throw new Exception("No registered Action found for name '$action_name'.");
                
            }

        }
    }


    static public function get_or_d(&$value, $default = null)
    {
    return isset($value) ? $value : $default;
    }
    
}
