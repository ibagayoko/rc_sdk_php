# Rasa PHP-SDK


PHP SDK for the development of custom actions for Rasa Core.

## Compatibility

| SDK version   | compatible Rasa Core version           |
|---------------|----------------------------------------|
| `0.1.x`       | `>=0.12.x`                             |


## Installation

To install the SDK run

```bash
composer require ibagayoko/rc_sdk_php
```

## Usage
Can find detailed instructions about [Custom Actions](https://rasa.com/docs/core/customactions) in the Rasa Core Documentation.
[Custom Actions](https://rasa.com/docs/core/customactions).
In your acion endpoint config file put:
```yml
action_endpoint:
  url: http://path-to.php/webhook

```
Simple PHP app
```php

require "vendor/autoload.php";

use IBagayoko\RasaCoreSdk\Endpoint;
use IBagayoko\RasaCoreSdk\Action;
use IBagayoko\RasaCoreSdk\Events;

class PhpAction extends Action 
{
    public function name()
    {
        return "action_php";
    }
    public function run($dispatcher, $tracker, $domain)
    {
        $dispatcher->utter_message("Hello From Php");
        return [Events::SlotSet("php", "up")];
    }
}

// create a new endpoint with an array of customs actions
$endpoint = new Endpoint([PhpAction::class]);

switch ($_SERVER["PATH_INFO"]) {
    case '/health':
        $endpoint->health();
        break;
    case '/webhook':
        $endpoint->webhook();
    default:
        break;
}


```

## Laravel Usage

```php
use IBagayoko\RasaCoreSdk\Endpoint;
use IBagayoko\RasaCoreSdk\Action;
use IBagayoko\RasaCoreSdk\Events;

class PhpAction extends Action 
{
    public function name()
    {
        return "action_php";
    }
    public function run($dispatcher, $tracker, $domain)
    {
        $dispatcher->utter_message("Hello From Php");
        return [Events::SlotSet("php", "up")];
    }
}

// create a new endpoint with an array of customs actions
$endpoint = new Endpoint([PhpAction::class]);

Route::get('/health', function () use($endpoint)
{
	  return $endpoint->health();
});

Route::post('/webhook', function () use($endpoint)
{
	 return $endp->webhook();
});
```


## License
Licensed under the Apache License, Version 2.0. [Copy of the license](LICENSE).