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

    //funções em teste e desenvolvimento

    public static function printInitialStatment($file_path, $user_id, $source_page) {
        $occupation_array = Occupation::userOccupation($user_id);
        $roles_array = UsersRole::valueUserRole($user_id);
        $user_occupation ="";
        $user_roles ="";
        $user_occupation = ActionUser::convertArrayObjectToString($occupation_array,'occupation');
        $user_roles = ActionUser::convertArrayObjectToString($roles_array,'name');
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $info = sprintf('[%s] Acesso do usuário de ID nº: [%d], com ocupação [%s] e role [%s], a partir de [%s].', $date_and_time, $user_id, $user_occupation, $user_roles, $source_page);
        
        $log = new Logger('Access_logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function createDirectoryAndFile($date, $user_id, $source_page) {
        $dir_path =  storage_path() . '/logs/' . $date;
        $file_path = storage_path() . '/logs/' . $date . '/' . 'user_' . $user_id . '.log';

        $filesystem = new Filesystem();
        if(!$filesystem->exists($dir_path)) {
            $dir_created = $filesystem->makeDirectory($dir_path);
        }
        if(!$filesystem->exists($file_path)) {
            $handle = fopen($file_path, 'a+');
            fclose($handle);
            ActionUser::printInitialStatment($file_path, $user_id, $source_page);
        }
        return $file_path;
    }

    public static function verifyTimeout($file_path, $user_id, $source_page) {
        if ($user_id == 0) return;
        $data = file($file_path);
        $line = $data[count($data)-1];
        sscanf($line, "[%s %s]", $date, $time);
        list($last_hour, $last_minutes, $last_seconds) = explode(":", $time);
        $last_seconds = explode("]", $last_seconds);
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        list($today, $now) = explode(" ", $date_and_time);
        list($now_hour, $now_minutes, $now_seconds) = explode(":", $now);
        if ((int)$now_hour - (int)$last_hour > 1) {
            $result = "Timeout atingido, novo acesso detectado";
            $log = new Logger('Timeout_logger');
            ActionUser::addInfoToLog($log, $file_path, $result);
            ActionUser::printInitialStatment($file_path, $user_id, $source_page);
        }
        elseif ((int)$now_hour - (int)$last_hour == 0) {
            if(abs((int)$now_minutes - (int)$last_minutes) > 20) {
                $result = "Timeout atingido, novo acesso detectado";
                $log = new Logger('Timeout_logger');
                ActionUser::addInfoToLog($log, $file_path, $result);
                ActionUser::printInitialStatment($file_path, $user_id, $source_page);
            }
        }
        elseif ((60-(abs((int)$now_minutes - (int)$last_minutes))) > 20) {
            $result = "Timeout atingido, novo acesso detectado";
            $log = new Logger('Timeout_logger');
            ActionUser::addInfoToLog($log, $file_path, $result);
            ActionUser::printInitialStatment($file_path, $user_id, $source_page);
        }
    }

    public static function addInfoToLog($log, $file_path, $info) {
        $formatter = new LineFormatter("%message%\n", null, false, true);
        $handler = new StreamHandler($file_path, Logger::INFO);
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);
        $log->addInfo($info);
    }

    public static function printUploadOrDownloadLog($user_id, $photo_id, $source_page, $up_or_down) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        $info = sprintf('[%s] ' . $up_or_down . ' da foto de ID nº: %d, pela página %s', $date_and_time, $photo_id, $source_page);

        $log = new Logger('UpOrDownload_logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function printFollowOrUnfollowLog($user_id, $target_user_id, $source_page, $follow_or_unfollow) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        $info = sprintf('[%s] ' . $follow_or_unfollow . ' no usuário de ID nº: %d, pela página %s', $date_and_time, $target_user_id, $source_page);

        $log = new Logger('FollowOrUnfollow_logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function printSelectPhoto($user_id, $photo_id, $source_page) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        ActionUser::verifyTimeout($file_path, $user_id, $source_page);
        $info = sprintf('[%s] selecionou a foto de ID nº: %d, pela página %s', $date_and_time, $photo_id, $source_page);

        $log = new Logger('Select logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function printLoginOrLogout($user_id, $source_page, $login_or_logout, $arquigrafia_facebook_stoa) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        $info = sprintf('[%s] ' . $login_or_logout .' através do ' . $arquigrafia_facebook_stoa . ', pela página %s', $date_and_time, $source_page);

        $log = new Logger('LoginOrLogout logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function printSelectUser($user_id, $target_user_id, $source_page) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        $info = sprintf('[%s] selecionou o usuário de ID nº: %d, pela página %s', $date_and_time, $target_user_id, $source_page);

        $log = new Logger('SelectUser logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function printComment($user_id, $source_page, $inserted_edited_deleted, $comment_id, $photo_id) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        $info = sprintf('[%s] '. $inserted_edited_deleted . ' o comentário de ID nº: %d, na foto de ID nº: %d, pela página %s', $date_and_time, $comment_id, $photo_id, $source_page);

        $log = new Logger('Comment logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }

    public static function printSearch($user_id, $source_page, $key_words) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        $date_only = date('Y-m-d');
        $file_path = ActionUser::createDirectoryAndFile($date_only, $user_id, $source_page);
        $info = sprintf('[%s] buscou pelas palavras: ' . $key_words . '; pela página %s', $date_and_time, $source_page);

        $log = new Logger('Search logger');
        ActionUser::addInfoToLog($log, $file_path, $info);
    }
}