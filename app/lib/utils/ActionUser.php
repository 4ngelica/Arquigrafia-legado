<?php 
namespace lib\utils;
use Occupation;
use UsersRole;
use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class ActionUser{

	public static function userEvents($user_id, $photo_id,$events,$sourcePage, $edit)
	{
		//to get occupation
        $time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $arrayOccupation = Occupation::userOccupation($user_id);
        $arrayUsersRoles = UsersRole::valueUserRole($user_id);
        $stringOccupation ="";
        $stringRoles ="";
        $stringOccupation = static::convertArrayObjectToString($arrayOccupation,'occupation');
        $stringRoles = static::convertArrayObjectToString($arrayUsersRoles,'name');
        //dd($stringRoles);
        if (strcmp($edit, "edit") == 0 || strcmp($edit, "insertion") == 0) {
            $log_info = sprintf('[%s][%d][%d][%s][%s][%s][%s]', $time, $user_id, $photo_id, $sourcePage, $stringOccupation, $stringRoles, $edit);
        }
        else {
            $log_info = sprintf('[%s][%d][%d][%s][%s][%s]', $time, $user_id, $photo_id, $sourcePage, $stringOccupation, $stringRoles);     
        } 

        $log = new Logger('Download_logger');
        $file = storage_path() . '/logs/'.$events.'/'.$events.'.log';
        if (!file_exists($file)) {
          $handle = fopen($file, 'a+');
          fclose($handle);
        }

        $formatter = new LineFormatter("%message%\n", null, false, true);
        $handler = new StreamHandler($file, Logger::INFO);
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);
        $log->addInfo($log_info);
        return null; 

	}

    public static function convertArrayObjectToString($array,$atribute){

        $string = "";
        $numElement = count($array);
        $i = 1;
        $separator = ', ';
        if(!empty($array)) {             
            foreach ($array as $value) {
                if($numElement == $i){
                    $separator = '';
                }
                 $string = $string.''.$value->$atribute.$separator;
                 $i++;
            }           
        }
        return $string;
    }

    //funções não utilizadas por enquanto, em desenvolvimento

    public static function printInitialStatment($file_path, $user_id, $source_page) {
        $occupation_array = Occupation::userOccupation($user_id);
        $role_array = UsersRole::valueUserRole($user_id);
        $user_occupation ="";
        $user_roles ="";
        $user_occupation = static::convertArrayObjectToString($occupation_array,'occupation');
        $user_roles = static::convertArrayObjectToString($role_array,'name');
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $info = sprintf('Acesso do usuário [$d], com ocupação [%s] e role [%s], as [%s] a partir de [%s].', $user_id, $user_occupation, $user_roles, $date_and_time, $source_page);
        
        $log = new Logger('Download_logger');
        $formatter = new LineFormatter("%message%\n", null, false, true);
        $handler = new StreamHandler($file_path, Logger::INFO);
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);
        $log->addInfo($info);
    }
}