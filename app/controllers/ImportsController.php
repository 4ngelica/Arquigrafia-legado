<?php

class ImportsController extends \BaseController {

  public function import() {
    $acervoreview = User::whereLogin('acervoreview')->first();
    if ( ! $acervoreview->equal( Auth::user() ) ) {
      return Redirect::to('/');
    }
    $root = public_path() . '/arquigrafia-imports/';

    $acervofau = User::whereLogin('acervofau')->first();
    $this->importOdsFiles($acervofau, $root . 'acervofau');

    $acervoquapa = User::whereLogin('acervoquapa')->first();
    $this->importOdsFiles($acervoquapa, $root . 'acervoquapa');
    return Redirect::to('/');
  }

  public function importOdsFiles($user, $root) {
    Queue::push( 'lib\photoimport\import\Importer', array(
      'user' => $user->id,
      'root' => $root
    ));
  }


}