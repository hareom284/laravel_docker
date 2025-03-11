<?php

use Illuminate\Support\Facades\Artisan;
use Src\Company\User\Domain\Model\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;

beforeEach(function () {
    // Run migrations
    Artisan::call('migrate:fresh');
    // Seed the database with test data
    Artisan::call('db:seed');

    //login as superadmin
    $this->post('/login', [
        'email' => 'superadmin@mail.com',
        'password' => 'password',
    ]);
});

/***
 *
 *
 *  superadmin only create superadmin
 *
 *  step first login as superadmin then
 *  check auth login as superadmin or not
 *  then create permissions
 */

test("superadmin only create permission", function () {


    //auth check
    $this->assertTrue(Auth::check());


    $response = $this->post('/permissions', [
        'name' => 'new permission',
        'description' => "new description"
    ]);

    // Then the new role should be created successfully
    $response->assertStatus(302);
    $response->assertRedirect('/permissions');
    $this->assertDatabaseHas('permissions', ['name' => 'new permission']);
});

/***
 * superadmin but mission permission checking validation
 *
 */
test("create permission mission permission name", function () {


    //auth check
    $this->assertTrue(Auth::check());

    $response = $this->post('/permissions', [
        'name' => '',
        'description' => "new description"
    ]);


    $response->assertSessionHasErrors(['name']);
});


/**
 * create permission without login
 */

test("create permission without login", function () {

    Auth::logout();

    $response = $this->post('/permissions', [
        'name' => 'new permission',
        'description' => "new description"
    ]);

    // access denied not superadmin role
    $response->assertRedirect('/login');
});




test("create permission without not superadmin roles", function () {

    Auth::logout();

    $user = UserEloquentModel::create([
        "name" => "testing",
        "email" => "testinguser@gmail.com",
        "password" => "password",
        "email_verified_at" => Carbon::now()
    ]);

    $user->roles()->sync(2);

    if (Auth::attempt(["email" => "testinguser@gmail.com","password" => "password",]))
    {
        $response = $this->post('/permissions', [
            'name' => 'new permission',
            'description' => "new description"
        ]);

        $response->assertStatus(403);
    }


});
