<?php

namespace Src\Company\Security\Domain\Factories;

use Src\Company\Security\Domain\Model\User;

/**************
 *  TODO
 *    currently try to implemenation on prgresss
 *
 **************/
class UserFactory
{
    public static function new(array $attributes = null): User
    {
        $attributes = $attributes ?: [];

        $defaults = [
            'id' => null,
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'organization_id' => null,
            'email_verified_at' => now(),
            'dob' => fake()->date(),
            'contact_number' => '',
            'storage_limit' => '',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'is_active' => 1,
            'stripe_id' => '',
            'pm_brand' => '',
            'pm_last_four' => '',
            'trial_end_at' => null,
        ];

        $attributes = array_replace($defaults, $attributes);

        return new User(
            id : $attributes['id'],
            name : $attributes['name'],
            email : $attributes['email'],
            organization_id : $attributes['organization_id'],
            email_verified_at : $attributes['email_verified_at'],
            dob  : $attributes['dob' ],
            contact_number : $attributes['contact_number'],
            storage_limit : $attributes['storage_limit'],
            password : $attributes['password'],
            is_active : $attributes['is_active'],
            stripe_id : $attributes['stripe_id'],
            pm_last_four : $attributes['pm_last_four'],
            trial_end_at : $defaults['trial_end_at']
        );
    }
}
