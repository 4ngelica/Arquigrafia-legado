<?php 
namespace lib\utils;
use Occupation;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class ActionUser{

	public static function userEvents($user_id, $photo_id,$events,$sourcePage)
	{
		//to get occupation
        $arrayOccupation = Occupation::userOccupation($user_id);
        if(!empty($arrayOccupation)) {
            
            foreach ($arrayOccupation as $value) {
                 //echo $value->occupation;
            }
           
        }else{
            echo ""; 
        }

      
         
         

        $log_info = sprintf('[%s][%d][%d][%s]', date('Y-m-d'), $user_id, $photo_id,$sourcePage);
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
}