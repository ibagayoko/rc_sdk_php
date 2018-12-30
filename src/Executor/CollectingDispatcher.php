<?php

namespace IBagayoko\Executor;

/**
 * CollectingDispatcher.
 * Send messages back to user
 */
class CollectingDispatcher 
{
    protected $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * utter_custom_message.
     * Sends a message with custom elements to the output channel.
     * @access	public
     * @param	mixed	&$elements	
     * @return	void
     */
    public function utter_custom_message( &$elements){

        $message = (object)["text" => null, "elements" => $elements ];

        $this->messages->append($message);
    }
    /**
     * utter_message.
     * Send a text to the output channel
     * @access	public
     * @param	mixed	$text	
     * @return	void
     */
    public function utter_message($text){

        $message = (object)["text"=> $text];

        $this->messages[] = ($message);
    }
    /**
     * utter_button_message.
     * Sends a message with buttons to the output channel.
     * @access	public
     * @param	mixed	$text     	
     * @param	mixed	$buttons  	
     * @param	mixed	...$kwargs	
     * @return	void
     */
    public function utter_button_message($text, $buttons, ...$kwargs){

        $message = (object)["text"=> $text, "buttons"=> $buttons];
        $message = (object)array_merge((array)$message, $kwargs);

        $this->messages->append($message);
}
    /**
     * utter_attachment.
     * Send a message to the client with attachments.
     * @access	public
     * @param	mixed	$attachment	
     * @return	void
     */
    public function utter_attachment($attachment){

        $$message = (object)["text"=> null, "attachment"=> $attachment];

        $this->messages->append($message);
    }

    


    /**
     * utter_button_template.
     * Sends a message template with buttons to the output channel.
     * # TODO=> $deprecate this function?
     * @access	public
     * @param	mixed	$template  	
     * @param	mixed	$buttons   	
     * @param	mixed	$tracker   	
     * @param	mixed	$silent_fai	
     * @param	mixed	...$kwargs 	
     * @return	void
     */
    public function utter_button_template($template, $buttons, $tracker, $silent_fail=False, ...$kwargs ){

        $message = (object)["template"=> $template, "buttons"=> $buttons];
        $message = (object)array_merge((array)$message, $kwargs);

        $this->messages->append($message);
                              }

    /**
     * utter_template.
     * Send a message to the client based on a template.
     * @access	public
     * @param	mixed	$template  	
     * @param	mixed	$tracker   	
     * @param	mixed	$silent_fai	
     * @param	mixed	...$kwargs 	
     * @return	void
     */
    public function utter_template($template, $tracker, $silent_fail=false, ...$kwargs){

        $message = (object)["template"=> $template];
        $message = (object)array_merge((array)$message, $kwargs);

        $this->messages->append($message);
                       }
    public function getMessages()
    {
        return $this->messages;
    }
    public function __get( $name ) {
        if( method_exists( $this , $method = ( 'get' . ucfirst( $name  ) ) ) )
            return $this->$method();
        else
            throw new Exception( 'Can\'t get property ' . $name );
    }
}
