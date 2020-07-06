<?php

namespace App\Nova\Actions;

use App\Models\EstateRequest;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\ActionFields;

class RequestForEstateRole extends DetachedAction
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $showOnIndexToolbar = false;

    public $confirmButtonText = 'Send';

    public $cancelButtonText = 'Cancel';

    public $confirmText = 'Are you sure you want to send request to become Estate user?';


    /**
     * Get the displayable label of the button.
     *
     * @return string
     */
    public function label()
    {
        return __('Send request for Estate role');
    }

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Fields\ActionFields $fields
     * @return mixed
     */
    public function handle(ActionFields $fields)
    {
        if (!Auth::user()->can('runEstateRequest', User::class)) {
            return DetachedAction::danger(__('Your request was already sent earlier, please wait for admin response'));
        }

        EstateRequest::create(['user_id' => Auth::user()->id]);

        return DetachedAction::message(__('Your request is sent to admin and will be reviewed in several days'));
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
