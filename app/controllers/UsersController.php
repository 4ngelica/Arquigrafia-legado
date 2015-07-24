<?php
use lib\utils\ActionUser;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookRequestException;

class UsersController extends \BaseController {

  public function __construct()
  {
    $this->beforeFilter('auth',
      array('only' => ['follow', 'unfollow']));
  }
  
	public function index()
	{
		$users = User::all();

		return View::make('/users/index',['users' => $users]);
	}

	public function show($id)
	{
		$user = User::whereid($id)->first();
    $photos = $user->photos()->get()->reverse();
    if (Auth::check()) {      
      if (Auth::user()->following->contains($user->id))
        $follow = false;
      else 
        $follow = true;
    } else{ 
      $follow = true;
    }
    
    if (Auth::check()) {
        $user_id = Auth::user()->id;
        $user_or_visitor = "user";
    }
    else { 
        $user_or_visitor = "visitor";
        session_start();
        $user_id = session_id();
    }
    $source_page = Request::header('referer');
    ActionUser::printSelectUser($user_id, $id, $source_page, $user_or_visitor);

    return View::make('/users/show',['user' => $user, 'photos' => $photos, 'follow' => $follow,
      'evaluatedPhotos' => Photo::getEvaluatedPhotosByUser($user),
      'lastDateUpdatePhoto' => Photo::getLastUpdatePhotoByUser($id),
      'lastDateUploadPhoto' => Photo::getLastUploadPhotoByUser($id)
      ]);
	}
  
  // show create account form
  public function account()
  {
    if (Auth::check()) return Redirect::to('/');
    return View::make('/modal/account');
  }

  
  // register of user with email 
  public function store()
  {    
    // put input into flash session for form repopulation
    Input::flash();
    $input = Input::all();
    
    // validate data
    $rules = array(
        'name' => 'required',
        'login' => 'required|unique:users',
        'password' => 'required|min:6|confirmed',
        'email' => 'required|email|unique:users',
        'terms' => 'required'
    );     
    $validator = Validator::make($input, $rules);   
    if ($validator->fails()) {
      $messages = $validator->messages();
      return Redirect::to('/users/account')->withErrors($messages);
    } else {
          
       $name = $input["name"];
       $email =$input["email"];
       $login =$input["login"];
       $verify_code = str_random(30);
      //create user with a verify code      
      User::create([
      'name' => $name,
      'email' => $email,
      'password' => Hash::make($input["password"]),
      'login' => $login,
      'verify_code' => $verify_code       
      ]);

      $user = User::userInformation($login);
      $source_page = Request::header('referer');
      ActionUser::printNewAccount($user->id, $source_page, "arquigrafia", "user"); 

        //send email to user created
       Mail::send('emails.users.verify', array('name' => $name, 'email' => $email, 'login' => $login ,'verifyCode' => $verify_code), 
          function($msg) use($email) {
            $msg->to($email)
                ->subject('[Arquigrafia]- Cadastro de Usuário');
        });
     
        return Redirect::to("/users/register"); 

    }
  }

  public function emailRegister(){
    
    $msgType = "sendEmail";
    return View::make('/modal/register')->with(['msgType'=>$msgType]); 
  

  }

  public function verify($verify_code){
        
    if(!empty($verify_code)){
      $newUser= User::userVerifyCode($verify_code);
    }
    
    if (!$newUser){   
            //erro
            return Redirect::to('users/verify');
      }else{
          //update data of new user registered 
          $newUser->active = 'yes';
          $newUser->verify_code = null;
          $newUser->save();
          
          return Redirect::to('/users/login')->with('msgRegister', "<strong>Conta ativada com sucesso!.</strong>");

      }
  }

  public function verifyError(){

      $msgType = "verify";
      return View::make('/modal/register')->with(['msgType'=>$msgType]); 
  }  


  public function forgetForm()
  {
    $message = false;
    $existEmail = true;
    return View::make('/modal/forget')->with(['message'=>$message, 'existEmail'=>$existEmail]);
  }

  public function forget(){    
    $input = Input::all(); 
    $email = $input["email"];
    $rules = array('email' => 'required|email');

    $validator = Validator::make($input, $rules);   
    if ($validator->fails()) {
      $messages = $validator->messages();
      return Redirect::to('/users/forget')->withErrors($messages);
    }else{

      $user = User::userInformationObtain($email);

      if(!empty($user)){
        $randomPassword = strtolower(Str::quickRandom(8)); 
        $user->password = Hash::make($randomPassword);
        
        $user->touch();
        $user->save();
        Mail::send('emails.users.reset-password', array('user' => $user,'email' => $email,'randomPassword' => $randomPassword),
         function($message) use($email) {  
               $message->to($email)
               //->replyTo($email)
               ->subject('[Arquigrafia] - Esqueci minha senha');  
         }); 
        $message = true; 
        $existEmail = true;
      } else{
        $existEmail = false;
        $message = null;
      } 
      return View::make('/modal/forget')->with(['message'=>$message,'email'=>$email, 'existEmail'=>$existEmail]);
    }
      
    
  }


  
  // formulário de login
  public function loginForm()
  {
    if (Auth::check())
      return Redirect::to('/');

		session_start();
    $fb_config = Config::get('facebook');
    FacebookSession::setDefaultApplication($fb_config["id"], $fb_config["secret"]);
    $helper = new FacebookRedirectLoginHelper(url('/users/login/fb/callback'));
    $fburl = $helper->getLoginUrl(array(
        'scope' => 'email',
    ));

    if (!Session::has('filter.login') && !Session::has('login.message')) //nao foi acionado pelo filtro, retornar para pagina anterior
      Session::put('url.previous', URL::previous());
    
    return View::make('/modal/login')->with(['fburl' => $fburl]);
  }
  

   // validacao do login
  public function login()
  { 
     $input = Input::all();   
     $user = User::userInformation($input["login"]);    
    
    if ($user != null && $user->oldAccount == 1) 
    {
      if ( User::checkOldAccount($user, $input["password"]) )
      {
        $user->oldAccount = 0;
        $user->password = Hash::make($input["password"]);
        $user->save();
      } else {
        Session::put('login.message', 'Usuário e/ou senha inválidos, tente novamente.');
        return Redirect::to('/users/login')->withInput();
      }
    }

    if (Auth::attempt(array('login' => $input["login"], 'password' => $input["password"],'active' => 'yes')) == true || Auth::attempt(array('email' => $input["login"], 'password' => $input["password"],'active' => 'yes')) == true  )
        { 
      if ( Session::has('filter.login') ) //acionado pelo login
      {  
        Session::forget('filter.login');
        $source_page = Request::header('referer');
        ActionUser::printLoginOrLogout($user->id, $source_page, "Login", "arquigrafia", "user");
        return Redirect::intended('/');
      }
        
      if ( Session::has('url.previous') )
      {
        $url = Session::pull('url.previous'); 
        
        if (!empty($url)) {
          $source_page = Request::header('referer');
          ActionUser::printLoginOrLogout($user->id, $source_page, "Login", "arquigrafia", "user");
          //Redirect when user forget password
          if($url == URL::to('users/forget')){ 
            return Redirect::to('/');
          }elseif(!empty($input["firstTime"])){ 
              return Redirect::to('/')->with('msgWelcome', "Bem-vind@ ".ucfirst($user->name).".");
          
          }else{
            return Redirect::to($url);
          }

        }

        
        $source_page = Request::header('referer');
        ActionUser::printLoginOrLogout($user->id, $source_page, "Login", "arquigrafia", "user");
        return Redirect::to('/');
      }
      $source_page = Request::header('referer');
      ActionUser::printLoginOrLogout($user->id, $source_page, "Login", "arquigrafia", "user");
      return Redirect::to('/');
    } else {
			Session::put('login.message', 'Usuário e/ou senha inválidos, tente novamente.');
      return Redirect::to('/users/login')->withInput();
    }
  }
  
  // formulário de login
  public function logout()
  {
    $user_id = Auth::user()->id;
    $source_page = Request::header('referer');
    ActionUser::printLoginOrLogout($user_id, $source_page, "Logout", "arquigrafia", "user");

    Auth::logout();
    Session::flush();
    return Redirect::to('/');
  }
  
  // facebook login NÃO ESTA SENDO USADO
  public function facebook()
  {
    $fb_config = Config::get('facebook');
    $facebook = new Facebook( $fb_config );
    
    $params = array(
        'redirect_uri' => url('/users/login/fb/callback'),
        'scope' => 'email',
    );
    return Redirect::to($facebook->getLoginUrl($params));
  }
	
	// facebook login callback
	public function callback() 
	{
    session_start();
    
    $fb_config = Config::get('facebook');
    
    FacebookSession::setDefaultApplication($fb_config["id"], $fb_config["secret"]);
    
    $helper = new FacebookRedirectLoginHelper(url('/users/login/fb/callback'));
    
    try {
      $session = $helper->getSessionFromRedirect();
    } catch(FacebookRequestException $ex) {
      // When Facebook returns an error
      dd($ex);
    } catch(\Exception $ex) {
      // When validation fails or other local issues
      dd($ex);
    }
    if ($session) {
      // Logged in
      $request = new FacebookRequest($session, 'GET', '/me');
      $response = $request->execute();
      $fbuser = $response->getGraphObject();
      $fbid = $fbuser->getProperty('id');
      
      //usuarios antigos tem campo id_facebook null, mas existe login = $fbid;
      $user = User::where('id_facebook', '=', $fbid)->orWhere('login', '=', $fbid)->first();
      
      if (!is_null($user)) {
        // loga usuário existente
        Auth::loginUsingId($user->id);
        
        // pega avatar
        $request = new FacebookRequest(
          $session,
          'GET',
          '/me/picture',
          array (
            'redirect' => false,
            'height' => '200',
            'type' => 'normal',
            'width' => '200',
          )
        );
        $response = $request->execute();
        $pic = $response->getGraphObject();
        $image = Image::make($pic->getProperty('url'))->save(public_path().'/arquigrafia-avatars/'.$user->id.'.jpg');
        if ($user->photo == "") {
          $user->photo = '/arquigrafia-avatars/'.$user->id.'.jpg';
          $user->save();
        }

        $source_page = Request::header('referer');
        ActionUser::printLoginOrLogout($user->id, $source_page, "Login", "facebook", "user");
        
        return Redirect::to('/')->with('message', "Bem-vindo {$user->name}!");
        
      } else {
        // cria um novo usuário
        $user = new User;
        $user->name = $fbuser->getProperty('name');
        $user->login = $fbuser->getProperty('id');
        $user->email = $fbuser->getProperty('email');
        $user->password = 'facebook';
        $user->id_facebook = $fbuser->getProperty('id');
        $user->save();
        Auth::loginUsingId($user->id);
        
        // pega avatar
        $request = new FacebookRequest(
          $session,
          'GET',
          '/me/picture',
          array (
            'redirect' => false,
            'height' => '200',
            'type' => 'normal',
            'width' => '200',
          )
        );
        $response = $request->execute();
        $pic = $response->getGraphObject();
        $image = Image::make($pic->getProperty('url'))->save(public_path().'/arquigrafia-avatars/'.$user->id.'.jpg'); 
        $user->photo = '/arquigrafia-avatars/'.$user->id.'.jpg';
        $user->save();
        
        $source_page = Request::header('referer');
        ActionUser::printLoginOrLogout($user->id, $source_page, "login", "arquigrafia", "user");

        // return $user;
        return Redirect::to('/')->with('message', 'Sua conta foi criada com sucesso!');
      }
            
    }
    
    
	}

  public function follow($user_id)
  {
    $logged_user = Auth::user();
    
    if ($logged_user == null) //futuramente, adicionar filtro de login
      return Redirect::to('/');

    $following = $logged_user->following;

    
    if ($user_id != $logged_user->id && !$following->contains($user_id)) {
      $logged_user->following()->attach($user_id);

      $logged_user_id = Auth::user()->id;
      $pageSource = Request::header('referer'); //get url of the source page
      ActionUser::printFollowOrUnfollowLog($logged_user_id, $user_id, $pageSource, "passou a seguir", "user");
    }

    return Redirect::to(URL::previous()); // redirecionar para friends
  }

  public function unfollow($user_id)
  {
    $logged_user = Auth::user();
    
    if ($logged_user == null) //futuramente, adicionar filtro de login
      return Redirect::to('/');

    $following = $logged_user->following;

    
    if ($user_id != $logged_user->id && $following->contains($user_id)) {
      $logged_user->following()->detach($user_id);

      $logged_user_id = Auth::user()->id;
      $pageSource = Request::header('referer'); //get url of the source page
      ActionUser::printFollowOrUnfollowLog($logged_user_id, $user_id, $pageSource, "deixou de seguir", "user");
    }

    return Redirect::to(URL::previous()); // redirecionar para friends
  }
  
  // AVATAR
  public function profile($id)
  {
    $path = public_path().'/arquigrafia-avatars/'.$id.'_view.jpg';
    if( File::exists($path) ) {
      header("Cache-Control: public");
      header("Content-Disposition: inline; filename=\"".$id . '_view.jpg'."\"");
      header("Content-Type: image/jpg");
      header("Content-Transfer-Encoding: binary");
      readfile($path);
      exit;
    }
    return $path;
  }

/**
 * Show the form for editing the specified resource.
 *
 * @return Response
 */
  public function edit($id) {     
    $user = User::find($id);   
    return View::make('users.edit')
      ->with(
        ['user' => $user] );
  }

  public function update($id) {              
    $user = User::find($id);
    
    Input::flash();    
    $input = Input::only('name', 'login', 'email', 'scholarity', 'lastName', 'site', 'birthday', 'country', 'state', 'city', 
      'photo', 'gender', 'institution', 'occupation', 'visibleBirthday', 'visibleEmail','password','password_confirmation');    
    
    $rules = array(
        'name' => 'required',
        'login' => 'required',
        'email' => 'required|email',
        'password' => 'min:6|confirmed',
        'birthday' => 'date_format:"d/m/Y"'                  
    );     
    if ($input['email'] !== $user->email)        
      $rules['email'] = 'required|email|unique:users';

    if ($input['login'] !== $user->login)
      $rules['login'] = 'required|unique:users';

    $validator = Validator::make($input, $rules);   
    if ($validator->fails()) {
      $messages = $validator->messages();      
      return Redirect::to('/users/' . $id . '/edit')->withErrors($messages);
    } else {  
      $user->name = $input['name'];
      $user->login = $input['login'];
      $user->email = $input['email'];      
      $user->scholarity = $input['scholarity'];
      $user->lastName = $input['lastName'];
      $user->site = $input['site'];
      if ( !empty($input["birthday"]) )
        $user->birthday = $input["birthday"];      
      $user->country = $input['country'];
      $user->state = $input['state'];
      $user->city = $input['city'];  
      $user->gender = $input['gender'];  
      $user->visibleBirthday = $input['visibleBirthday'];  
      $user->visibleEmail = $input['visibleEmail'];   
      $user->password = Hash::make($input["password"]);

      $user->touch();
      $user->save();   

      if ($input["institution"] != null or $input["occupation"] != null) {
        $occupation = Occupation::firstOrCreate(['user_id'=>$user->id]);
        $occupation->institution = $input["institution"];
        $occupation->occupation = $input["occupation"];
        $occupation->save();
      }

      if (Input::hasFile('photo') and Input::file('photo')->isValid())  {    
        $file = Input::file('photo');
        $ext = $file->getClientOriginalExtension();
        $user->photo = "/arquigrafia-avatars/".$user->id.".jpg";
        $user->save();
        $image = Image::make(Input::file('photo'))->encode('jpg', 80);         
        $image->save(public_path().'/arquigrafia-avatars/'.$user->id.'.jpg');
        $file->move(public_path().'/arquigrafia-avatars', $user->id."_original.".strtolower($ext));         
      } 
      
      return Redirect::to("/users/{$user->id}")->with('message', '<strong>Edição de perfil do usuário</strong><br>Dados alterados com sucesso'); 
      
    }    
  }

  public function stoaLogin() {

    $account = Input::get('stoa_account');
    $password = Input::get('password');
    $stoa_user = $this->getStoaAccount($account, $password, 'login');
    if (!$stoa_user->ok) {
      $stoa_user = $this->getStoaAccount($account, $password, 'usp_id');
      if (!$stoa_user->ok) {
        return Response::json(false);
      }
    }
    $user = User::stoa($stoa_user);
    Auth::loginUsingId($user->id);
    //$user_id = Auth::user()->id;
    $source_page = Request::header('referer');
    ActionUser::printLoginOrLogout($user->id, $source_page, "Login", "stoa", "user");
    return Response::json(true);
  }

  public function institutionalLogin(){ 
    
    $login = Input::get('login');    
    $institution = Input::get('institution');
    $password = Input::get('password');

    Log::info("Retrieved params login=".$login.", institution=".$institution);

    $booleanExist = User::userBelongInstitution($login,$institution); 
    
    Log::info("Result belong institution -> booleanExist=".$booleanExist);

    //
    if ((Auth::attempt(array('login' => $login, 'password' => $password)) == true || 
      Auth::attempt(array('email' => $login, 'password' => $password,'active' => 'yes')) == true) &&
      $booleanExist == true){

      Log::info("Valid access, redirect");
      Session::put('institutionId', $institution);
      //loga usuario da institution
      return Redirect::to('/');
    }else{
      Log::info("Invalid access, return message");
      
      return Response::json(false);
    }



  }

  private function getStoaAccount($account, $password, $account_type) {
    $ch = curl_init();
    $this->setCurlOptions($ch, $account, $password, $account_type);
    $response = curl_exec($ch);
    curl_close ($ch);
    return json_decode($response);
  }

  private function setCurlOptions($ch, $account, $password, $account_type) {
    curl_setopt($ch, CURLOPT_URL,"https://social.stoa.usp.br/plugin/stoa/authenticate/");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
      http_build_query([
          $account_type => $account,
          'password' => $password,
          'fields' => 'full'
        ])
    );    
  }

}
