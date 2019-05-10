<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tasks';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="container">
    <div class="">
        <div class="row">

          <?php if($data){ ?>
            <?php foreach($data as $child){ ?>
              

              <div class="col-xs-12 col-md-3" data-toggle="modal" data-target="#modalTask<?=$child["id"] ?>"> 
                  <a href="#" class="thumbnail">
                      <img src="<?=$child["image"] ?>" style="width: 100px;

height: 100px;"  alt="...">
                      <h4> 
                        <?php if($child["status"] == 0){
                              echo "<strike>";
                        } ?>
                         <?= $child["name"] ?> 
                         <?php if($child["status"] == 0){
                              echo "</strike>";
                        } ?>
                      </h4>
                      <p><?= $child["description"] ?></p>

                  </a>
              </div> 

              <!-- Modal show detail of Task -->

              <div id="modalTask<?=$child["id"] ?>" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h2 class="modal-title">Task <?=$child["id"] ?>: <?=$child["name"] ?></h2>
                    </div>
                    <div class="modal-body">
                      <div class="membersInTask">
                          <p>Members In this Task:</p>
                          <?php foreach($memberInTask as $cMIT){ 
                                  if($cMIT["task_id"] == $child["id"]){  
                          ?>
                            <a class="padding-10px" href="
                            <?= yii\helpers\Url::to(['user/view', 'id' =>$cMIT["user_id"]]);  ?>">
                            <span class="MIT"><i class="fas fa-user-tag"></i>  <?= $cMIT["username"] ?> </span></a>

                          <?php 
                            }
                          } 
                        ?>
                      </div>
                      <div class="contentTask">
                        <p>Description od this task: <br>
                        <?=$child["description"] ?>
                        </p>
                      </div>


                      <div class="showComment">
                        <strong>Comments:</strong>
                          <?php foreach($data_cmt as $childComment){ ?>
                            <?php if($childComment["task_id"] == $child["id"]) {?>
                              
                              <p><img src="/assets/img_avatar.png" alt="Avatar" class="avatar">  
                                  
                                <?= $childComment["username"] ." : &#160;&#160;&#160;". $childComment["contentComment"] ?></p>
                          <?php } ?>
                          <?php } ?>
                          <div class="">
                            <p class="demo"></p> 
                          </div>
                      </div>



                      <div class="commentTask">
                              <div class="row">
                                <div class="col-md-10">
                                  <input type="text" placeholder="Input your comment here" id="user_comment<?=$child["id"] ?>" class="user_comment  form-control" name="user_comment<?=$child["id"] ?>"  />
                                </div>

                                <div class="custom_button_task col-md-2">
                                  <button data-id-task="<?=$child["id"] ?>" type="button" 
                                class="btn btn-md btn-primary btn_comment_task" 
                                data-id-user="<?=$child["idUser"] ?>" >Comment</button>
                                </div>
                              </div>
                      </div>
                      
                    </div>
                    <div class="modal-footer">
                      <h3>Deadline on: <?= date("m-d-Y", strtotime($child["deadline"]));  ?></h3>
                      <form action="<?= yii\helpers\Url::to(['tasks/donetask']);  ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                        <input type="hidden" name="idTaskDone" value="<?=$child["id"] ?>">
                        <button type="submit" class="btn btn-primary">Done this Task</button>
                      </form>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>

                </div>
              </div>
              <!-- end detail of Task -->

            <?php } ?>

            <?php 
            } 
                else
                {
                  echo "No task !";
                }
            ?>
        </div>
    </div>
</div>
