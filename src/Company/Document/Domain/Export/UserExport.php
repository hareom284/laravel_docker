<?php

namespace Src\Company\Document\Domain\Export;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Src\Company\System\Application\Repositories\Eloquent\UserRepository; // Adjust the namespace if needed

class UserExport implements FromCollection
{
    private $userRepository;

    // Dependency injection of UserRepository
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function collection()
    {
        // Fetch all users using the adapted method
        $users = $this->userRepository->getAllUsers();

        // Transform users to a format suitable for export
        $collection = new Collection();
        foreach ($users as $user) {
            $collection->push([
                'ID' => $user->id,
                'First Name' => $user->first_name,
                'Last Name' => $user->last_name,
                'Email' => $user->email,
                // Add other fields as needed
            ]);
        }

        return $collection;
    }
}
