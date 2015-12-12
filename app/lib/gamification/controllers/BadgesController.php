<?php namespace lib\gamification\controllers;

use lib\gamification\models\Badge;

class BadgesController extends \BaseController {

  public function show($id) {
    $badge = Badge::find($id);
    if ( is_null($badge) ) {
      return \Redirect::to('/');
    }
    return \View::make('show_badge')->with(compact('badge'));
  }
}