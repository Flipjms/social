<?php

namespace Clumsy\Social\Providers\Facebook\Models;

class UserPosts extends BaseModel
{

    protected $table = 'facebook_user_posts';

    public $importer = 'Clumsy\Social\Providers\Facebook\ImportResolver@UserPosts';

    public function getImporterParametersAttribute()
    {
        return array_merge(
            parent::getImporterParametersAttribute(),
            array(
                'endpoint'     => '/'.$this->page_id.'/posts',
                'fields'       => array(),
                'permissions'  => array('user_posts'),
            )
        );
    }
}
