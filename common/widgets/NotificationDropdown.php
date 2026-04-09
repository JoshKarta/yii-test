<?php

namespace common\widgets;

use Yii;
use yii\base\Widget;
use common\models\NotificationUser;

class NotificationDropdown extends Widget
{
    public $limit = 5;

    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return '';
        }

        $userId = Yii::$app->user->id;

        $notifications = NotificationUser::find()
            ->where(['user_id' => $userId])
            ->with('notification')
            ->orderBy(['id' => SORT_DESC])
            ->limit($this->limit)
            ->all();

        $unreadCount = NotificationUser::find()
            ->where(['user_id' => $userId, 'is_read' => 0])
            ->count();

        return $this->render('notification-dropdown', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    protected function registerJs()
    {
        $js = <<<JS
        document.addEventListener('click', function(e) {
        if (e.target.classList.contains('mark-all-read')) {
            fetch('/notification/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': yii.getCsrfToken()
                }
            }).then(() => location.reload());
        }
        });
        JS;

        $this->getView()->registerJs($js);
    }
}
