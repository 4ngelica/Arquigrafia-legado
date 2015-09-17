<?php

class InstitutionsController extends \BaseController {

  public function show($id) {
    $institution = Institution::find($id);
    if ( is_null($institution) ) {
      return Redirect::to('/');
    }
    $photos = $institution->photos()->get()->reverse();
    // if (Auth::check()) {
    //     $user_id = Auth::user()->id;
    //     $user_or_visitor = "user";
    // }
    // else { 
    //     $user_or_visitor = "visitor";
    //     session_start();
    //     $user_id = session_id();
    // }
    // $source_page = Request::header('referer');
    // ActionUser::printSelectUser($user_id, $id, $source_page, $user_or_visitor);

    //verificar seguir instituição
    //log de ações

    return View::make('institutions.show', [
      'institution' => $institution, 'photos' => $photos,
      // 'evaluatedPhotos' => Photo::getEvaluatedPhotosByUser($user),
      // 'lastDateUpdatePhoto' => Photo::getLastUpdatePhotoByUser($id),
      // 'lastDateUploadPhoto' => Photo::getLastUploadPhotoByUser($id)
      ]);
  }
}