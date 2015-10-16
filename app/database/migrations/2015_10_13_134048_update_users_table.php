<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if ( Schema::hasColumn('users', 'nb_eval') ) {
			Schema::table('users', function(Blueprint $table)
			{
				$table->dropColumn('nb_eval');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if ( ! Schema::hasColumn('users', 'nb_eval') ) {
			Schema::table('users', function(Blueprint $table)
			{
				$table->integer('nb_eval')->default(0);
			});
		}
	}

}
