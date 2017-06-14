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
		//accepted
		$suggestion->accepted = false;
		$suggestion->save();
		EventLogger::printEventLogs(null, 'completion', ['suggestion' => $suggestion->id], 'Web');
		return \Response::json('Data sent successfully');
	}

  public function sendNotification() {
    $input = \Input::all();
    $user = \Auth::user();
    $photo = Photo::find($input['photo']);
    \Notification::create('suggestion_sent', $user, $photo, [$user], null);

    $photosObj = Photo::where('accepted', 0)->whereNull('institution_id')->get()->random(3);
		$photos = array();
		foreach ($photosObj as $photo) {
			$photos[] = ['id' => $photo->id, 'name' => $photo->name, 'nome_arquivo' => $photo->nome_arquivo];
		}
    //'type', 'sender_id', 'sender_type', 'object_id', 'objetc_type', 'data'
    return \Response::json($photos);
  }
}
