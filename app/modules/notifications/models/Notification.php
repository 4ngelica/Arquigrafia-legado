<?php
namespace modules\notifications\models;
use User;
use Photo;

class Notification extends \Eloquent{

	protected $fillable = ['type', 'sender_id', 'sender_type', 'object_id', 'objetc_type', 'data', 'created_at', 'updated_at'];

	public function user(){
		return $this->belongsToMany('User', 'notification_user');
	}
}