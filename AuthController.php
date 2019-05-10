<?php

namespace app\controllers;
use Yii;
use app\models\Tasks;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\LoginForm;
use yii\filters\AccessControl;


use yii\web\Response;



use app\models\ContactForm;
use yii\web\User;

class AuthController extends Controller
{
	public function actionChecklogin(){

		$request = Yii::$app->request;

		$username = $request->post('username');
		$password = $request->post('password');

		if($username == '' || $password == ''){
			return -1;
		}
			

       	$data = (new \yii\db\Query())
		      ->select(['id'])
		      ->from('users')
		      ->where(['username' => $username, 'password' => $password])
		      ->all();
	
		if(count($data) == 1){
			// return $username;
			 $this->redirect(array('tasks/index', 'username'=>$username));
		}
        else{
			return 0;
		}	
	}

	// function find user in db 
	// input: user_msg
	// output: username, user_id

	public function actionFinduser(){
		//$request = Yii::$app->request;
		
		$query = "select users.username, users.id from users";

        $data = Yii::$app->db->createCommand($query)->queryAll();

		$array= array();
		
		foreach($data as $child)
		{
			array_push($array,$child["id"]);
		}

		foreach ($array as $value){ 
			//code to be executed; \
			var_dump($value);
		} 
		
		

		// return $array;
	


	}


	public function actionTest(){



		// $message = "@sa @ss @bb @sa #ghj @abc2 @abc3 @xuy!=12 @123abc aaaaaa @xyz";
		// $array_name = array();
		
		// while(preg_grep('/(@)/', explode(" ",$message))){
			
		// 	$start = strcspn($message,"@");
			
		// 	// /$end = strcspn($message," ");
		// 	$end = strcspn($message," ",$start,strlen($message));
		
		// 	$name = substr($message,$start+1,$end);

		// 	array_push($array_name,$name);

		// 	$end =  $end + 1;
		// 	$message = substr($message,$end);
			
		// }

		
		// // print_r($array_name);
		// $rows = (new \yii\db\Query())
		// 		->select(['id'])
		// 		->from('users')->distinct()
		// 		->where([
		// 			'username' => $array_name,
		// 		])
		// 		->all();

	    // foreach($rows as $c){
		// 	Yii::$app->db->createCommand('INSERT INTO `notifications` (`task_id`,`user_id_from`,`user_id_to`) VALUES (:task_id,:user_id_from,:user_id_to)', 
		// 	[
		// 		':task_id' => 10,
		// 		':user_id_from' => 6,
		// 		':user_id_to' => $c["id"],
		// 	])->execute();

		// }
		// print_r($rows);




	}


	public function actionComment(){
		$request = Yii::$app->request;
		$message = $request->post('msg');
		$task_id = $request->post('task_id');
		$user_id = $request->post('user_id');

		if($message != null && $task_id != null && $user_id != null){
			Yii::$app->db->createCommand('INSERT INTO `comments` (`content`,`task_id`,`user_id`) VALUES (:content,:task_id,:user_id)', [
				':content' => $message,
				':task_id' => $task_id,
				':user_id' => $user_id,
			])->execute();

			// insert into table [notifications] to push notification for user

			$user_id_from =  $request->post('user_id');


			/** get the id of user from message 
			 * input: message
			 * output: id of user which name
			 * 
			*/
				
			// find name of the users from input
			$array_name = array();
			while(preg_grep('/(@)/', explode(" ",$message))){
			
				$start = strcspn($message,"@");
				
				// need to improve 
				$end = strcspn($message," ",$start,strlen($message));
			
				$name = substr($message,$start+1,$end);
	
				array_push($array_name,$name);
	
				$end =  $end + 1;
				$message = substr($message,$end);
				
			}

			/**
			 * find the user by name
			 */
			$rows = (new \yii\db\Query())
			->select(['id'])
			->from('users')->distinct()
			->where([
				'username' => $array_name,
			])
			->all();

			// insert data into table notifications

			foreach($rows as $c){
				Yii::$app->db->createCommand('INSERT INTO `notifications` (`task_id`,`user_id_from`,`user_id_to`) VALUES (:task_id,:user_id_from,:user_id_to)', 
				[
					':task_id' => $task_id,
					':user_id_from' => $user_id_from,
					':user_id_to' => $c["id"],
				])->execute();

			}
			return 1;
		}
		else{
			return 0;
		}
	}


	


	
}