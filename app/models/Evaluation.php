<?php

class Evaluation extends Eloquent {

	protected $fillable = ['photo_id','evaluationPosition','binomial_id','user_id','knownArchitecture'];

	protected $table = 'binomial_evaluation';

	public $timestamps = false;

	public function binomial()
	{
		return $this->hasOne('Binomial');
	}

	public function user()
	{
			return $this->belongsTo('User');
	}

	public function photo()
	{
		return $this->hasOne('Photo');
	}

	public static function average($id) {
		 return DB::table('binomial_evaluation')
			->select('binomial_id', DB::raw('avg(evaluationPosition) as avgPosition'))
			->where('photo_id', $id)
			->orderBy('binomial_id', 'asc')
			->groupBy('binomial_id')->get();
	}


	public static function userKnowsArchitecture($photoId,$userId){
		   $result = DB::table('binomial_evaluation')
			->select('knownArchitecture')
			->where('photo_id', $photoId)
			->where('user_id',$userId)->get();
		   if($result != null && $result[0] != null && $result[0]->knownArchitecture == 'yes'){
		   		return true;
		   }else{
		   		return false;
		   }
		   	
	}

	public static function averageAndUserEvaluation($photoId,$userId){

			$avgPhotosBinomials = DB::table('binomial_evaluation')
			->select('binomial_id', DB::raw('avg(evaluationPosition) as avgPosition'))
			->where('photo_id', $photoId)
			->orderBy('binomial_id', 'asc')
			->groupBy('binomial_id')->get();
			//dd($avgPhotosBinomials);
	
			$evaluations = null;
			if($userId != null){
			$evaluations = DB::table('binomial_evaluation')
			->select('id','photo_id','evaluationPosition','binomial_id','user_id')
			->where('user_id', $userId)
			->where("photo_id", $photoId)
			->orderBy("binomial_id", "asc")->get();
			//$evaluations = Evaluation::where("user_id", $userId)
			//->where("photo_id", $photoId)->orderBy("binomial_id", "asc")->get(); 
			//$arrayEvaluation = $evaluations->toArray();

			foreach ($evaluations as $valuesEvaluation) {
				foreach ($avgPhotosBinomials as $avgBinomials) {
					if($avgBinomials->binomial_id == $valuesEvaluation->binomial_id){
						$valuesEvaluation->avg = $avgBinomials->avgPosition;
						break;
					}
					
				}
			}

		}
			//dd($evaluations);
			return $evaluations;			
	}



}