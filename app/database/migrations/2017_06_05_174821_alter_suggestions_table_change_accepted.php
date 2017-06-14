<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSuggestionsTableChangeAccepted extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$q = 'ALTER TABLE suggestions MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, MODIFY updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL;';
		DB::unprepared(DB::raw($q));
		$q = 'ALTER TABLE suggestions MODIFY accepted TINYINT(1) DEFAULT NULL;';
		DB::unprepared(DB::raw($q));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$q = 'ALTER TABLE suggestions MODIFY accepted TINYINT(1) NOT NULL;';
		DB::unprepared(DB::raw($q));
	}

}
