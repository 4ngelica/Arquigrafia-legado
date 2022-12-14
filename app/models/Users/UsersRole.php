<?php


class UsersRole extends Eloquent {

	protected $fillable = ['user_id','role_id'];
	public $timestamps = false;

	public static function valueUserRole($user_id){
		$userRolesArray = array();
		if($user_id != null){
			$userRoles = DB::table('roles')
			->join('users_roles','roles.id','=','users_roles.role_id')
			->select('roles.name')
			->where('users_roles.user_id', $user_id)->get();

			foreach ($userRoles as $valUserRoles) {
				 $userRolesArray[] = $valUserRoles;
			}
		}
		 return $userRolesArray;
	}


}
