<?php
namespace lib\log;
use Occupation;
use UsersRole;
use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Filesystem\Filesystem;
use lib\utils\ActionUser;
use Auth;

class EventLogger {
	public static function printInitialStatment($file_path, $user_id, $source_page, $user_or_visitor) {
        $occupation_array = Occupation::userOccupation($user_id);
        $roles_array = UsersRole::valueUserRole($user_id);
        $user_occupation ="";
        $user_roles ="";
        $user_occupation = ActionUser::convertArrayObjectToString($occupation_array,'occupation');
        $user_roles = ActionUser::convertArrayObjectToString($roles_array,'name');
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        if ($user_or_visitor == "user") {
            $info = sprintf('[%s] Acesso do usuário de ID nº: [%d], com ocupação [%s] e role [%s], a partir de [%s].', $date_and_time, $user_id, $user_occupation, $user_roles, $source_page);
        }
        else {
            $info = sprintf('[%s] Acesso de visitante, a partir de [%s].', $date_and_time, $source_page);
        }
        $log = new Logger('Access_logger');
        EventLogger::addInfoToLog($log, $file_path, $info);
    }

    public static function createDirectoryAndFile($date, $user_id, $source_page, $user_or_visitor) {
        $dir_path =  storage_path() . '/logs/' . $date;
        if ($user_or_visitor == "user") {
            $file_path = storage_path() . '/logs/' . $date . '/' . 'user_' . $user_id . '.log';
        }
        elseif ($user_or_visitor == "visitor") {
            $file_path = storage_path() . '/logs/' . $date . '/' . 'visitor_' . $user_id . '.log';
        }

        $filesystem = new Filesystem();
        if(!$filesystem->exists($dir_path)) {
            $dir_created = $filesystem->makeDirectory($dir_path);
        }
        if(!$filesystem->exists($file_path)) {
            $handle = fopen($file_path, 'a+');
            fclose($handle);
            EventLogger::printInitialStatment($file_path, $user_id, $source_page, $user_or_visitor);
        }
        return $file_path;
    }

    public static function verifyTimeout($file_path, $user_id, $source_page, $user_or_visitor) {
        $data = file($file_path);
        $line = $data[count($data)-1];
        sscanf($line, "[%s %s]", $date, $time);
        list($last_year, $last_month, $last_day) = explode("-", $date);
        list($last_hour, $last_minutes, $last_seconds) = explode(":", $time);
        list($last_seconds, $trash) = explode("]", $last_seconds);
        $date_and_time_now = Carbon::now('America/Sao_Paulo');
        $date_and_time_last = Carbon::create($last_year, $last_month, $last_day, $last_hour, $last_minutes, $last_seconds, 'America/Sao_Paulo');
        if ($date_and_time_now->diffInMinutes($date_and_time_last) > 20) {
            $result = "Timeout atingido, novo acesso detectado";
            $log = new Logger('Timeout_logger');
            EventLogger::addInfoToLog($log, $file_path, $result);
            EventLogger::printInitialStatment($file_path, $user_id, $source_page, $user_or_visitor);
        }
    }

    public static function addInfoToLog($channel, $file_path, $info) {
        $log = new Logger($channel);
        $formatter = new LineFormatter("%message%\n", null, false, true);
        $handler = new StreamHandler($file_path, Logger::INFO);
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);
        $log->addInfo($info);
    }

    public static function printEventLogs($photoId, $eventType, $eventContent, $device) {
        $date_and_time = Carbon::now('America/Sao_Paulo')->toDateTimeString();
        list($date_only) = explode(" ", $date_and_time);

        if(!isset($_SESSION)) session_start();
        $userId = session_id();
        $userType = 'visitor';
        if(Auth::check()) {
            $userType = 'user';
            $userId = Auth::user()->id;
        } elseif ($device == 'mobile') {
            $userType = 'user';
            $userId = $eventContent['user'];
        }
        $sourcePage = \Request::server('HTTP_REFERER');

        $filePath = EventLogger::createDirectoryAndFile($date_only, $userId, $sourcePage, $userType);
        EventLogger::verifyTimeout($filePath, $userId, $sourcePage, $userType);


        switch ($eventType) {
        	case "home":
        		$info = sprintf('[%s] Acessou a home page, pela página %s, via %s',
        						$date_and_time, $sourcePage, $device);
                break;
            case "new_account":
            	$info = sprintf('[%s] Nova conta criada pelo %s, ID nº: %d',
            					$date_and_time, $eventContent['origin'], $userId);
                break;
            case "upload":
                $info = sprintf('[%s] Upload da foto de ID nº: %d, pela página %s, via %s',
                	$date_and_time, $photoId, $sourcePage, $device);
                break;
            case "download":
            	$info = sprintf('[%s] Download da foto de ID nº: %d, pela página %s, via %s',
                	$date_and_time, $photoId, $sourcePage, $device);
                break;
            case "edit":
            	$info = sprintf('[%s] Edição da foto de ID nº: %d, pela página %s, via %s',
                				$date_and_time, $photoId, $sourcePage, $device);
                break;
            case "delete":
            	$info = sprintf('[%s] Deleção da foto de ID nº: %d, pela página %s, via %s',
                				$date_and_time, $photoId, $sourcePage, $device);
                break;
            case "follow":
            	$info = sprintf('[%s] Usuário de ID nº: %d passou a seguir o usuário de ID nº: %d, pela página %s, via %s',
            					$date_and_time, $userId, $eventContent['target_userId'], $sourcePage, $device);
                break;
            case "unfollow":
                $info = sprintf('[%s] Usuário de ID nº: %d deixou de seguir o usuário de ID nº: %d, pela página %s, via %s',
                				$date_and_time, $userId, $eventContent['target_userId'], $sourcePage, $device);
                break;
            case "select_photo":
            	$info = sprintf('[%s] Selecionou a foto de ID nº: %d, pela página %s, via %s',
            					$date_and_time, $photoId, $sourcePage, $device);
                break;
            case "edit_photo":
                $info = sprintf('[%s] Editou a foto de ID nº: %d, pela página %s, via %s',
                                $date_and_time, $photoId, $sourcePage, $device);
                break;
            case "insert_tags":
                $info = sprintf('[%s] Inseriu as tags: %s. Pertencentes a foto de ID nº: %d, via %s',
                				$date_and_time, $eventContent['tags'], $photoId, $device);
                break;
            case "edit_tags":
                $info = sprintf('[%s] Editou as tags: %s. Pertencentes a foto de ID nº: %d, via %s',
                				$date_and_time, $eventContent['tags'], $photoId, $device);
                break;
            case "login":
            	$info = sprintf('[%s] Login através do %s, pela página %s, via %s',
            					$date_and_time, $eventContent['origin'], $sourcePage, $device);
                break;
            case "logout":
            	$info = sprintf('[%s] Logout pela página %s, via %s',
            					$date_and_time, $sourcePage, $device);
                break;
            case "select_user":
            	$info = sprintf('[%s] Selecionou o usuário de ID nº: %d, pela página %s, via %s',
            		            $date_and_time, $eventContent['target_userId'], $sourcePage, $device);
                break;
            case "insert_comment":
             	$info = sprintf('[%s] Inseriu o comentário de ID nº: %d, na foto de ID nº: %d, pela página %s, via %s',
             					$date_and_time, $eventContent['comment_id'], $photoId, $sourcePage, $device);
                break;
            case "edit_comment":
             	$info = sprintf('[%s] Editou o comentário de ID nº: %d, na foto de ID nº: %d, pela página %s, via %s',
             					$date_and_time, $eventContent['comment_id'], $photoId, $sourcePage, $device);
                break;
            case "delete_comment":
             	$info = sprintf('[%s] Deletou o comentário de ID nº: %d, na foto de ID nº: %d, pela página %s, via %s',
             					$date_and_time, $eventContent['comment_id'], $photoId, $sourcePage, $device);
                break;
            case "search":
            	$info = sprintf('[%s] Buscou por %d palavra(s): %s; pela página %s, via %s',
            					$date_and_time, $eventContent['search_size'], $eventContent['search_query'], $sourcePage, $device);
                break;
            case "like":
            	$info = sprintf('[%s] Curtiu %s, ID nº: %d, pela página %s, via %s',
                                $date_and_time, $eventContent['target_type'], $eventContent['target_id'], $sourcePage, $device);
                break;
            case "dislike":
            	$info = sprintf('[%s] Descurtiu %s, ID nº: %d, pela página %s, via %s',
                                $date_and_time, $eventContent['target_type'], $eventContent['target_id'], $sourcePage, $device);
                break;
            case "insert_evaluation":
            	$info = sprintf('[%s] Inseriu avaliação na foto de ID nº: %d, com os seguintes valores %s pela página: %s, via %s',
                                $date_and_time, $photoId, $eventContent['evaluation'], $sourcePage, $device);
                break;
            case "edit_evaluation":
            	$info = sprintf('[%s] Editou avaliação na foto de ID nº: %d, com os seguintes valores %s pela página: %s, via %s',
                                $date_and_time, $photoId, $eventContent['evaluation'], $sourcePage, $device);
                break;
            case "access_evaluation_page":
            	$info = sprintf('[%s] Acessou a página de avaliação %s da página %s, via %s',
                                $date_and_time, $eventContent['object_source'], $sourcePage, $device);
                break;
            case "access_notification_page":
            	$info = sprintf('[%s] Acessou a página de notificações pela página %s, via %s',
                                $date_and_time, $sourcePage, $device);
                break;
            case "new_thread":
                $info = sprintf('[%s] Criou o chat %s com os usuários %s pela página %s, via %s',
                                $date_and_time, $eventContent['thread'], $eventContent['participants'], $sourcePage, $device);
                break;
            case "new_message":
                $info = sprintf('[%s] Criou a mensagem %s para o chat %s pela página %s, via %s',
                                $date_and_time, $eventContent['message'], $eventContent['thread'], $sourcePage, $device);
                break;
            case "completion":
                $info = sprintf('[%s] Completude de dados de ID nº: %s, pela página %s, via %s',
                                $date_and_time, $eventContent['suggestion'], $sourcePage, $device);
                break;
			case "completion-none":
					$info = sprintf('[%s] Fluxo de cartões - Abandono sem ação pela página %s, via %s',
													$date_and_time, $sourcePage, $device);
					break;
			case "completion-incomplete":
					$info = sprintf('[%s] Fluxo de cartões - Abandono com %s sugestões pela página %s, via %s',
													$date_and_time, $eventContent['suggestions'], $sourcePage, $device);
					break;
			case "completion-complete":
					$info = sprintf('[%s] Fluxo de cartões - Finalizado com %s sugestões pela página %s, via %s',
													$date_and_time, $eventContent['suggestions'], $sourcePage, $device);
					break;
            case "moderation":
                $info = sprintf('[%s] Moderacao de dados no Id: %s, pela página %s, via %s',
                                $date_and_time, $eventContent['suggestion'], $sourcePage, $device);
                break;
            default:
                break;
        }
        EventLogger::addInfoToLog('Logger', $filePath, $info);
    }

}
