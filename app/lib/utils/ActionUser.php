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
}