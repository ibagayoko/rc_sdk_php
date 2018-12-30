<?php

namespace IBagayoko;


class Tracker  
{
    /**
     * id of the source of the messages
     * @var		mixed	$sender_id
     */
    protected $sender_id;

    /**
     * slots that can be filled in this domain
     * @var		mixed	$slots
     */
    protected $slots;

    /**
     *  latest_message is `parse_data`,
     *  which is a dict: {"intent": UserUttered.intent,
     *                    "entities": UserUttered.entities,
     *                    "text": text}
     * @var		mixed	$latest_message
     */
    protected $latest_message;

    /**
     * list of previously seen events
     * @var		mixed	$events
     */
    protected $events;

    protected $paused;

    protected $followup_action;

    protected $active_form;

    protected $latest_action_name;

    public function __construct($sender_id, $slots, $latest_message, $events, $paused, $followup_action, $active_form, $latest_action_name)
    {
        $this->sender_id = $sender_id;
        $this->slots = $slots;
        $this->latest_message = $latest_message;
        $this->events = $events;
        $this->paused = $paused;
        $this->followup_action = $followup_action;
        $this->active_form = $active_form;
        $this->latest_action_name = $latest_action_name;
    }


 
    /**
     * from_dict.
     * Create a tracker from dump.
     * @access	static public
     * @param	mixed	$state	
     * @return	Tracker
     */
    static public function from_dict($state)
    {
        
        return new Tracker($state->sender_id,
                       Tracker::get_or_d($state->slots, (object)[]),
                       Tracker::get_or_d($state->latest_message, (object)[]),
                       $state->events,
                       $state->paused,
                       $state->followup_action,
                       Tracker::get_or_d($state->active_form, (object)[]),
                       $state->latest_action_name);
    }

    public function current_state()
    {
        $latest_event_time = null;
        if (!empty($this->events))
            $latest_event_time = end($this->events)["timestamp"];
        
            return json_decode("{
            \"sender_id\": \"$this->sender_id\",
            \"slots\": \"$this->slots\",
            \"latest_message\": \"$this->latest_message\",
            \"latest_event_time\": latest_event_time\",
            \"paused\": \"$this->paused\",
            \"events\": \"$this->events\",
            \"active_form\": \"$this->active_form\",
            \"latest_action_name\": \"$this->latest_action_name
        }");

    }
    public function current_slot_values()
    {
        return $this->slots;
    }

    public function get_slot(string $key)
    {
        return Tracker::get_or_d($this->slots[$key], null);
    }

    public function get_latest_entity_values($entity_type)
    {
        $entities = Tracker::get_or_d($this->latest_message["entities"], []);
        $out = [];
        foreach ($entities as $x ) {
            if (x["entity"]==$entity_type) {
                $out[] = x;
            }
        }

        return $out;
    }


    public function idx_after_latest_restart()
    {
        $idx = 0;
        foreach ($this->events as $i => $event) {
            if ($event["event"]=="restarted") {
                $idx = i+1;
            }
        }
        return $idx;
    }

    public function events_after_latest_restart()
    {
        return $this->events[$this->idx_after_latest_restart()];
    }

    public function copy()
    {
        return clone $this;
    }

    public function getLatest_message()
    {
        return $this->latest_message;
    }
      public function getLatest_action_name()
    {
        return $this->latest_action_name;
    }
      public function getSlots()
    {
        return $this->slots;
    }
       public function getActive_form()
    {
        return $this->active_form;
    }
    public function __get( $name ) {
        if( method_exists( $this , $method = ( 'get' . ucfirst( $name  ) ) ) )
            return $this->$method();
        else
            throw new Exception( 'Can\'t get property ' . $name );
    }


    static public function get_or_d(&$value, $default = null)
    {
    return isset($value) ? $value : $default;
    }
}
