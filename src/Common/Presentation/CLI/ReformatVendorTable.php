<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;

/**
 * Class LogoutAllUsers
 *
 * This command is used to log out all users by truncating the `personal_access_tokens` table.
 * It requires the person running the command to authenticate using their email and password,
 * ensuring that only authorized users can perform this operation. Once authenticated, the
 * command will delete all access tokens, effectively logging out all users.
 *
 * @author Harry(hareom284)
 * @date October 11, 2024
 */
class ReformatVendorTable extends Command
{

    protected $signature = 'reformat:vendor-table';


    protected $description = 'Log out all users by deleting their personal access tokens';


    public function handle()
    {
        $records = VendorEloquentModel::all();

        foreach ($records as $record) {

            $parts = explode('-', $record->contact_person);

            $record->name_prefix = $parts[0] ?? null;
            $record->contact_person = $parts[1] ?? null;
            $record->contact_person_last_name = $parts[2] ?? null;
            $record->save();
        }
    }
}
