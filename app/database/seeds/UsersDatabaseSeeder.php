<?php

class UsersDatabaseSeeder extends Seeder {
  public function run() {
    User::create([ 'name' => 'Ricardo', 'lastName' => 'Almeida', 'login' => 'ricardo', 'email' => 'ricardo@ricardo.com', 'password' => Hash::make('ricardo'), 'active' => 'yes' ]);
    User::create([ 'name' => 'Joao', 'lastName' => 'Batista', 'login' => 'joao', 'email' => 'joao@joao.com', 'password' => Hash::make('joao'), 'active' => 'yes' ]);
    User::create([ 'name' => 'Alberto', 'lastName' => 'Coelho', 'login' => 'alberto', 'email' => 'alberto@alberto.com', 'password' => Hash::make('alberto'), 'active' => 'yes' ]);
    User::create([ 'name' => 'Ronaldo', 'lastName' => 'Silva', 'login' => 'ronaldo', 'email' => 'ronaldo@ronaldo.com', 'password' => Hash::make('ronaldo'), 'active' => 'yes' ]);
  }
}
