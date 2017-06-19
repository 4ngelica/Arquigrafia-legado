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
		EventLogger::printEventLogs(null, 'completion', ['suggestion' => $suggestion->id], 'Web');
		return \Response::json('Data sent successfully');
	}

  public function sendNotification() {
    $input = \Input::all();
    $user = \Auth::user();
    $photo = Photo::find($input['photo']);
		$points = $input['points'];
    //\Notification::create('suggestion_sent', $user, $photo, [$user], null);

    $photosObj = Photo::where('accepted', 0)->where('type', '<>', 'video')->whereNull('institution_id')->get()->shuffle();
		$i = 0;
		$photosFiltered = array();

		while(count($photosFiltered) < 3 && $i < count($photosObj)){
			$i = 0;
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
