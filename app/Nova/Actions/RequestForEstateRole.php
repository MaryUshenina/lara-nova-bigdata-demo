<?php

namespace App\Nova\Actions;

use App\Models\EstateRequest;
use App\Models\User;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\ActionFields;

class RequestForEstateRole extends DetachedAction
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $showOnIndexToolbar = false;

    public $confirmButtonText = 'agent.estate_access_request.confirm';

    public $cancelButtonText = 'agent.estate_access_request.cancel';

    public $confirmText = 'agent.estate_access_request.confirmText';


    /**
     * Get the displayable label of the button.
     *
     * @return string
     */
    public function label()
    {
        return __('agent.estate_access_request.label');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @return mixed
     */
    public function handle(ActionFields $fields)
    {
        if (!Auth::user()->can('runEstateRequest', User::class)) {
            return DetachedAction::danger(__('agent.estate_access_request.already_sent'));
        }

        EstateRequest::create(['user_id' => Auth::user()->id]);

        return DetachedAction::danger(__('agent.estate_access_request.sent'));
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
