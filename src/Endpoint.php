<?php 

namespace IBagayoko;

use IBagayoko\Executor\ActionExecutor;

class Endpoint 
{
    protected $executor;

    public function __construct($action_package_name)
    {
        $this->executor = new ActionExecutor();
        $this->executor->register_package($action_package_name);
    }


    
    /**
     * health.
     * Have to be bind to URL : /health 
     * Method : GET & OPTIONS
     * @access	public
     * @return	mixed
     */
    public function health()
    {
        return json_encode((object)["status"=>"ok"]);
    }

    public function webhook()
    {
        $action_call = json_decode(file_get_contents("php://input"));
        // var_dump($_REQUEST);

        $response = $this->executor->run($action_call);
        
        return json_encode($response);
    }






    
}
