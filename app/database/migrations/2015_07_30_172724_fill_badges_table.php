<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillBadgesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Badge::create(['name'=>'Iniciante', 'description'=>'usuário realizou 2 avaliações', 'class'=>'Bronze']);
      	Badge::create(['name'=>'Veterano', 'description'=>'usuário realizou 5 avaliações', 'class'=>'Bronze']);
      	Badge::create(['name'=>'Arquiteto', 'description'=>'usuário realizou 10 avaliações', 'class'=>'Bronze']);
      	Badge::create(['name'=>'Especialista', 'description'=>'usuário realizou 20 avaliações', 'class'=>'Silver']);
      	Badge::create(['name'=>'Professor', 'description'=>'usuário realizou 50 avaliações', 'class'=>'Silver']);
      	Badge::create(['name'=>'Master', 'description'=>'usuário realizou 100 avaliações', 'class'=>'Gold']);
      	Badge::create(['name'=>'Rei', 'description'=>'usuário realizou 100+ avaliações', 'class'=>'Gold']);
      
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
