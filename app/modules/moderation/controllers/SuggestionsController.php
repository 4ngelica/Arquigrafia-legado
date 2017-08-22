<?php

namespace modules\moderation\controllers;

use lib\log\EventLogger;
use Carbon\Carbon;
use Photo;
use User;
use modules\moderation\models\Suggestion;
use modules\moderation\models\PhotoAttributeType;
use modules\notifications\models\Notification;

class SuggestionsController extends \BaseController {


	public function store(){
		$input = \Input::all();

		$rules = array(
			'user_id' => 'required',
			'photo_id' =>'required',
			'attribute_type' => 'required',
			'text' => 'required'
		);

		$validator = \Validator::make($input, $rules);
		if($validator->fails()){
			return \Response::make($validator->messages(), 400);
		}

		$suggestion = new Suggestion();
		//user_id
		$suggestion->user_id = $input['user_id'];
		//photo_id
		$suggestion->photo_id = $input['photo_id'];
		//attribute_type
		$attribute = PhotoAttributeType::where('attribute_type', '=', $input['attribute_type'])->first();

		$suggestion->attribute_type = $attribute->id;
		//text
		$suggestion->text = $input['text'];
		//moderator_id
		//TO DO
		$suggestion->save();
		$photo = $suggestion->photo;
		EventLogger::printEventLogs(null, 'completion', ['suggestion' => $suggestion->id], 'Web');
		return \Response::json('Data sent successfully');
	}

  public function sendNotification() {
    $input = \Input::all();
    $user = \Auth::user();
    $photo = Photo::find($input['photo']);
		$owner = $photo->user;
		$points = $input['points'];
		$status = $input['status'];
		$suggestions = $input['suggestions'];

		if($status == 'none') {
			EventLogger::printEventLogs(null, 'completion-none', null, 'Web');
		}
		else {
			if($status == 'complete'){
				EventLogger::printEventLogs(null, 'completion-complete', ['suggestions' => $suggestions], 'Web');
			}	elseif($status == 'incomplete'){
				EventLogger::printEventLogs(null, 'completion-incomplete', ['suggestions' => $suggestions], 'Web');
			}
			$email = $owner->email;
			\Notification::create('suggestionReceived', $user, $photo, [$owner], null);
			\Mail::send('emails.users.suggestion-received', array('name' => $owner->name, 'email' => $owner->email, 'id' => $owner->id, 'user' => $user->name, 'image' => $photo->name),
				 function($msg) use($email) {
					 $msg->to($email)
							 ->subject('[Arquigrafia]- Recebimento de Sugestão');
			 });

	    $photosObj = Photo::where('accepted', 0)->where('type', '<>', 'video')->whereNull('institution_id')->orderByRaw("RAND()")->take(150)->get()->shuffle();
			$i = 0;
			$photosFiltered = array();
			$i = 0;
			while(count($photosFiltered) < 3 && $i < count($photosObj)){
				if(!$photosObj[$i]->checkPhotoReviewing()){
					$photosFiltered[] = $photosObj[$i];
				}
				$i++;
			}

			$photos = array();
			foreach ($photosFiltered as $photo) {
				$photos[] = ['id' => $photo->id, 'name' => $photo->name, 'nome_arquivo' => $photo->nome_arquivo];
			}
	    return \Response::json($photos);
		}
	}

	public function edit(){
		if(!\Auth::check())
			return \Redirect::to('/users/login');
		$user = \Auth::user();
		$photos = $user->photos->lists('id');
		//$photos = [1];
		$suggestions = Suggestion::whereNull('accepted')->whereIn('photo_id', $photos)->get();
		//dd($suggestions);
		$final = [];
		foreach ($suggestions as $suggestion){
			$field = PhotoAttributeType::find($suggestion->attribute_type)->attribute_type;
			$field_name = Photo::$fields_data[$field]['name'];
			$photo = $suggestion->photo;
			$final[] = ['suggestion' => $suggestion, 'photo' => $photo, 'field' => $field, 'field_name' => $field_name];
		}

		return \View::make('show-suggestions', ['suggestions' => $final]);
	}

	public function update(){
		$input = \Input::all();
		$id_self = \Auth::user()->id;
		$suggestion = Suggestion::find($input['suggestion_id']);
		$photo = $suggestion->photo;
		$user = $photo->user;
		$status = $input['operation'];

		if($status == 'accepted'){
			$field = PhotoAttributeType::find($suggestion->attribute_type)->attribute_type;
			$suggestion->accepted = true;
			$suggestion->save();
			Photo::updateSuggestion($field, $suggestion->text, $suggestion->photo_id);
			\Notification::create('suggestionAccepted', $user, $photo, [$suggestion->user], null);
		}
		if($status == 'rejected'){
			$suggestion->accepted = false;
			$suggestion->save();
			\Notification::create('suggestionDenied', $user, $photo, [$suggestion->user], null);
		}
		return \Redirect::to('/users/suggestions');
	}
}
