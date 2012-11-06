<?php
class PrivateField {
	public static function get($id){
		$passwords = array(
			'db'=>'salemsal', //enter database password here.
			'hashkey'=>'rockOnBUDDY!', //changing this will prevent any existing users from logging in
			'email'=>'justin@eightsevencentral.com',
		);
		return $passwords[$id];
	}
}