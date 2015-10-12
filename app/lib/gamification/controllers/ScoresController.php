<?php namespace lib\gamification\controllers;

use lib\gamification\models\Score;

class ScoresController extends \BaseController {
  
  public function getRankEval(){
    $users = array();
    $u = \User::orderBy('nb_eval','DESC')->take(10)->get();
    $i = 0;
    foreach ($u as $user) {
      $info = array();
      $info['id'] = $user->id ;
      $info['score'] = $user->nb_eval;
      $info['image'] = $user->photo;
      $info['name'] = $user->name;
      $users[] = $info;
    }
 
    return \Response::json($users);
  }

  public function getLeaderboard() {
    $score_type = \Input::get('type', 'points');
    if ( ! in_array($score_type, ['points', 'uploads', 'evals']) ) {
      $score_type = 'points';
    }
    $users = \User::with($score_type)->paginate(10);
    $count = ($users->getCurrentPage() - 1) * 10 + 1;
    if ( \Request::ajax() ) {
      return $this->getNextPage($score_type, $users, $count);
    }
    return \View::make('leaderboard')
      ->with(compact('users', 'count', 'score_type', 'current_page'));
  }

  public function getNextPage($score_type, $users, $count) {
    $view = \View::make('leaderboard_users')
      ->with(compact('score_type', 'users', 'count'))
      ->render();
    $current_page = $users->getCurrentPage();
    return \Response::json(compact('score_type', 'current_page', 'view'));
  }


}