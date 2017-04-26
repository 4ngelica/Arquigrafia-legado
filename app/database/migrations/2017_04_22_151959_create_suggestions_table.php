<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuggestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('suggestions', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id');
			$table->bigInteger('photo_id');
			$table->bigInteger('attribute_type');
			$table->bigInteger('moderator_id');
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('photo_id')->references('id')->on('photos');
			$table->foreign('attribute_type')->references('id')->on('photo_attribute_type');
			$table->string('text');
			$table->boolean('accepted');
			$table->foreign('moderator_id')->references('id')->on('moderators');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('suggestions');
	}

}
