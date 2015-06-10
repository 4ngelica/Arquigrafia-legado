<?php 
namespace lib\utils;
use Occupation;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class ActionUser{

	public static function userEvents($user_id, $photo_id,$events,$sourcePage, $edit)
	{
		//to get occupation
        $arrayOccupation = Occupation::userOccupation($user_id);
        $stringOccupation ="";
       
        $stringOccupation = static::convertArrayObjectToString($arrayOccupation,'occupation');
        
        if (strcmp($edit, "edit") == 0 || strcmp($edit, "insertion") == 0) {
            $log_info = sprintf('[%s][%d][%d][%s][%s][%s]', date('Y-m-d'), $user_id, $photo_id,$sourcePage, $stringOccupation, $edit);
        }
        else {
            $log_info = sprintf('[%s][%d][%d][%s][%s]', date('Y-m-d'), $user_id, $photo_id,$sourcePage,$stringOccupation);     
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