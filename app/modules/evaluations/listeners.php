<?php
  use modules\evaluations\models\Binomial as Binomial;
  use modules\evaluations\models\Evaluation as Evaluation;
  use modules\news\models\News as News;

    Evaluation::created (function ($evaluation) {  
    	$min_id = Binomial::orderBy('id', 'asc')->first();
    	if ( $evaluation->binomial_id == $min_id->id ) {     	
      		 News::registerPhotoEvaluated($evaluation,'evaluated_photo'); 
      	}
  });
