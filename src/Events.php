<?php

namespace IBagayoko\RasaCoreSdk;

class Events 
{
    # noinspection PyPep8Naming
static public function UserUttered($text,
                $parse_data=null,
                $timestamp=null){
    return (object)[
        "event"=> "user",
        "timestamp"=> $timestamp,
        "text"=> $text,
        "parse_data"=> $parse_data,
    ];
}


# noinspection PyPep8Naming
static public function BotUttered($text=null, $data=null, $timestamp=null){
    return (object)[
        "event"=> "bot",
        "timestamp"=> $timestamp,
        "text"=> $text,
        "data"=> $data,
    ];
}


# noinspection PyPep8Naming
static public function SlotSet($key, $value=null, $timestamp=null){
    return (object)[
        "event"=> "slot",
        "timestamp"=> $timestamp,
        "name"=> key,
        "value"=> value,
    ];}


# noinspection PyPep8Naming
static public function Restarted($timestamp=null){
    return (object)[
        "event"=> "restart",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function UserUtteranceReverted($timestamp=null){
    return (object)[
        "event"=> "rewind",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function AllSlotsReset($timestamp=null){
    return (object)[
        "event"=> "reset_slots",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function ReminderScheduled($action_name, $trigger_date_time, $name=null,
                      $kill_on_user_message=true, $timestamp=null){
    return (object)[
        "event"=> "reminder",
        "timestamp"=> $timestamp,
        "action"=> $action_name,
        "date_time"=> $trigger_date_time.isoformat(),
        "name"=> $name,
        "kill_on_user_msg"=> $kill_on_user_message
    ];}


# noinspection PyPep8Naming
static public function ActionReverted($timestamp=null){
    return (object)[
        "event"=> "undo",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function StoryExported($timestamp=null){
    return (object)[
        "event"=> "export",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function FollowupAction($name,
                   $timestamp=null){
    return (object)[
        "event"=> "followup",
        "timestamp"=> $timestamp,
        "name"=> $name,
    ];}


# noinspection PyPep8Naming
static public function ConversationPaused($timestamp=null){
    return (object)[
        "event"=> "pause",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function ConversationResumed($timestamp=null){
    return (object)[
        "event"=> "resume",
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function ActionExecuted($action_name, $policy=null, $confidence=None, $timestamp=null){
    return (object)[
        "event"=> "action",
        "name"=> $action_name,
        "policy"=> $policy,
        "confidence"=> $confidence,
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function AgentUttered($text=null, $data=null, $timestamp=null){
    return (object)[
        "event"=> "agent",
        "text"=> $text,
        "data"=> $data,
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function Form($name, $timestamp=null){
    return (object)[
        "event"=> "form",
        "name"=> $name,
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function FormValidation($validate, $timestamp=null){
    return (object)[
        "event"=> "form_validation",
        "validate"=> $validate,
        "timestamp"=> $timestamp,
    ];}


# noinspection PyPep8Naming
static public function ActionExecutionRejected($action_name, $policy=null, $confidence=null,
                            $timestamp=null){
    return (object)[
        "event"=> "action_execution_rejected",
        "name"=> $action_name,
        "policy"=> $policy,
        "confidence"=> $confidence,
        "timestamp"=> $timestamp,
    ];
}

}
