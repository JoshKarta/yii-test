<?php

use edofre\fullcalendar\Fullcalendar;
use yii\bootstrap5\Modal;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bookings';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="booking-index">
    <?= Html::a('Create Booking', ['create'], ['class' => 'btn btn-success', 'role' => 'modal-remote', 'style' => 'display:contents;']) ?>

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
            'selectHelper' => true,
            'editable' => true,
            'select' => new \yii\web\JsExpression("
            function(start, end) {
                var date = moment(start).format('YYYY-MM-DD');
                // Store date in session storage for the create form
                sessionStorage.setItem('selectedDate', date);
                // Trigger the create modal
                $('a[href*=\"create\"][role=\"modal-remote\"]').click();
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
            // 'eventClick' => new \yii\web\JsExpression("
            //     function(event, jsEvent, view) {
            //         jsEvent.preventDefault();
            //         if (event.id) {
            //             $.ajax({
            //                 url: '" . \yii\helpers\Url::to(['booking/view']) . "?id=' + event.id,
            //                 type: 'GET',
            //                 success: function (data) {
            //                     $('#ajaxCrudModal').modal('show')
            //                         // .find('.modal-content')
            //                         .html(data);
            //                 },
            //                 error: function () {
            //                     alert('Failed to load booking details.');
            //                 }
            //             });
            //         }
            //     }
            // "),
            'eventResize' => new \yii\web\JsExpression("
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
                                alert('Error resizing time.');
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
<!-- <?php echo '<div class="modal-content"></div>'; ?> -->
<?php Modal::end(); ?>

<?php
$this->registerJs("
    $(document).on('click', '.fc-daygrid-day', function () {
        let date = $(this).data('date') || $(this).find('.fc-daygrid-day-number').attr('data-date');
        if (date) {
            sessionStorage.setItem('selectedDate', date);
            $('a[href*=\"create\"][role=\"modal-remote\"]').click();
        }
    });
    
    // Set the date when modal opens
    $(document).on('shown.bs.modal', '#ajaxCrudModal', function() {
        var selectedDate = sessionStorage.getItem('selectedDate');
        if (selectedDate) {
            $('#booking-date').val(selectedDate);
            sessionStorage.removeItem('selectedDate');
        }
    });
", \yii\web\View::POS_READY);
?>