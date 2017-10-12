<?php

namespace modules\moderation\controllers;

class ContributionsController extends \BaseController {
  protected $gamified;

  public function __construct() {
    // Filtering if user is logged out, redirect to login page
    $this->beforeFilter(
      'auth',
      array('except' => [])
    );
    // Setting gamified initial value
    $this->gamified = false;
  }

  public function showContributionsGamified() {
    // Setting gamified flag to true
    $this->gamified = true;
    // Running show
    return $this->showContributions();
  }

  public function showContributions() {
    $user = \Auth::user();
    $input = \Input::all();
    $tab = null;
    if (isset($input['tab'])) {
      $tab = $input['tab'];
    }
    $filter = null;
    if (isset($input['filter'])) {
      $filter = $input['filter'];
    }

    return \View::make('show-contributions', [
      'currentUser' => $user,
      'isGamefied' => $this->gamified,
      'selectedTab' => $tab,
      'selectedFilterId' => $filter
    ]);
  }

}
