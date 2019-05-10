<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tasks */

$this->title = $model->name;

$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tasks-view">

    <h1>Name's task: <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div style="border-bottom: solid 1px #ccc;" class="col-md-12">
            <label for="">Descripton's task: </label>
            <h4><?= $task["description"] ?></h4>

            <label for="">Deadline's task: </label>
            <h4><?= $task["deadline"] ?></h4>
            <label for="">Status's task: </label>
            <h4><?= $task["status"] = 0 ? 'Finshed' : 'Not finish yet' ?></h4>

        </div>


        <div style="padding: 10px;" class="col-md-12">
            <strong>Comments area:</strong>
            <!-- handle show comments -->
            <?php foreach ($data_cmt as $childComment) { ?>
                <?php if ($childComment["task_id"] == $task["id"]) { ?>
                    <p>
                        <img src="/assets/img_avatar.png" alt="Avatar" class="avatar">
                        <?= $childComment["username"] . " : &#160;&#160;" . $childComment["contentComment"] ?>
                    </p>
                    
                <?php } ?>
            <?php } ?>
            <p class="showCommentUser"></p> 
        </div>
        <div class="col-md-12">

            <div class="commentTask">
                <div class="row">
                
                    <div class="col-md-10">
                        <select id="selectUser" style="display: none">
                        <?php foreach($data as $cU) {?>
                            <option value="<?=$cU["user_id"] ?>" ><?=$cU["username"] ?> </option>
                        <?php }?>
                        </select>  

                        <input style="" type="text" 
                        placeholder="Input your comment here" 
                        id="user_comment<?= $task["id"] ?>" class="user_comment  form-control" 
                        name="user_comment<?= $task["id"] ?>" 
                           
                        />
                    </div>

                    <div class="custom_button_task col-md-2">
                        <button data-id-task="<?= $task["id"] ?>" type="button" class="btn btn-md btn-primary btn_comment_task" data-id-user="6">Comment</button>
                    </div>

                    
                </div>
            </div>
        </div>

    </div>



</div>