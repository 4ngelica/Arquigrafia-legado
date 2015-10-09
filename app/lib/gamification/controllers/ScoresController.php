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
    $users = \User::with($score_type)->paginate(10);
    $count = ($users->getCurrentPage() - 1) * 10 + 1;
    return \View::make('leaderboard')
      ->with(compact('users', 'count', 'score_type', 'current_page'));
  }

  public function getNextPage() {
    $type = \Input::get('type', 'points');
    $users = \User::with($score_type)->paginate(10);
    $page = $scores->getCurrentPage();
    return Response::json([
        'score_type' => $type,
        'users' => $users->getItems(),
        'current_page' => $users->getCurrentPage(),
        'last_page' => $users->getLastPage()
      ]);
  }


}