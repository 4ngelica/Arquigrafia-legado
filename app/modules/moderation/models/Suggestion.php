<?php
namespace modules\moderation\models;

class Suggestion extends \Eloquent {
	protected fillable = ['user_id', 'photo_id', 'attribute_type', 'text' , 'accepted', 'moderator_id'];

	public function photo_attribute_type() {
		return $this->belongsTo('PhotoAttributeType');
	}

	public function user(){
		return $this->belongsTo('User');
	}

	public function photo(){
		return $this->belongsTo('Photo');
	}
}