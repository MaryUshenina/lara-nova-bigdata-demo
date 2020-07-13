<?php

namespace App\Nova\Actions;

use App\Models\EstateRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class DeclineOrRevokeEstateAccess extends Action
{
    use InteractsWithQueue, Queueable;

    public $showOnTableRow = true;

    public $confirmButtonText = 'admin.revoke_agent_access.confirm';

    public $cancelButtonText = 'admin.revoke_agent_access.cancel';

    public $confirmText = 'admin.revoke_agent_access.confirmText';

    /**
     * Get the displayable label of the button.
     *
     * @return string
     */
    public function label()
    {
        return __('admin.revoke_agent_access.label');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->user->role = User::PLAIN_USER_ROLE;
            $model->user->save();

            $model->status = EstateRequest::DECLINED_STATUS;
            $model->save();
        }
        return Action::message(__('admin.revoke_agent_access.complete'));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
