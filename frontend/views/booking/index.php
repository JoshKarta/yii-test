<?php

use common\models\Booking;
use edofre\fullcalendar\Fullcalendar;
use yii\bootstrap5\Modal;
use yii2ajaxcrud\ajaxcrud\CrudAsset;

$this->title = 'Bookings';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$model = new Booking();
?>

<div class="booking-index">
    <?= Fullcalendar::widget([
        'clientOptions' => [
            'initialView' => 'month',
            'events' => \yii\helpers\Url::to(['booking/events']),
            'header' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'month,agendaWeek,agendaDay',
            ],
            'selectable' => true,
            'editable' => true,
            'select' => new \yii\web\JsExpression("
            function(start, end) {
                var date = moment(start).format('YYYY-MM-DD');
                $('#booking-date').val(date);
                $('#ajaxCrudModal').modal('show');
            }
        "),
            'eventDrop' => new \yii\web\JsExpression("
            function(event, delta, revertFunc) {
                $.ajax({
                    url: '" . \yii\helpers\Url::to(['booking/update-time']) . "',
                    type: 'POST',
                    data: {
                        id: event.id,
                        start: event.start.format(),
                        end: event.end ? event.end.format() : event.start.format()
                    },
                    success: function(response) {
                        if (!response.success) {
                            alert('Error updating time.');
                            revertFunc();
                        }
                    },
                    error: function() {
                        alert('Server error!');
                        revertFunc();
                    }
                });
            }
        "),
        ],
    ]) ?>

</div>

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",
    "size" => Modal::SIZE_LARGE,
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => false,
    ],
    "options" => [
        "tabindex" => false
    ]
]) ?>
<?php echo $this->render('_form', ['model' => $model]); ?>
<?php Modal::end(); ?>

<?php
$this->registerJs("
    $(document).on('click', '.fc-daygrid-day', function () {
        let date = $(this).data('date') || $(this).find('.fc-daygrid-day-number').attr('data-date');

        if (date) {
            $('#booking-date').val(date);
            $('#ajaxCrudModal').modal('show');
        }
    });
", \yii\web\View::POS_READY);
?>