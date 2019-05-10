<?php

namespace app\controllers;

use Yii;
use app\models\Tasks;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\User;
use app\models\LoginForm;
use yii\filters\AccessControl;


use yii\web\Response;



use app\models\ContactForm;

/**
 * TasksController implements the CRUD actions for Tasks model.
 */
class TasksController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tasks models.
     * @return mixed
     */
    public function actionIndex($username)
    {

        $query = "SELECT tasks.*,users.id as idUser 
                    FROM `user_task` 
                    join users on users.id = user_task.user_id 
                    join tasks on tasks.id = user_task.task_id 
                    WHERE users.username = '$username' ";

        $data = Yii::$app->db
        ->createCommand($query)
        ->queryAll();

        $cmt = 'SELECT 
        comments.id as idComment, comments.content as contentComment,
        users.username as username, users.id as user_id,
        comments.task_id as task_id
        FROM `comments` 
        join users on users.id = comments.user_id';
        
        $data_cmt = Yii::$app->db
        ->createCommand($cmt)
        ->queryAll();
        
        $query = "
            SELECT users.username as username, users.id as user_id,
                    user_task.task_id as task_id
            FROM `user_task` 
            join users on users.id = user_task.user_id 
        ";
        $memberInTask = Yii::$app->db
        ->createCommand($query)
        ->queryAll();
    

        return $this->render('index', [
            'data' => $data,
            'data_cmt' => $data_cmt,
            'memberInTask' => $memberInTask,
        ]);
       
    }




    public function actionView($id)
    {
        // get task by ID
        $query = "select * from tasks where id = $id";
        $task = Yii::$app->db
        ->createCommand($query)
        ->queryOne();

        $cmt = 'SELECT 
        comments.id as idComment, comments.content as contentComment,
        users.username as username, users.id as user_id,
        comments.task_id as task_id
        FROM `comments` 
        join users on users.id = comments.user_id';
        
        $data_cmt = Yii::$app->db
        ->createCommand($cmt)
        ->queryAll();
        
        $query = "SELECT users.id as user_id, users.username 
                    FROM users ";

        $data = Yii::$app->db
        ->createCommand($query)
        ->queryAll();


        return $this->render('view', [
            'model' => $this->findModel($id),
            'task' => $task,
            'data_cmt' => $data_cmt,
            'data' => $data, 
        ]);
    }

    // get all tasks for admin to control 
    public function actionViewall(){
        
        $q_users = "select users.id as user_id, users.username as username
                    from users ";

        $data_users = Yii::$app->db->createCommand($q_users)->queryAll();
        
        $all_task = Tasks::find()
            ->orderBy('status')
            ->all();

        $user_in_task = "select 
            users.id as user_id, users.username as username,
            user_task.task_id as task_id
        FROM `user_task` 
        join users on user_task.user_id = users.id";

        $data_users_in_tasks = Yii::$app->db->createCommand($user_in_task)->queryAll();

        return $this->render('viewall', [
            'all_task' => $all_task,
            'data_users' => $data_users,
            'data_users_in_tasks' => $data_users_in_tasks,
        ]);
    }

    public function actionAddnewtask(){
        $request = Yii::$app->request;
        $name = $request->post("nameTask");
        $description = $request->post("description");

        $target_dir = "assets/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 9000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        $image = "/assets/" . $_FILES["fileToUpload"]["name"];



        $deadline = $request->post("date");

        $user_task = $request->post("user_task");

        // echo(sizeof($user_task));
        
        // add task
        Yii::$app->db->createCommand('INSERT INTO `tasks`(`name`, `status`, `image`, `description`, `deadline`) VALUES (:name,:status,:image,:description,:deadline)', [
            ':name' => $name,
            ':status' => 1,
            ':image' => $image,
            ':description' => $description,
            ':deadline' => $deadline,
        ])->execute();
        $id = Yii::$app->db->getLastInsertID();

        // add user)task

        foreach ($user_task as &$idUser) {
            Yii::$app->db->createCommand('INSERT INTO `user_task`(`task_id`, `user_id`, `status`) VALUES (:task_id,:user_id,:status)', [
                ':task_id' => $id,
                ':user_id' => $idUser,
                ':status' => 1,
                
            ])->execute();
        }
        
        return $this->redirect('viewall');
       

	}


    public function actionCreate()
    {
        $model = new Tasks();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDonetask(){
        $request = Yii::$app->request;
        $id = $request->post("idTaskDone");
        Yii::$app->db->createCommand()
             ->update('tasks', [                
                'status' => 0,
                ],
                 "id= $id"
                 )
             ->execute();

        return $this->redirect(['viewall']);

    }

    public function actionUpdate($id)
    {

        $request = Yii::$app->request;

        $name = $request->post("name");
        
        
        $target_dir = "assets/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        // if (file_exists($target_file)) {
        //     echo "Sorry, file already exists.";
        //     $uploadOk = 0;
        // }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 9000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        // if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        // && $imageFileType != "gif" ) {
        //     echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        //     $uploadOk = 0;
        // }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
             } 
            //else {
            //     echo "Sorry, there was an error uploading your file.";
            // }
        }
        if($_FILES["fileToUpload"]["name"]){
            $image = "/assets/" . $_FILES["fileToUpload"]["name"];
        }
        else{
            $image = $request->post("imgDefault");
        }
        
        
        //pathinfo($_FILES['fileToUpload']['name'], PATHINFO_FILENAME);
        

        // end image upload


        $description = $request->post("description");

        if($request->post("deadline") == null){
            $deadline = $request->post("dateDefault");
        }
        else{
            $deadline = $request->post("deadline");
        }
        
        $status = 1;
        if(in_array('0', $_POST['status'])){
            $status = 0;
        };



        Yii::$app->db->createCommand()
             ->update('tasks', [
                'name' => $name,
                'status' => $status,
                'image' => $image,
                'description' => $description,
                'deadline' => $deadline,
                ],
                 "id= $id"
                 )
             ->execute();

        // update table user_task

        $my_user_task = $request->post("my_user_task");

        Yii::$app->db->createCommand()
             ->delete('user_task', 
                 "task_id= $id"
                 )
             ->execute();

        foreach ($my_user_task as &$idUser) {
            Yii::$app->db->createCommand()
             ->insert('user_task', [
                 'task_id' => $id,
                'user_id' => $idUser,
                'status' => $status,
                ]
                 )
             ->execute();
        }

    
        return $this->redirect(['viewall']);


    }

    public function actionDelete($id)
    { 
        $model = Yii::$app->db->createCommand('DELETE FROM tasks WHERE  id=:id');
        $model->bindParam(':id', $id);
        $model->execute();

        return $this->redirect(['viewall']);
    }


    protected function findModel($id)
    {
        if (($model = Tasks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
