<?php

namespace Clumsy\Social\Providers\Facebook\Console;

use Clumsy\Social\Providers\Facebook\Resources\PageLikes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;

class UpdateLikes extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clumsy:facebook-update-likes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch likes from facebook page and store it on database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $fb_app_id = $this->option('app_id');
        $fb_app_secret = $this->option('app_secret');
        $pageIds = $this->option('page_id');
        $fields = array('fields' => 'likes');

        foreach ($pageIds as $item) {
            $endpoint = '/'.$item;
            $fbObj = new PageLikes($fb_app_id, $fb_app_secret, null, null, $endpoint, $fields);
            $fbObj->import();
        }

        $this->comment('Likes Imported Successfully');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('app_id', null, InputOption::VALUE_OPTIONAL, 'Facebook App Id',Config::get('clumsy/social::facebook_app_id')),
            array('app_secret', null, InputOption::VALUE_OPTIONAL, 'Facebook App Secret',Config::get('clumsy/social::facebook_app_secret')),
            array('page_id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Facebook Page Id(s)',(array) Config::get('clumsy/social::facebook_ids')),
        );
    }
}
