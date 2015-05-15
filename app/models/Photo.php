<?php

use lib\date\Date;
use lib\metadata\Exiv2;
use lib\license\CreativeCommons_3_0;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Photo extends Eloquent {

	use SoftDeletingTrait;

	protected $softDelete = true;

	protected $dates = ['deleted_at'];

	protected $fillable = ['user_id','name', 'description', 'nome_arquivo','state','street', 'tombo', 'workAuthor', 'workdate', 'dataUpload', 'dataCriacao', 'country', 'collection', 'city'];

	static $allowModificationsList = [
		'YES' => ['Sim', ''],
		'YES_SA' => ['Sim, contanto que os outros compartilhem de forma semelhante', '-sa'],
		'NO' => ['Não', '-nd']
	];

	static $allowCommercialUsesList = [
		'YES' => ['Sim', ''],
		'NO' => ['Não', '-nc']
	];

	public function user()
	{
		return $this->belongsTo('User');
	}	

	public function tags()
	{
		return $this->belongsToMany('Tag', 'tag_assignments');
	}
	
	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function albums()
	{
		return $this->belongsToMany('Album', 'album_elements');
	}

	public function evaluations()
	{
		return $this->hasMany('Evaluation');
	}

	public static function formatDate($date)
	{
		return Date::formatDate($date);
	}

	public static function translate($date) {
		return Date::translate($date);
	}

	public function saveMetadata($originalFileExtension)
	{
		$user = $this->user;
		$exiv2 = new Exiv2($originalFileExtension, $this->id, public_path() . '/arquigrafia-images/');	
		$exiv2->setImageAuthor($this->workAuthor);
		$exiv2->setArtist($this->workAuthor, $user->name);
		$exiv2->setCopyRight($this->workAuthor,
			new CreativeCommons_3_0($this->allowCommercialUses, $this->allowModifications));
		$exiv2->setDescription($this->description);
		$exiv2->setUserComment($this->aditionalImageComments);		
	}

	public static function paginateUserPhotos($user, $perPage = 24) {
		return Photo::where('user_id', '=', $user->id)
			->paginate($perPage);
	}

	public static function paginateAlbumPhotos($album, $perPage = 24) {
		return $album->photos()->paginate($perPage);
	}

	public static function paginateOtherPhotos($user, $photos, $perPage = 24) {
		return Photo::where('user_id', '=', $user->id)
			->whereNotIn('id', $photos->modelKeys())
			->paginate($perPage);
	}

	public static function composeArchitectureName($name) {
		$array = explode(" ", $name);
		$architectureName = "";			
		if (!is_null($array) && !is_null($array[0])) {		
			if (ends_with($array[0], 'a') || ends_with($array[0], 'dade')
				|| ends_with($array[0], 'ção') || ends_with($array[0], 'ase')
				|| ends_with($array[0], 'ede') || ends_with($array[0], 'dral')
				|| ends_with($array[0], 'agem') || $array[0] == "fonte"
				|| $array[0] == "Fonte" || $array[0] == "ponte"
				|| $array[0] == "Ponte")
				$architectureName = 'a ';
			else if (ends_with($array[0], 's'))
				$architectureName = 'a arquitetura de ';	
			else
				$architectureName = 'o ';
		}
		return $architectureName = $architectureName .$name;
	}

	public static function getEvaluatedPhotosByUser($user) {
		$evaluations =  Evaluation::where("user_id", $user->id)->groupBy('photo_id')->distinct()->get();
    	return Photo::whereIn('id', $evaluations->lists('photo_id'))->get();
	}

//msy
	public static function photosWithSimilarEvaluation($average) { 		
		$similarPhotos = array();
		$arrayPhotosId = array();
		$arrayPhotosDB = array();
		
		$i=0;
		foreach ($average as $avg) {                    
			Log::info("Logging params ".$avg->binomial_id." ".$avg->avgPosition);
			//media de fotos x binomio
			$avgPhotosBinomials = DB::table('binomial_evaluation')
			->select('photo_id', DB::raw('avg(evaluationPosition) as avgPosition'))
			->where('binomial_id', $avg->binomial_id)				
			->groupBy('photo_id')->get();

			
			$arrayPhotosId = array();
			$flag=false;

			foreach ($avgPhotosBinomials as $avgPhotoBinomial) {
				//Log::info("Logging iterate avgPhotoBinomial pos ".$avgPhotoBinomial->avgPosition." param ".$avg->avgPosition);
				if(abs($avgPhotoBinomial->avgPosition - $avg->avgPosition)<=5){
					$flag=true;
					//Log::info("Logging push ".$avgPhotoBinomial->photo_id);
					array_push($arrayPhotosId,$avgPhotoBinomial->photo_id);

				}
			}
			
			if($flag == false){
				Log::info("Logging break "); 
				$similarPhotos = array();
				break;
			}

			if($i==0){				
				$similarPhotos = $arrayPhotosId;			
			}

			$similarPhotos = array_intersect($similarPhotos, $arrayPhotosId);
			$i++;
			
		}
		//To remove repeted values
		$similarPhotos = array_unique($similarPhotos);



		//To obtain name of similarPhotos
		foreach ($similarPhotos  as $similarPhotosId ) {

			$similarPhotosDB = DB::table('photos')
			->select('id', 'name')
			->where('id',$similarPhotosId )				
			->get(); 

			array_push($arrayPhotosDB,$similarPhotosDB[0]);

		}
		
    	return $arrayPhotosDB;
	}

}