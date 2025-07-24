<?php

use edofre\fullcalendar\Fullcalendar;
use edofre\fullcalendar\models\Event;
use yii\bootstrap5\Modal;
use yii\helpers\Url;

$this->title = 'Booking Calendar';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="booking-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $this->title ?></h1>
        <button class="w-auto btn btn-outline-primary" onClick="$('#bookingModal').modal('show')">New Booking</button>
    </div>

    <?= Fullcalendar::widget([
        'events' => $events,
        'clientOptions' => [
            'plugins' => ['dayGrid', 'timeGrid', 'interaction'],
            'initialView' => 'dayGridMonth',
            'dayMaxEventRows' => true,
            'eventDisplay' => 'block',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
        ],
    ]) ?>

</div>

<?php Modal::begin([
    "id" => "bookingModal",
    "footer" => "", // always need it for jquery plugin
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => false,
    ],
    "options" => [
        "tabindex" => false
    ]
]) ?>
<div id="bookingModalContent">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
<?php Modal::end(); ?>

<?php
$this->registerJs(<<<JS
function openBookingForm(dateStr) {
    $.get('create', { start_date: dateStr }, function(response) {
        $('#bookingModal').modal('show')
            .find('#bookingModalContent')
            .html(response.content);
        $('#bookingModal .modal-title').html(response.title);
        $('#bookingModal .modal-footer').html(response.footer);
    });
}

JS);
?>