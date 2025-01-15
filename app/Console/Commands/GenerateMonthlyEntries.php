<?php

namespace App\Console\Commands;

use App\Models\Entries;
use App\Models\Role;
use App\Models\SuccessIndicator;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateMonthlyEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entries:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate entries for success indicators every month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $indicators = SuccessIndicator::all();
        $matchingUserIds = [];

        // Get all success indicator IDs
        foreach ($indicators as $indicator) {
            $indicatorDivisionIds = json_decode($indicator->division_id, true);

            if (is_array($indicatorDivisionIds)) {

                $excludedRoles = Role::whereIn('name', ['SuperAdmin', 'Admin'])
                ->pluck('id');
                // Fetch all users
                $users = User::whereNotIn('role_id', $excludedRoles)->get();

                foreach ($users as $user) {
                    $userDivisionIds = json_decode($user->division_id, true);

                    if (is_array($userDivisionIds)) {
                        $commonDivisions = array_intersect($indicatorDivisionIds, $userDivisionIds);

                        if (!empty($commonDivisions)) {
                            $matchingUserIds[$user->id] = $user->id;
                        }
                    }
                }
            }
        }

        // Insert into entries table
        foreach ($matchingUserIds as $userId) {
            foreach ($indicators as $indicator) {
                Entries::create([
                    'indicator_id' => $indicator->id,
                    'user_id' => $userId,
                    'created_by' => 'system',
                ]);
            }
        }

        $this->info('Monthly entries have been successfully stored.');
    }
}

