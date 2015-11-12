<?php
class News extends Eloquent {
	protected $table = 'news';
	protected $fillable = array('object_type', 'object_id', 'user_id', 'sender_id', 'news_type');

	public function user()
    {
        return $this->belongsTo('User');
    }
}
?>