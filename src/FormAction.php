<?php

namespace IBagayoko\RasaCoreSdk;


define("REQUESTED_SLOT", "requested_slot");

abstract class FormAction extends Action 
{
    /**
     * required_slots.
     * A list of required slots that the form has to fill
     * Use `tracker` to request different list of slots
     * depending on the state of the dialogue
     * @access	static public
     * @param	Tracker	$tracker	
     */
    abstract static public function required_slots(Tracker $tracker);

    /**
     * from_entity.
     * A dictionary for slot mapping to extract slot value from
     *      - an extracted entity
     *      - conditioned on
     *      - intent if it is not null
     *      - not_intent if it is not null,
     *       meaning user intent should not be this intent
     * @access	public
     * @param	mixed	$entity    	
     * @param	mixed	$intent    	
     * @param	mixed	$not_intent	
     * @return	mixed
     */
    public function from_entity($entity, $intent=null, $not_intent=null)
    {
        list($intent, $not_intent) = $this._list_intents($intent, $not_intent);

        return (object)["type"=>"from_entity", "entity"=> $entity,
                "intent"=> $intent, "not_intent"=> $not_intent];
    }

    /**
     * from_intent.
     * A dictionary for slot mapping to extract slot value from
     *     - intent: value pair
     *     - conditioned on
     *     - intent if it is not None
     *     - not_intent if it is not None,
     *           meaning user intent should not be this intent
     * @access	public
     * @param	mixed	$value    	
     * @param	mixed	$inten    	
     * @param	mixed	$not_inten	
     * @return	mixed
     */
    public function from_intent($value, $intent=null, $not_intent=null)
    {
       list($intent, $not_intent) = $this._list_intents($intent, $not_intent);

        return (object)["type"=>"from_intent", "value"=> $value,
                "intent"=> $intent, "not_intent"=> $not_intent];
    }

       /**
        * from_text.
        * A dictionary for slot mapping to extract slot value from
        *    - a whole message
        *    - conditioned on
        *    - intent if it is not None
        *    - not_intent if it is not None,
        *            meaning user intent should not be this intent
        * @access	public
        * @param	mixed	$value    	
        * @param	mixed	$inten    	
        * @param	mixed	$not_inten	
        * @return	mixed
        */
       public function from_text($value, $intent=null, $not_intent=null)
    {
       list($intent, $not_intent) = $this->_list_intents($intent, $not_intent);

        return (object)["type"=>"from_text", 
                "intent"=> $intent, "not_intent"=> $not_intent];
    }

    public function get_mappings_for_slot($slot_to_fill )
    {
        $requested_slot_mappings = $this->_to_list($this->from_entity($slot_to_fill));

        return $requested_slot_mappings;
        
    }

    /**
     * intent_is_desired.
     * Check whether user intent matches
     * intent conditions in requested_slot_mapping
     * @access	static public
     * @param	mixed  	$requested_slot_mapping	
     * @param	Tracker	$tracker               	
     * @return	mixed
     */
    static public function intent_is_desired($requested_slot_mapping, Tracker $tracker)
    {
        $mapping_intents = FormAction::get_or_d($requested_slot_mapping["intent"], []);
        $mapping_not_intents = FormAction::get_or_d($requested_slot_mapping["not_intent"], []);
        $intent = FormAction::get_or_d($tracker->latest_message["intent"],(object)[])["name"];
        
        
        return (!$mapping_intents and !in_array($intent, $mapping_not_intents) or in_array($intent, $mapping_intents));
    }
    

    /**
     * extract_other_slots.
     * Extract the values of the other slots
     * if they are set by corresponding entities from the user input
     * else return None
     * @access	public
     * @param	mixed  	$dispatcher	
     * @param	Tracker	$tracker   	
     * @param	mixed  	$daomain   	
     * @return	mixed
     */
    public function extract_other_slots($dispatcher, Tracker $tracker, $domain)
    {
        $slot_to_fill = $tracker->get_slot(REQUESTED_SLOT);

        $slot_values = (object)[];
        foreach ($this->required_slots(tracker) as $slot) {
            // look for other slots;
            if($slot != $slot_to_fill){
                // list is used to cover the case of list slot type;
                $other_slot_mappings = $this->get_mappings_for_slot($slot);
                foreach ($other_slot_mappings as $other_slot_mapping) {
                    $intent = FormAction::get_or_d($tracker->latest_message["intent"], (object)[])["name"];
                    // check whether the slot should be filled
                    // by entity with the same name;
                    $should_fill_slot = (
                            $other_slot_mapping["type"] == "from_entity" and
                            $other_slot_mapping->get("entity") == $slot and
                            $this->intent_is_desired($other_slot_mapping,
                                                   $tracker)
                    );
                    if($should_fill_slot){
                        // list is used to cover the case of list slot type
                        $value = array($tracker->get_latest_entity_values($slot));
                        if(count($value) == 1){
                            $value = $value[0];
                        }
                        if($value){
                            // logger->debug("Extracted '{}' ";
                                        //  "for extra slot '{}'";
                                        //  ""->format(value, slot));
                            $slot_values[$slot] = $value;
                            # this slot is done, check  next;
                            break;
                        }
                    }
                }
            }
        }
        return $slot_values;

    }

public function extract_requested_slot($dispatcher, Traccker $tracker, $domain)
{
    $slot_to_fill = $tracker->get_slot(REQUESTED_SLOT);
        // logger->debug("Trying to extract requested slot '{}' ->->->"
        //              ""->format(slot_to_fill))

        # get mapping for requested slot
        $requested_slot_mappings = $this->get_mappings_for_slot(slot_to_fill);
        foreach ($requested_slot_mappings as $requested_slot_mapping) {
            // logger->debug("Got mapping '{}'"->format(requested_slot_mapping))

            if ($this->intent_is_desired($requested_slot_mapping, $tracker)){
                $mapping_type = $requested_slot_mapping["type"];

                if ($mapping_type == "from_entity"){
                    # list is used to cover the case of list slot type
                    $value = array($tracker->get_latest_entity_values(
                                    $requested_slot_mapping["entity"]));
                    if (count($value) == 0)
                        $value = null;

                    elseif (count($value) == 1)
                        $value = $value[0];
                }
                elseif ($mapping_type == "from_intent")
                    $value = $requested_slot_mapping["value"];
                elseif ($mapping_type == "from_text")
                    $value = $tracker->latest_message->get("text");
                else
                    throw new Exception(
                            'Provided slot mapping type 
                            /**
                             * @var		is	no
                             */
                            is not supported');

                if ($value != null){
                    // logger->debug("Successfully extracted '{}' "
                    //              "for requested slot '{}'"
                    //              ""->format(value, slot_to_fill))
                    return (object)[$slot_to_fill=> $value];
                }
            }
        }
        // logger->debug("Failed to extract requested slot '{}'"
        //              ""->format(slot_to_fill))
        return (object)[];
}
    /**
     * validate.
     * Validate extracted value of requested slot
     * else reject execution of the form action
     * Subclass this method to add custom validation and rejection logic
     * @access	public
     * @param	mixed  	$dispatcher	
     * @param	Tracker	$tracker   	
     * @param	mixed  	$domain    	
     * @return	mixed
     */
    public function validate($dispatcher, Tracker $tracker, $domain)
    {
        # extract other slots that were not requested
        # but set by corresponding entity
        $slot_values = $tthis->extract_other_slots($dispatcher, $tracker, $domain);

        # extract requested slot
        $slot_to_fill = $tracker->get_slot(REQUESTED_SLOT);
        if ($slot_to_fill)
            foreach ($this->extract_requested_slot($dispatcher,
                                                $tracker,
                                                $domain) as $exval) {
                $slot = $exval["slot"];
                $value = $exval["value"];
                if (method_exists($this, "validate_$slot")) {
                    $funcname = "validate_".$slot;
                    $slot_values[$slot] = $this->$funcname($value, $dispatcher, $tracker, $domain);
                }else{
                    $slot_values[$slot] = $value;
                }
            }
            
            // reject to execute the form action
            // if some slot was requested but nothing was extracted
            //  it will allow other policies to predict another action
            if (!$slot_values){
                throw new ActionExecutionRejection($this->name(),
                                               "Failed to validate slot $slot_to_fill 
                                                with action $this->name()");
                
            }

        // validation succeed, set slots to extracted values
        $setslots = [];
        foreach ($slot_values as $slot => $value) {
            $setslots[]= Events::SlotSet($slot, $value);
        }
        return $setslots;

    }

/**
 * request_next_slot.
 * Request the next slot and utter template if needed,
 * else return None
 * @access	public
 * @param	mixed  	$dispatcher	
 * @param	tracker	$tracker   	
 * @param	mixed  	$domain    	
 * @return	mixed
 */
public function request_next_slot($dispatcher, Tracker $tracker, $domain)
{
    foreach ($this->required_slots($tracker) as $slot ) {
        if (FormAction::_should_request_slot($tracker, $slot)) {
            // logger.debug("Request next slot '{}'".format(slot))
            $dispatcher->utter_template("utter_ask_$slot",
                                          $tracker,
                                          false,
                                          ...$tracker->slots);
            return [Events::SlotSet(REQUESTED_SLOT, $slot)];

        }
        // logger.debug("No slots left to request")
        return null;
    }
}

    /**
     * submit.
     * Define what the form has to do
     * after all required slots are filled
     * @access	public
     * @param	mixed  	$dispatcher	
     * @param	tracker	$tracker   	
     * @param	mixed  	$domain    	
     * @return	void
     */
    abstract public function submit($dispatcher, Tracker $tracker, $domain);

    /**
     * _to_list.
     * Convert object to a list if it is not a list,
     * null converted to empty list
     * @access	static public
     * @param	mixed	$x	
     * @return	array
     */
    static public function _to_list($x)
    {
        if ($x== null) return [];
        else if(!is_array($x)) return [$x];

        return $x;
    }

    public function _list_intents($intent=null, $not_intent=null)
    {
        if ($intent and $not_intent) 
            throw new Exception("Providing  both intent '$intent' 
                            and not_intent '$not_intent' is not supported");
        return [FormAction::_to_list($intent), FormAction::_to_list($not_intent)];
        
    }

    /**
     * _activate_if_required.
     * Return `Form` event with the name of the form
     *  if the form was called for the first time"""
     * @access	public
     * @param	tracker	$tracker	
     * @return	array
     */
    public function _activate_if_required(Tracker $tracker)
    {
        if ($tracker->active_form['name']!=null) {
            # logger.debug("The form '$tracker->active_form' is active"
        }
        else {
            # logger.debug("There is no active form")

        }
         if ($tracker->active_form['name']== $this->name()) 
            return [];
        else {
            # logger.debug("Activated the form '{}'".format(self.name()))
            return [Events::Form(self.name())];
            }
             
    }

    /**
     * _validate_if_required.
     * Return a list of events from `self.validate(...)`
     * if validation is required:
     * the form is active
     * the form is called after `action_listen`
     * form validation was not cancelled
     *
     * @access	public
     * @param	mixed  	$dispatcher	
     * @param	tracker	$tracker   	
     * @param	mixed  	$domain    	
     * @return	array
     */
    public function _validate_if_required($dispatcher, Tracker $tracker, $domain)
    {
        if ($tracker->latest_action_name == 'action_listen' and
                FormAction::get_or_d($tracker->active_form['validate'], true)){
            // logger.debug("Validating user input 'tracker.latest_message'"
            return $this->validate($dispatcher, $tracker, $domain);
        }
            // logger.debug("Skipping validation")
            return [];
    }

    /**
     * _should_request_slot.
     * Check whether form action should request given slot
     * @access	static public
     * @param	tracker	$slot     	
     * @param	string 	$slot_name	
     * @return	bool
     */
    static public function _should_request_slot(Tracker $slot, string $slot_name)
    {
        return $tracker->get_slot($slo_name) == null;
    }

    /**
     * _deactivate.
     * Return `Form` event with `None` as name to deactivate the form
     * and reset the requested slot
     * @access	public
     * @return	mixed
     */
    public function _deactivate()
    {
        // logger.debug("Deactivating the form '$this->name()")
        return [Events::Form(numfmt_get_locale), Events::SlotSet(REQUESTED_SLOT, null)];

    }

    public function run($dispatcher, Tracker $tracker, $domain)
    {
        # activate the form
        $events = $this->_activate_if_required($tracker);
        # validate user input
        $events =array_merge($events,$this->_validate_if_required($dispatcher, $tracker, $domain));

        # create temp tracker with populated slots from `validate` method
        $temp_tracker = $tracker->copy();
        foreach ($events as $e ) {
            if ($e['event'] == 'slot')
                $temp_tracker->slots[$e["name"]] = $e["value"];
        }

        $next_slot_events = $this->request_next_slot($dispatcher, $temp_tracker,
                                                  $domain);
        if ($next_slot_events != null)
            # request next slot
            $events =array_merge($events,$next_slot_events);
        else{
            # there is nothing more to request, so we can submit
            $events =array_merge($events,$this->submit($dispatcher, $temp_tracker, $domain));
            # deactivate the form after submission
            $events =array_merge($events,$this->_deactivate());
        }
        return $events;
    }



    public function __toString()
   {
      return "FormAction('$this->name()')";
   }

    static public function get_or_d(&$value, $default = null)
    {
    return isset($value) ? $value : $default;
    }
}
