<?php

$company = Yii::$app->configuration->company
?>

<footer class="main-footer">
    Copyright &copy; 2014-<?= date('Y') ?> <strong> <?= $company->name ?? "Company Name" ?></strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.1.0
    </div>
</footer>