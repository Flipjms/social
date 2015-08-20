<?php

/*
 |--------------------------------------------------------------------------
 | Migrations info
 |--------------------------------------------------------------------------
 |
 | This table is necessary for Social Providers that need to store an access
 | token (e.g. Facebook User Access Token).
 | 
 | 
 |
 | To use an Social Provider migration, run the following Artisan command:
 |
 | php artisan migrate --path=vendor/clumsy/social/src/migrations/[provider]
 |
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialAccessTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_access_tokens',function(Blueprint $table){

			$table->increments('id');
			$table->string('service');
			$table->string('access_token');
			$table->timestamp('expiration_date');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_access_tokens');
	}

}
