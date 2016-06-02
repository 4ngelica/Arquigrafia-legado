<?php
namespace modules\news\models;
use User;
use Log;
use \Session;
use Illuminate\Support\Collection as Collection;
use Carbon\Carbon; 

class News extends \Eloquent {
	protected $table = 'news';
	protected $fillable = array('object_type', 'object_id', 'user_id', 'sender_id', 'news_type');

	public function user()
    {
        return $this->belongsTo('User');
    }

    public static function registerPhotoInstitutional($photo, $type)
    {	
    	$institutional_news = Static::user0NewsPhoto($photo,$type)->get();
    	
        foreach ($institutional_news as $note) 
        {
          Log::info("note-newType=".$note->news_type." sender_id=".$note->sender_id." int=".Session::get('institutionId') );
          if($note->news_type == $type && 
          	 $note->sender_id == Session::get('institutionId')) {
          		Log::info("enter");
             	$curr_note = $note;
            }
        }
        if(isset($curr_note)) {
        	Log::info("updte");
        	$currentNews = Static::specificNews($curr_note->id)->first();
        	//dd($currentNews); 
        	$currentNews->updateCurrentNews($photo);
            /*\DB::table('news')
            ->where('id', $curr_note->id)
            ->update(
            	array('object_id' => $photo->id,
             'updated_at' => Carbon::now('America/Sao_Paulo'))); */
        }else { 
        	Log::info("creat");
        	Static::createNews('Photo',$photo->id,0,$photo->institution_id,$type);
        }

    }

    public static function createNews($objectType, $objectId, $userId, $senderId, $type)
  	{
		$news = new News();
		$news->object_type = $objectType;
        $news->object_id = $objectId;
        $news->user_id = $userId;
        $news->sender_id = $senderId;
        $news->news_type = $type;
        $news->save();    
	}

    public function updateCurrentNews($photo)
    {
    	$this->object_id = $photo->id;
        $this->updated_at = Carbon::now('America/Sao_Paulo');
        $this->save();
    	
    }

    public function scopeUser0NewsPhoto($query, $photo, $type) 
    {   	return $query->where('user_id', '=', 0)
					->where('news_type', '=',$type );
    }	

    public static function scopeSpecificNews($query, $currentId) 
    {   //if ($news instanceof News) {
			return $query->where('id', '=', $currentId);
		//} 
    }

    
}
?>