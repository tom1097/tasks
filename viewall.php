<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admin control tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?php echo ($this->title) ?></h1>

    <button type="button" class="btn btn-info btn-md pull-right" data-toggle="modal" data-target="#modalNewTask">Create Tasks</button>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th>ID Task</th>
                <th>Name Task</th>
                <th>Description</th>
                <th>Deadline</th>
                <th>Done?</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($all_task as $child) { ?>
            <tr>
                <td><?= $child["id"] ?></td>
                <td><?= $child["name"] ?></td>
                <td><?= $child["description"] ?></td>
                <td><?= $child["deadline"] ?></td>
                <td><?php
                        if($child["status"]  == 0){ 
                            echo "Done";
                        } 
                        else{
                            echo "Not yet";
                        }
                    
                    ?>
                    
                </td>
                <td> 
                    <img style="width: 80px; height: 80px;" src="<?= $child["image"] ?>"> 
                </td>
                <td><a data-toggle="modal" data-target="#modalEditTask<?= $child["id"] ?>"> Sửa</a></td>
                <td><a href="<?= yii\helpers\Url::to(['tasks/delete', 'id' =>$child["id"]]);  ?>"> Xóa </a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>



</div>



<!-- Modal create task-->
<div id="modalNewTask" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Create a new Task !</h4>
      </div>


      <div class="modal-body">
        <div class="row">
            <form action="<?= yii\helpers\Url::to(['tasks/addnewtask']);  ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's name: </label>
                    <input type="text" class="form-control" name="nameTask" id="nameTask" placeholder="Name of the task" />
                </div>


                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's description: </label>
                    <input type="text" class="form-control" id="descriptionTask" name="description" placeholder="Describe the task" />
                </div>
            

                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's deadline: </label>
                    <input type="date"  id="deadlineTask" name="date" >
                </div>
            

                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's image: </label>
                    <input type="file" class="" id="imageTask"  name="fileToUpload" />
                </div>
                
                
                <div class="padding10px col-md-12">
                    <label for="nameTask">For users: </label>
                    <?php foreach($data_users as $childUser) {?>
                        <br>
                        <input type="checkbox" name="user_task[]" class="user_task" value="<?=$childUser["user_id"] ?>"><?=$childUser["username"] ?>
                    
                    
                        <?php } ?>
                </div>
          
           
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id ="btn_create_task">Create</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
      </form>
    </div>

  </div>
</div>





<!-- Modal edit task-->
<?php foreach ($all_task as $child) { ?>
<div id="modalEditTask<?= $child["id"] ?>" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Task <?= $child["id"] ?> !</h4>
      </div>


      <div class="modal-body">
        <div class="row">
            <form 
            action="<?= yii\helpers\Url::to(['tasks/update', 'id' => $child["id"]]);  ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's name: </label>
                    <input type="text" class="form-control" name="name" id="nameTask" placeholder="Name of the task" value="<?=$child["name"] ?>"/>
                </div>

                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's description: </label>
                    <input type="text" class="form-control" id="descriptionTask" name="description" placeholder="Describe the task" value="<?=$child["description"] ?>"/>
                </div>
            

                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's deadline: </label>
                    <input type="date"  
                    id="deadlineTask" name="deadline" 
                    value="<?php

                    echo date('m-d-yy', strtotime($child["deadline"]));
                
                     ?>"    />
                    
                    <input type="hidden" name="dateDefault" value="<?=$child["deadline"] ?>">


                </div>
            

                <div class="padding10px col-md-12">
                    <label for="nameTask">Task's image: </label>
                    <input type="file" class="" id="imageTask"  name="fileToUpload" value="<?=$child["image"] ?>"/>
                    <input type="hidden" name="imgDefault" value="<?=$child["image"] ?>">
                </div>

                <div class="padding10px col-md-12">
                    <label for="Done">Finished Task: </label>
                    <input type="radio" name="status[]" value="0" 
                    <?php if($child["status"] == 0)
                        echo "checked";
                    ?>
                    >Done
                    <input type="radio" name="status[]" value="1"
                    <?php if($child["status"] == 1)
                        echo "checked";
                    ?>
                    >Not yet
                </div>

                <div class="padding10px col-md-12">
                    <label for="nameTask">For users: </label>



                    <!-- <?php foreach($data_users as $childUser) { ?> -->
                        <br>
                        <input type="checkbox" name="my_user_task[]" class="user_task" value="<?=$childUser["user_id"] ?>" 
                        <?php
                            foreach($data_users_in_tasks as $element){
                                if(($element["task_id"] == $child["id"]) && ($element["user_id"] == $childUser["user_id"])){
                                    echo "checked";
                                }
                            }

                        ?>
                        /><?=$childUser["username"] ?>
                    
                    
                         <!-- <?php } ?> end data_users  -->
                </div>
          
           
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id ="btn_create_task">Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
      </form>
    </div>

  </div>
</div>

<?php } ?>