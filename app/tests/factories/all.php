<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

FactoryMuffin::define('User', [
	'name' => 'firstName',
	'login' => 'text',
	'email' => 'unique:email',
	'password' => 'password'
]);

FactoryMuffin::define('Photo', [
	'name' => 'text',
	'imageAuthor' => 'firstName',
	'nome_arquivo' => 'text',
	'user_id' => 'factory|User',
]);

FactoryMuffin::define('Album', [
	'title' => 'text',
	'description' => 'sentence|5',
	'user_id' => 'factory|User',
	'cover_id' => 'factory|Photo'
]);