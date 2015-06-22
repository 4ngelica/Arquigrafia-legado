	<?php

	class Comment extends Eloquent {

		protected $fillable = ['text', 'user_id', 'photo_id'];

		public function photo()
		{
			return $this->belongsTo('Photo');
		}

		public function user()
		{
			return $this->belongsTo('User');
		}

		public function likes()
		{
			return $this->hasMany('Like');
		}

		public function badge() {
			return $this->belongsTo('Badge');
		}

		public function isLiked(){
			$user_id=Auth::id();
			if (is_null($user_id)){
				return false;
			}
			$like = Like::where('user_id', $user_id)->where('comment_id', $this->id)->first();
			if(is_null($like)){
				return false;
			}
			return true;
		}

		public function attachBadge($badge) {
			$this->badge_id = $badge->id;
			$this->save();
		}
	}