<?php

namespace Clumsy\Social\Providers\Facebook\Resources;

use Illuminate\Support\Facades\DB;

class PageLikes extends Base
{

    public $endpoint;
    public $fields;

    public function __construct()
    {
        call_user_func_array(array('parent', '__construct'), func_get_args());

        $this->fb->setDefaultAccessToken($this->app_id.'|'.$this->app_secret);

        $this->endpoint = $this->params[0];
        $this->fields = $this->params[1];
    }

    public function parseResponse($response)
    {
        $response = $response->getDecodedBody();

        $data = array(
                'likes'       => $response['likes'],
                'resource_id' => $response['id'],
                'created_at'  => date("Y-m-d H:i:s"),
                'updated_at'  => date("Y-m-d H:i:s"),
            );

        DB::table('facebook_likes')->insert($data);

        return true;
    }
}
