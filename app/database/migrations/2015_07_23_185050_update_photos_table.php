<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*Schema::table('photos', function ($table) {
    		$table->string('support')->nullable();    		
    		$table->string('subject')->nullable();
    		$table->dateTime('hygieneDate')->nullable();
    		$table->dateTime('backupDate')->nullable();
    		$table->string('UserResponsible')->nullable();
    		$table->string('observation')->nullable();    		
    });*/



	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			/*Schema::table('photos', function ($table) {
    		$table->dropColumn('support');    		
    		$table->dropColumn('subject');
    		$table->dropColumn('hygieneDate');
    		$table->dropColumn('backupDate');
    		$table->dropColumn('UserResponsible');
    		$table->dropColumn('observation');    		
      });*/
	}

}
