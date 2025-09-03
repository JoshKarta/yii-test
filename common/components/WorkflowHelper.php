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
            // Get possible transitions for the current status
            $transitions = $model
                ->getWorkflowSource()
                ->getTransitions($model->getWorkflowStatus()->getId());

            foreach ($transitions as $transition) {
                $endStatus = $transition->getEndStatus();
                $result[] = [
                    'id' => $endStatus->getId(),
                    'label' => $endStatus->getLabel(),
                ];
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

                $model->sendToStatus($nextStatus['id']);
            } catch (\Throwable $th) {
                throw new \Exception('Error trying to update status');
            }
        } else {
            throw new \Exception('No possible transitions found');
        }
    }

    public static function setStatusToFinal($model)
    {
        $transitions = self::getStatusTransitions($model);

        try {
            $model->sendToStatus('final');
        } catch (\Throwable $th) {
            throw new \Exception('Error trying to update status');
        }
    }

    public static function canTransitionTo($model, $label)
    {
        $transitions = self::getStatusTransitions($model);

        dd($transitions);
        // foreach ($transitions as $transition) {
        //     if ($transition->getEndStatus()->getId() === $targetStatusId) {
        //         return true;
        //     }
        // }

        // return false;
    }
}
