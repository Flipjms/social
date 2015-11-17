<?php namespace Clumsy\Social\Providers\Facebook\Resources;

use Clumsy\Social\Providers\Facebook\Models\UserPosts as FacebookPosts;

class UserPosts extends Base{

    protected $access_token_type = 'user';

    public $endpoint;
    public $fields;
    public $permissions;

    public function __construct($app_id,$app_secret,$redirect_to,$params = null)
    {
        call_user_func_array(array('parent', '__construct'), func_get_args());

        $this->endpoint = $this->params[0];
        $this->fields = $this->params[1];
        $this->permissions = $this->params[2];
    }

    public function parseResponse($response)
    {
        /*
                DO THE IMPORT ROUTINE
         */
    }
}