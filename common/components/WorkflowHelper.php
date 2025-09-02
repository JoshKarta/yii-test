<?php

namespace common\components;

use yii\base\Component;

use function PHPUnit\Framework\throwException;

/**
 * WorkflowHelper
 *
 * A helper component for managing workflow transitions without
 * repeating boilerplate code in models/controllers.
 */
class WorkflowHelper extends Component
{

    public static function getStatusTransitions($model)
    {
        $result = [];
        if ($model->hasWorkflowStatus()) {
            // Get possible transitions for the WorkflowSource
            $transitions = $model
                ->getWorkflowSource()
                ->getTransitions($model->getWorkflowStatus()->getId());

            foreach ($transitions as $transition) {
                $result[] = $transition->getEndStatus()->getId();
            }
        }
        return $result;
    }

    public static function goToNextStatus($model)
    {
        $transitions = self::getStatusTransitions($model);

        if (!empty($transitions)) {
            try {
                $nextStatus = reset($transitions);

                $model->sendToStatus($nextStatus);
            } catch (\Throwable $th) {
                throw new \Exception('Error trying to update status');
            }
        } else {
            throw new \Exception('No possible transitions found');
        }
    }
}
