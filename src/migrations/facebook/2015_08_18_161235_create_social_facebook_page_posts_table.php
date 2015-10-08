<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialFacebookPagePostsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_posts', function (Blueprint $table) {

            $table->increments('id');
            $table->string('external_id');
            $table->boolean('active')->default(1);
            $table->boolean('is_published');

            $table->text('content')->nullable()->default(null);
            $table->string('link')->nullable()->default(null);
            $table->integer('likes')->nullable()->default(null);
            $table->string('status_type');
            $table->string('type');

            $table->timestamp('published_at');

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
        Schema::drop('facebook_posts');
    }
}
