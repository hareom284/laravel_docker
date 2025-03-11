<?php

use Illuminate\Support\Facades\Artisan;
use  Src\Company\User\Domain\Model\User;
use Illuminate\Support\Facades\Auth;
use Src\Company\User\Domain\Model\Permission;
use Carbon\Carbon;
use Src\Company\Security\Infrastructure\EloquentModels\PermissionEloquentModel;
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

/**
 *  superadmin can only create roles and assign roles
 *
 */
test('super admin can only create roles', function () {
    //auth check
    $this->assertTrue(Auth::check());

    $selectIds = PermissionEloquentModel::pluck('id');

    $response = $this->post("/roles", [
        "name" => "testing role",
        "description" => "testing",
        "selectedIds" => $selectIds
    ]);

    // Then the new role should be created successfully
    $response->assertStatus(302);
    $response->assertRedirect('/roles');
    $this->assertDatabaseHas('roles', ['name' => 'testing role']);
});


test('super admin with empty name', function () {

    $this->assertTrue(Auth::check());


    $selectIds = PermissionEloquentModel::pluck('id');

    $response = $this->post("/roles", [
        "name" => "",
        "description" => "testing",
        "selectedIds" => $selectIds
    ]);

    $response->assertSessionHasErrors(['name']);
});

test("create role without login", function () {
    Auth::logout();
    //without login
    $this->assertFalse(Auth::check());

    $selectIds = PermissionEloquentModel::pluck('id');

    $response = $this->post("/roles", [
        "name" => "",
        "description" => "testing",
        "selectedIds" => $selectIds
    ]);

    $response->assertRedirect('/login');
});


test("create role with other roles", function () {

    Auth::logout();


    $user = UserEloquentModel::create([
        "name" => "testing",
        "email" => "testinguser@gmail.com",
        "password" => "password",
        "email_verified_at" => Carbon::now()
    ]);

    $user->roles()->sync(2);

    if (Auth::attempt(["email" => "testinguser@gmail.com", "password" => "password",])) {
        $selectIds = PermissionEloquentModel::pluck('id');

        $rolesAccess = $this->get("/roles");

        $rolesAccess->assertStatus(403);

        $response = $this->post("/roles", [
            "name" => "testing roles",
            "description" => "testing",
            "selectedIds" => $selectIds
        ]);

        $response->assertStatus(403);
    }
});
