<?php
namespace modules\api\controllers;
use lib\utils\ActionUser;
use lib\log\EventLogger;

class APILogInController extends \BaseController {

	public function verify_credentials() {
		$input = \Input::all(); 
		if(\Auth::validate(['login' => $input["login"], 'password' => $input["password"]])) {
			$user = \User::where('login', '=', $input["login"])->first();
			$user->mobile_token = \Hash::make(str_random(10));
			$user->save();

			/* Registro de logs */
			EventLogger::printEventLogs(null, 'login', ['origin' => 'aplicativo'], 'Web');

			return \Response::json(['login' => $input["login"], 'token' => $user->mobile_token, 'id' => $user->id, 'valid' => 'true', 'msg' => 'Login efetuado com sucesso.']);
		}
		return \Response::json(['login' => $input["login"], 'token' => '', 'id' => '', 'valid' => 'false', 'msg' => 'Usuário ou senha inválidos.']);
	}

	public function validate_mobile_token() {
		$input = \Input::all();
		$user = \User::where('login', '=', $input["login"])->first();
		if(!is_null($user)) {
			if($input["token"] == $user->mobile_token && $input["id"] == $user->id) {
				return \Response::json(['auth' => true]);
			}
		}
		return \Response::json(['auth' => false]);
	}

	public function log_out() {
		$input = \Input::all();
		$user = \User::where('login', '=', $input["login"])->first();
		if(!is_null($user)) {
			if($input["token"] == $user->mobile_token) {
				$user->mobile_token = null;
				$user->save();

				/* Registro de logs */
				EventLogger::printEventLogs(null, 'logout', null, 'Web');

				return \Response::json(['logged_out' => 'true']);
			}
		}
		return \Response::json(['logged_out' => 'false']);
	}
}
