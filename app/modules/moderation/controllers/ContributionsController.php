<?php

namespace modules\moderation\controllers;

class ContributionsController extends \BaseController {

  public function __construct() {
    // Filtering if user is logged out, redirect to login page
    $this->beforeFilter(
      'auth',
      array('except' => [])
    );
  }

  public function showContributions() {
    return \View::make('show-contributions', []);
  }

}