<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillBadgesLikeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Badge::create(['name'=>'TestLike_photo', 'description'=>'x likes in one picture ', 'class'=>'Bronze']);
      	Badge::create(['name'=>'TestLike_comment', 'description'=>'x likes in one comment', 'class'=>'Bronze']);
      	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
