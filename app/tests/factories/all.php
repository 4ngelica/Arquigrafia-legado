<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

FactoryMuffin::define('User', [
	'name' => 'firstName',
	'login' => 'unique:word',
	'email' => 'unique:email',
	'password' => 'password'
]);

FactoryMuffin::define('Photo', [
	'name' => 'sentence|5',
	'imageAuthor' => 'firstName',
	'nome_arquivo' => 'word',
	'user_id' => 'factory|User',
	'tombo' => 'word',
]);

FactoryMuffin::define('Album', [
	'title' => 'sentence|5',
	'description' => 'sentence|5',
	'user_id' => 'factory|User',
	'cover_id' => 'factory|Photo'
]);

FactoryMuffin::define('Tag', [
	'name' => 'word',
	'count' => 'randomNumber'
]);