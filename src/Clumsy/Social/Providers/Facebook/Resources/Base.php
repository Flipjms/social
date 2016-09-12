<?php namespace Clumsy\Social\Providers\Facebook\Resources;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Facebook\Facebook as Fb;
use Facebook\Exceptions\FacebookResponseException as FbRespException;

abstract class Base
{

    protected $fb;
    protected $fbApp;
    protected $accessToken;
    protected $admin_prefix;
    protected $resource;
    protected $redirect_to;
    protected $params;
    protected $access_token_type;
    protected $permissions = array();

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $arguments = func_get_args();

        $this->app_id = array_shift($arguments);
        $this->app_secret = array_shift($arguments);

        $this->fb = new Fb(
            array(
                'app_id'      => $this->app_id,
                'app_secret'  => $this->app_secret,
                'default_graph_version' => 'v2.4',
            )
        );

        $this->admin_prefix = array_shift($arguments);
        $this->resource = array_shift($arguments);
        if ($this->resource) {
            $this->redirect_to = route("{$this->admin_prefix}.{$this->resource}.import");
        }

        $this->params = $arguments;
    }

    protected function getAccessTokenType()
    {
        return $this->access_token_type ?: 'user';
    }

    protected function setAccessToken($type = 'app')
    {
        switch ($type) {
            case 'user':
                $this->accessToken = $this->getAccessToken();
                if ($this->accessToken == '') {
                    try {
                        $accessToken = $this->fb->getRedirectLoginHelper()->getAccessToken();
                    } catch (FbRespException $e) {
                        return Redirect::to($this->getRedirectLoginHelper()->getReAuthenticationUrl($this->redirect_to));
                    }

                    if ($accessToken !== null) {
                        $this->saveToken($accessToken);
                        $this->accessToken = $accessToken;
                    } else {
                        return Redirect::to($this->loginUrl($this->redirect_to, $this->permissions));
                    }
                }
                break;
            default:
                $this->accessToken = $this->app_id.'|'.$this->app_secret;
                break;
        }

        $this->fb->setDefaultAccessToken($this->accessToken);

        return $this->accessToken;
    }

    protected function getRedirectLoginHelper()
    {
        return $this->fb->getRedirectLoginHelper();
    }

    protected function getAccessToken()
    {
        return Session::get('clumsy.fb-access-token', function () {
            $fb_tokens = DB::table('social_access_tokens')->get();

            $now = Carbon::now();
            foreach ($fb_tokens as $token) {
                $date = Carbon::parse($token->expiration_date);

                if ($date->lt($now)) {
                    DB::table('social_access_tokens')->where('id', '=', $token->id)->delete();
                } else {
                    Session::put('clumsy.fb-access-token', $token->access_token);
                    return $token->access_token;
                }
            }

            return '';

        });
    }

    protected function loginUrl($url, $permissions = array())
    {
        $helper = $this->getRedirectLoginHelper();
        return $helper->getLoginUrl($url, $permissions);
    }

    public function saveToken($accessToken)
    {
        DB::table('social_access_tokens')->insert(array(
                'service'         => 'facebook',
                'access_token'    => $accessToken->getValue(),
                'expiration_date' => $accessToken->getExpiresAt(),
                'created_at'      => date("Y-m-d H:i:s"),
                'updated_at'      => date("Y-m-d H:i:s"),
            ));

        Session::put('clumsy.fb-access-token', $accessToken->getValue());
    }

    public function getRequest()
    {
        return $this->fb->sendRequest('GET', $this->endpoint, (array)$this->fields);
    }

    public function importPrivate($token_type)
    {
        $accessToken = $this->setAccessToken($token_type);

        if ($accessToken instanceof SymfonyResponse) {
            return $accessToken;
        }

        Session::put('clumsy.attempted-private-import', true);

        return $this->import();
    }

    public function import()
    {
        if ($this->access_token_type && !$this->accessToken) {
            return $this->importPrivate($this->access_token_type);
        }

        try {
            $response = $this->getRequest();
        } catch (\Exception $e) {
            if ($e instanceof \Facebook\Exceptions\FacebookResponseException) {
                DB::table('social_access_tokens')->where('access_token', '=', $this->accessToken)->delete();
                Session::forget('clumsy.fb-access-token');
            }

            if (!Session::has('clumsy.attempted-private-import')) {
                return $this->importPrivate($this->getAccessTokenType());
            }

            Session::forget('clumsy.attempted-private-import');

            return new \Illuminate\Support\MessageBag(
                array(
                        'error' => 'Não foi possível aceder ao facebook! Tente novamente mais tarde...',
                    )
            );
        }

        Session::forget('clumsy.attempted-private-import');

        if ($response instanceof \Illuminate\Support\MessageBag) {
            return $response;
        }

        return $this->parseResponse($response);
    }
}
