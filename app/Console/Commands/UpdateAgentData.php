<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateAgentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:data:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agent data update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereIn('role', User::ROLES_WITH_ADS)->get();
        $this->output->progressStart($users->count());
        foreach ($users as $u) {
            $u->updateAgentData();
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
