<?php

use edofre\fullcalendar\Fullcalendar;
?>

<div class="booking-calender">
    <?= Fullcalendar::widget([
        'clientOptions' => [
            'events' => \yii\helpers\Url::to(['booking/events']),
            'initialView' => 'dayGridMonth',
            'selectable' => true,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
        ],
    ]) ?>
</div>