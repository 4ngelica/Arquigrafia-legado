<?php 
namespace lib\utils;
use Occupation;
use UsersRole;
use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Filesystem\Filesystem;

class ActionUser{

	public function userEvents($user_id, $photo_id,$events,$sourcePage, $edit)
	{
		//to get occupation
        $time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $dateLog = date('Y-m-d');
        $arrayOccupation = Occupation::userOccupation($user_id);
        $arrayUsersRoles = UsersRole::valueUserRole($user_id);
        $stringOccupation ="";
        $stringRoles ="";
        $stringOccupation = $this->convertArrayObjectToString($arrayOccupation,'occupation');
        $stringRoles = $this->convertArrayObjectToString($arrayUsersRoles,'name');

        $createResult = $this->createLogDirectory($dateLog,$events);

        if($createResult){

            if (strcmp($edit, "edit") == 0 || strcmp($edit, "insertion") == 0) {
                 $log_info = sprintf('*Acesso do usuário [%d], com ocupação:[%s], e com role: [%s], acessou em :[%s],a partir da página [%s],e realizou as seguinte ação:[%s], da photo: [%d]',$user_id,$stringOccupation,$stringRoles,$time, $sourcePage,$edit,$photo_id);     
            }
             else {
                 $log_info = sprintf('*Acesso do usuário [%d], com ocupação:[%s], e com role: [%s], acessou em :[%s],a partir da página [%s],e realizou as seguinte ação:[%s], da photo: [%d]',$user_id,$stringOccupation,$stringRoles,$time, $sourcePage,$events,$photo_id);     
            } 
        
            $log = new Logger('Download_logger');
            $file = storage_path() . '/logs/'.$dateLog.'/'.$events.'/'.'user_'.$user_id.'.log';
            if (!file_exists($file)) {
                $handle = fopen($file, 'a+');
                fclose($handle);
            }

            $formatter = new LineFormatter("%message%\n", null, false, true);
            $handler = new StreamHandler($file, Logger::INFO);
            $handler->setFormatter($formatter);
            $log->pushHandler($handler);
            $log->addInfo($log_info);

        }        

        return null; 

	}


    public function createLogDirectory($dateLog,$events){

        $dateFile = storage_path() .'/logs/'.$dateLog;

        $filesystem = new Filesystem();
        if (!$filesystem->exists($dateFile)){
            $resultDateFile = $filesystem->makeDirectory($dateFile);
        }else{
            $resultDateFile = true;
        }     

        $eventFile = storage_path() .'/logs/'.$dateLog.'/'.$events;

        if (!$filesystem->exists($eventFile)){
            $resultEventFile = $filesystem->makeDirectory($eventFile);
        }else{
            $resultEventFile = true;
        }

        if($resultDateFile == true && $resultEventFile == true){
            return true;
        }else{
            return false;
        }
    }

    public  function convertArrayObjectToString($array,$atribute){

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

    //public static function

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