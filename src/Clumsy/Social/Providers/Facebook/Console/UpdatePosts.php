<?php

namespace Clumsy\Social\Providers\Facebook\Console;

use Clumsy\Social\Providers\Facebook\Resources\PagePosts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdatePosts extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'clumsy:facebook-update-posts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fetch posts from facebook page and store it on database';

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
		$fb_app_id = Config::get('clumsy/social::facebook_app_id');
		$fb_app_secret = Config::get('clumsy/social::facebook_app_secret');

        $model = $this->option('model');
        if ($model == null) {
            $this->error('No page model was given...');
            die();
        }

        $page = new $model();

        $endpoint = $page->importer_parameters['endpoint'];
        $fields = $page->importer_parameters['fields'];

		$fbObj = new PagePosts($fb_app_id, $fb_app_secret, null, null, $endpoint, $fields);
		$fbObj->import();

		$this->comment('Posts Imported Successfully');
	}

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('model', null, InputOption::VALUE_REQUIRED, 'Model', null),
        );
    }
}
