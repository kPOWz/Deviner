<?php
class PrivateField {
	public static function get($id){
		
		$baseUri = $_SERVER['SERVER_NAME'];
		
		if($baseUri === "/gus.87c.us/"){
			return self::getLive($id) ;
		}
		else if($baseUri === "testgus.87c.us" || $baseUri === "gus1pt0.87c.us"){			
			return self::getTest($id);
		}
		
		return self::getLocal($id);
	}
	
	private static function getLocal($id){
		$config = array(
				'dbPass'=>'your local password here',
				'hashkey'=>'rockOnBUDDY!', //changing this will prevent any existing users from logging in
				'email'=>'justin@eightsevencentral.com',
				'connectionString' => 'mysql:host=localhost;dbname=gus',
				'username' => 'root'
		);
		return $config[$id];
	}
	private static function getTest($id){
		$config = array(
			'dbPass'=>'testgus', //enter database password here.
			'hashkey'=>'rockOnBUDDY!', //changing this will prevent any existing users from logging in
			'email'=>'karrie.zeman@gmail.com',
			'connectionString' => 'mysql:host=localhost;dbname=eight7cu_testgus;port=3306',
			'username' => 'eight7cu_testgus'
		);
		return $config[$id];
	}
	private static function getLive($id){
		$passwords = array(
				'dbPass'=>'salemsal', //enter database password here.
				'hashkey'=>'rockOnBUDDY!', //changing this will prevent any existing users from logging in
				'email'=>'justin@eightsevencentral.com',
				'connectionString' => 'mysql:host=localhost;dbname=eight7cu_databass;port=3306',
				'username' => 'eight7cu_lance'
		);
		return $config[$id];
	}
}