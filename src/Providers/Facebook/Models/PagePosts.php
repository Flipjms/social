<?php

namespace Clumsy\Social\Providers\Facebook\Models;

class PagePosts extends BaseModel
{

    protected $table = 'facebook_posts';

    public $importer = 'Clumsy\Social\Providers\Facebook\ImportResolver@PagePosts';
    public $page;

    public function getImporterParametersAttribute()
    {
        return array_merge(
            parent::getImporterParametersAttribute(),
            array(
                'endpoint' => '/'.$this->page_id.'/posts',
                'fields'   => array('fields' => 'full_picture,picture,message,created_time,id,link,likes.limit(1).summary(true),status_type,type,is_published'),
            )
        );
    }
}
