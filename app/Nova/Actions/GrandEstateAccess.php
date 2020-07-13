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

    public $confirmButtonText = 'admin.estate_access_request.confirm';

    public $cancelButtonText = 'admin.estate_access_request.cancel';

    public $confirmText = 'admin.estate_access_request.confirmText';


    /**
     * Get the displayable label of the button.
     *
     * @return string
     */
    public function label()
    {
        return __('admin.estate_access_request.label');
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
            $model->user->role = User::ESTATE_USER_ROLE;
            $model->user->save();

            $model->status = EstateRequest::CONFIRMED_STATUS;
            $model->save();
        }

        return Action::message(__('admin.estate_access_request.complete'));
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
