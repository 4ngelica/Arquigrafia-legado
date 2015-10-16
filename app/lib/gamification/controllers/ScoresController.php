<?php namespace lib\gamification\controllers;

use lib\gamification\models\Score;

class ScoresController extends \BaseController {

  public function getLeaderboard() {
    $score_type = \Input::get('type', 'points');
    if ( ! in_array($score_type, ['points', 'uploads', 'evals']) ) {
      $score_type = 'points';
    }
    $user_page = 0;
    $following = 0;
    $isFollowing = \Input::has('following');
    if ( \Auth::check() ) {
      $following = $isFollowing ? \Auth::id() : 0;
      $u = \User::sortBy($score_type, 'id', $following)->get();
      $location = array_search(\Auth::id(), $u->lists('id'));
      $user_page = $location === false ? 0 : intval($location / 10) + 1;
    }
    $users = \User::sortBy($score_type, '*', $following)->paginate(10);
    $count = ($users->getCurrentPage() - 1) * 10 + 1;
    if ( \Request::ajax() ) {
      return $this->getNextPage($score_type, $users, $count, $user_page, $following, $isFollowing);
    }  
    return \View::make('leaderboard')
      ->with(
        compact('users', 'count', 'score_type',
          'user_page', 'current_page', 'following', 'isFollowing')
      );
  }

  public function getNextPage($score_type, $users, $count, $user_page, $following, $isFollowing) {
    $view = \View::make('leaderboard_users')
      ->with(compact('score_type', 'users', 'count'))
      ->render();
    $current_page = $users->getCurrentPage();
    return \Response::json(
      compact('score_type', 'current_page', 'view', 'user_page', 'following')
    );
  }


}