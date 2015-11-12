<?php

class InstitutionsController extends \BaseController {

  public function index()
  {
    $institution = Institution::all();
    return $institution;
  } 

  public function show($id) {
    $institution = Institution::find($id);
    if ( is_null($institution) ) {
      return Redirect::to('/');
    }
    $photos = $institution->photos()->get()->reverse(); 
     
  /* 
    if(Session::has('institutionId')){
        if(Session::get('institutionId') == $id)
          $follow = false;
        else
          $follow = true;
      }else{
        $follow = true;
      } 
 
       if(Session::has('institutionId')){
            $institution_id = Session::get('institutionId');
            $institution_or_visitor = "institution";
        }else{
            $institution_or_visitor = "visitor";
            session_start();
            $user_id = session_id();
        }
          
    //$source_page = Request::header('referer');
    //ActionUser::printSelectUser($institution_id, $id, $source_page, $institution_or_visitor);

      */

    return View::make('institutions.show', [
      'institution' => $institution,
      'photos' => $photos
    ]);
  }
}