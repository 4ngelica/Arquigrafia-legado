<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToNews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$q = "ALTER TABLE news MODIFY COLUMN user_id int(10) unsigned NOT NULL;";
		DB::insert(DB::raw($q));
		$q = "ALTER TABLE news MODIFY COLUMN sender_id int(10) unsigned NOT NULL;";
		DB::insert(DB::raw($q));

		Schema::table('news', function(Blueprint $table){
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$q = "ALTER TABLE news DROP FOREIGN KEY news_user_id_foreign;";
		DB::insert(DB::raw($q));
		$q = "ALTER TABLE news DROP FOREIGN KEY sender_id_foreign;";
		DB::insert(DB::raw($q));

		$q = "ALTER TABLE news MODIFY COLUMN user_id bigint(20) NOT NULL;";
		DB::insert(DB::raw($q));
		$q = "ALTER TABLE news MODIFY COLUMN sender_id bigint(20) NOT NULL;";
		DB::insert(DB::raw($q));
	}

}
