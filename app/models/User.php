<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
// begin 12/05/2015 for date msy
use lib\date\Date;


class User extends Eloquent implements UserInterface, RemindableInterface {
	
	protected $fillable = ['id','name','email','password','login','verify_code'];

	public function notifications()
    {
        return $this->hasMany('\Tricki\Notification\Models\NotificationUser');
    }

	public function photos()
	{
		return $this->hasMany('Photo');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}
	public function evaluations()
	{
		return $this->hasMany('Evaluation');
	}

	public function likes()
	{
		return $this->hasMany('Like');
	}
	
	public function albums()
	{
		return $this->hasMany('Album');
	}

	public function occupation()
	{
		return $this->hasOne('Occupation');
	}

	public function badges()
	{
		return $this->belongsToMany('Badge','user_badges');
	}

	//seguidores
	public function followers()
	{
		return $this->belongsToMany('User', 'friendship', 'followed_id', 'following_id');
	}

	//seguindo
	public function following()
	{
		return $this->belongsToMany('User', 'friendship', 'following_id', 'followed_id');
	}
	//begin 12/05/2015 format date msy
	public static function formatDate($date)
	{
		return Date::formatDate($date);
	}
	//end
	use UserTrait, RemindableTrait;

	protected $hidden = array('password', 'remember_token');

	public static function checkOldAccount( $user, $password)
	{
		$verify = exec('java -cp "' . public_path() . '/java:' . public_path() . '/java/jasypt-1.7.jar" PasswordValidator ' . $password . ' ' . $user->password);
		if ( strcmp($verify, 'true') == 0 ) return true;
		return false;
	}

	public static function stoa($stoa_user) {

		$user = User::where('login', 'stoa_' . $stoa_user->nusp)->first();

		if (!$user) {
			$user = User::newStoaUser($stoa_user);
		}

		if ($stoa_user->image_base64) {
			User::saveProfileImage($user, $stoa_user->image_base64);
		}

		return $user;
	}

	private static function newStoaUser($stoa_user) {
		$user = new User();
		$user->name = $stoa_user->first_name;
		$user->email = $stoa_user->email;
		$user->password = 'stoa';
		$user->login = 'stoa_' . $stoa_user->nusp;
		$user->id_stoa = 'stoa_' . $stoa_user->nusp;
		if ($stoa_user->surname)
			$user->name = $user->name . ' ' . $stoa_user->surname;
		if ($stoa_user->homepage)
			$user->site = $stoa_user->homepage;
		$user->save();

		return $user;
	}

	private static function saveProfileImage($user, $image) {
		$user->photo = "/arquigrafia-avatars/".$user->id.".jpg";
		$user->save();
		$image = Image::make(base64_decode($image))->encode('jpg', 80);
		$image->save(public_path().'/arquigrafia-avatars/'.$user->id.'.jpg');
		$image->save(public_path().'/arquigrafia-avatars/'. $user->id."_original.jpg");
	}

	public function equal($user) {
		try {
			return ($this->id == $user->id);
		} catch (Exception $e) {
			return false;
		}
	}

	public static function userInformation($login){
		
		$user = User::where('login','=',$login)
          ->orWhere('email','=',$login)
          ->first();

          return $user;
	}
	

	public static function userInformationObtain($email){
		$user = User::where('email','=',$email)->first();
          return $user;
	}

	public static function userVerifyCode($verify_code){
		$newUser = User::where('verify_code','=',$verify_code)->first();			
        return $newUser;

	}

	public function getPostionRank()
{
    return ($this->newQuery()->where('nb_eval', '>=', $this->nb_eval)->count());
}

	public function getDignity($number){
		$title="";
		if(2<=$number && $number<5){
	    	 $title='Iniciante';
	     }
	    if(5<=$number && $number<10){
	         $title='Veterano';
	    }
	    if(10<=$number && $number<20){
	        $title='Arquiteto';
	    }
	    if(20<=$number && $number<50){
	        $title='Especialista';
	    }
	    if(50<=$number && $number<100){
	        $title='Professor';
	    }
	    if($number==100){
	        $title='Master';
	    }
	    if($number>=100){
	        $title='Rei';
	    }
	    return $title;
}

}