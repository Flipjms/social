<?php

namespace Clumsy\Social\Providers\Facebook\Models;

use Clumsy\CMS\Facades\Clumsy;
use Clumsy\CMS\Models\BaseModel as ClumsyModel;
use Clumsy\CMS\Models\Traits\Importable;

abstract class BaseModel extends ClumsyModel
{
    use Importable;

    public $importer;
    public $app_id;
    public $app_secret;

    public function getAppIdAttribute()
    {
        if ($this->app_id === null) {
            throw new \Exception();
        }
    }

    public function getAppSecretAttribute()
    {
        if ($this->app_secret === null) {
            throw new \Exception();
        }
    }

    public function getImporterParametersAttribute()
    {
        return array(
            'app-id'       => $this->app_id,
            'app-secret'   => $this->app_secret,
            'admin_prefix' => Clumsy::prefix(),
            'resource'     => $this->resourceName(),
        );
    }
}
