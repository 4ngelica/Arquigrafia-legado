<?php
namespace modules\api\controllers;
use modules\collaborative\models\Tag;

class APIReportController extends \BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($name)
	{
		
	}



	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	/**
	 * Report the photo
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function report($id)
	{
		\Input::flash();
	    $input = \Input::all();
	    
		$rules = array(
	        'data_type_report' => 'required',
	        'type_report'      => 'required',
	        'user_id'          => 'required'
	    ); 
	    $validator = \Validator::make($input, $rules);   

		if ($validator->fails()) {
	      $messages = $validator->messages();
	      return \Response::json(array(
				'code' => 400,
				'message' => 'Erro ao validar.'));
	    } else {
			$photo_id = $id;
	        $user = $input['user_id'];
			$photo = Photo::find($photo_id);
			$reportTypeDataAll =$input["data_type_report"];
			$reportType =$input["type_report"];
	        $comment =$input["observation_report"];        
	        //$reportTypeData = implode(",", array_values($reportTypeDataAll));
			$result = Report::getFirstOrCreate($user, $photo, $reportTypeData, $comment,$reportType);
	    	return \Response::json(array(
				'code' => 200,
				'message' => 'Imagem reportada com sucesso'));
	    }
	}
}