<?php

namespace Clumsy\Social\Providers\Facebook\Resources;

use Clumsy\Eminem\Models\Media;
use Clumsy\Social\Providers\Facebook\Models\PagePosts as FacebookPosts;
use Illuminate\Support\Facades\File;

class PagePosts extends Base
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
        $graphEdge = $response->getGraphEdge();

        foreach ($graphEdge as $graphNode) {
            $data = array(
                'external_id'  => $graphNode->getField('id'),
                'content'      => $graphNode->getField('message'),
                'link'         => 'https://www.facebook.com/'.$graphNode->getField('id'),
                'published_at' => $graphNode->getField('created_time'),
                'likes'        => $graphNode->getField('likes')->getTotalCount(),
                'status_type'  => $graphNode->getField('status_type'),
                'type'         => $graphNode->getField('type'),
                'is_published' => $graphNode->getField('is_published'),
            );

            $model = FacebookPosts::firstOrNew(array_only($data, 'external_id'));

            if (!$model->exists) {
                $model = $model->create($data);

                $imageLink = $graphNode->getField('full_picture');

                if ($imageLink !== null) {
                    Media::create(array(
                        'path_type' => 'external',
                        'path'      => $imageLink,
                        'mime_type' => 'image/'.substr(File::extension($imageLink), 0, 3),
                    ))
                    ->bind(array(
                        'association_type' => 'FacebookPost',
                        'association_id'   => $model->id,
                        'position'         => 'image',
                    ));
                }
            } else {
                $data['id'] = $model->id;
                $model->update($data);
            }
        }

        return true;
    }
}
