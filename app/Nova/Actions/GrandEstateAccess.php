<?php

namespace App\Nova\Actions;


use App\Models\EstateRequest;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class GrandEstateAccess extends Action
{
    use InteractsWithQueue, Queueable;

    public $showOnTableRow = true;

    public $confirmButtonText = 'Yes';

    public $cancelButtonText = 'No';

    public $confirmText = 'Are you sure you want to grand Estate Access level for this user?';

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Fields\ActionFields $fields
     * @param \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->user->role = User::ESTATE_USER_ROLE;
            $model->user->save();

            $model->status = EstateRequest::CONFIRMED_STATUS;
            $model->save();
        }

        return Action::message(__('All done'));
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