<?php

$this->title = "Profile";

$user = Yii::$app->user->identity;
?>

<div class="row">
    <div class="col-md-4">
        <div class="card p-0 rounded-3">
            <div class="card-header pt-3 pb-5 bg-primary-subtle">
                <h4> Hello, <?= $user->username ?></h4>
            </div>
            <div class="card-body">
                <img src="https://images.pexels.com/photos/7046685/pexels-photo-7046685.jpeg" alt="" class="rounded-circle object-fit-cover" width="100" height="100">
                <p><?= $user->email ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="card-header border-bottom">
                    <h3>
                        Informatie
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>