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
     //echo "ss=".!Session::has('institutionId');
    $follow = null;
    $responsible = false;
    if(!Session::has('institutionId') && Auth::check()){
        $userId = Auth::user()->id; 
        $user = User::whereid($userId)->first(); 
        
        if($user->followingInstitution->contains($institution->id))
            $follow = false;
        else
            $follow = true;        
    }
    if(Session::has('institutionId') && Auth::check() && $institution->id == Session::get('institutionId')){
       
       $userId = Auth::user()->id; 
       $responsibleEmployee = institution::RoleOfInstitutionalUser($userId);   
       if(!empty($responsibleEmployee)) 
          $responsible = true;  
    }

    return View::make('institutions.show', [
      'institution' => $institution,
      'photos' => $photos,
      'follow' => $follow,
      'responsible' => $responsible 
    ]);
  }

    public function followInstitution($institution_id)
  {
    $logged_user = Auth::user();    
    if ($logged_user == null)  return Redirect::to('/');

    $following = $logged_user->followingInstitution;   
    if (!$following->contains($institution_id)) { //$institution_id != $logged_user->id 
      $logged_user->followingInstitution()->attach($institution_id);
     // dd($logged_user); die();      
    }      
    return Redirect::to(URL::previous()); 
  }

  public function sendNotification($id){ //$id=instit
      $logged_user = Auth::user();
      if ($id != $logged_user->id) {
          $institution_notified = Institution::find($id);
          foreach ($institution_notified->notifications as $notification) {
              $info = $notification->render();
              if ($info[0] == "follow" && $notification->read_at == null) {
                $note_id = $notification->notification_id;
                $note_user_id = $notification->id;
                $note = $notification;
            }
          }
          if (isset($note_id)) {
               $note_from_table = DB::table("notifications")->where("id","=", $note_id)->get();
              if (NotificationsController::isNotificationByUser($logged_user->id, $note_from_table[0]->sender_id, $note_from_table[0]->data) == false) {
                $new_data = $note_from_table[0]->data . ":" . $logged_user->id;
                DB::table("notifications")->where("id", "=", $note_id)->update(array("data" => $new_data, "created_at" => Carbon::now('America/Sao_Paulo')));
                $note->created_at = Carbon::now('America/Sao_Paulo');
                $note->save();           }
          }
          else Notification::create('follow', $logged_user, $user_note, [$user_note], null);
      }  
      $logged_user_id = Auth::user()->id;
      $pageSource = Request::header('referer');
      ActionUser::printFollowOrUnfollowLog($logged_user_id, $user_id, $pageSource, "passou a seguir", "user");
  }


  public function unfollowInstitution($institution_id)
  {
    $logged_user = Auth::user();    
    if ($logged_user == null)   return Redirect::to('/');

    $following = $logged_user->followingInstitution;  
    
    if ($following->contains($institution_id)) {
      $logged_user->followingInstitution()->detach($institution_id);       
    }
    return Redirect::to(URL::previous()); 
  } 

  public function edit($id) {     
    if (!Session::has('institutionId') ) {
      return Redirect::to('/');
    }

    $institution = Institution::find($id); 
    if ( is_null($institution) )   return Redirect::to('/');
     
    return View::make('institutions.edit', [
      'institution' => $institution      
    ]);
  }

   public function update($id) {              
    $institution = Institution::find($id);
   //dd($institution);
    Input::flash();    
    $input = Input::only('name_institution', 'email', 'site', 'country', 'state', 'city', 
      'photo', 'address', 'phone');    
    
    $rules = array( 'name_institution' => 'required',
            "site" => "url",
            "phone" => "regex:/^[0-9-()]{8,21}$/"   );  

    if ($input['email'] !== $institution->email)        
      $rules['email'] = 'required|email|unique:institutions';

    $validator = Validator::make($input, $rules);   
    if ($validator->fails()) {
      $messages = $validator->messages();      
      return Redirect::to('/institutions/' . $id . '/edit')->withErrors($messages);
    } else {  
      //dd($input);
      $institution->name = $input['name_institution'];      
      $institution->email = $input['email'];     
         
      $institution->country = $input['country'];
      
      if(!empty($input['site']))
         $institution->site = $input['site'];   
      else
         $institution->site = null; 
       
      if(!empty($input['state']))
         $institution->state = $input['state'];   
      else
         $institution->state = null; 

      if(!empty($input['city']))
         $institution->city = $input['city'];   
      else
         $institution->city = null;

      if(!empty($input['address']))
         $institution->address = $input['address'];   
      else
         $institution->address = null;

      if(!empty($input['phone']))
         $institution->phone = $input['phone'];   
      else
         $institution->phone = null;

      $institution->touch();
      $institution->save();   

     

      if (Input::hasFile('photo') and Input::file('photo')->isValid())  {    
        $file = Input::file('photo');
        $ext = $file->getClientOriginalExtension();
        $institution->photo = "/arquigrafia-avatars-inst/".$institution->id.".jpg";
        $institution->save();
        $image = Image::make(Input::file('photo'))->encode('jpg', 80);         
        $image->save(public_path().'/arquigrafia-avatars-inst/'.$institution->id.'.jpg');
        $file->move(public_path().'/arquigrafia-avatars-inst', $institution->id."_original.".strtolower($ext));                
      } 
      return Redirect::to("/institutions/{$institution->id}")->with('message', '<strong>Edição de perfil da instituição</strong><br>Dados alterados com sucesso'); 
      
    }    
  }

}