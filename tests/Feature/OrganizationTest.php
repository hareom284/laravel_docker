<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
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


test('superadmin create organization', function () {

    $this->assertTrue(Auth::check());

    $reponse = $this->get("/organizations");
    $reponse->assertStatus(200);
});



test('without login not access organization', function () {

    Auth::logout();

    $reponse = $this->get("/organizations");
    $reponse->assertRedirect('/login');
});

/**
 *
 *
 *
 */

test('without other role not access organization  ', function () {

    Auth::logout();
    $user = UserEloquentModel::create([
        "name" => "testing",
        "email" => "testinguser@gmail.com",
        "password" => "password",
        "email_verified_at" => Carbon::now()
    ]);
    $user->roles()->sync(3);

    if (Auth::attempt(["email" => "testinguser@gmail.com", "password" => "password"])) {
        $reponse = $this->get("/organizations");
        $reponse->assertStatus(403);
    }
    $reponse = $this->get("/organizations");
    $reponse->assertStatus(403);
});

test("form submit as organization with superadmin role", function () {

    $this->assertTrue(Auth::check());

    $response = $this->get("/organizations");
    $response->assertStatus(200);

    $postData = $this->post("/organizations", [
        "name"  => "oranization name",
        "contact_person" => "Zaw Zaw Win",
        "contact_email" => "zawzawwin@gmail.com",
        "contact_number" => "09951613400",
        "price" => 100,
        "payment_period" => 10,
        "allocated_storage" => "100GB",
        "teacher_license" => "mm"
    ]);


    $postData->assertStatus(302);

    $this->assertDatabaseHas(
        "organizations",
        [
            "name"  => "oranization name",
            "contact_person" => "Zaw Zaw Win",
            "contact_email" => "zawzawwin@gmail.com",
            "contact_number" => "09951613400"
        ]
    );

    $this->assertDatabaseHas(
        "plans",
        [
            "name" => "oranization name",
            "price" => "100.00",
            "payment_period" => "10",
            "allocated_storage" => "100GB",
            "teacher_license" => "mm"
        ]
    );


    $postData = $this->post("/organizations", []);
    $postData->assertSessionHasErrors(['name', 'contact_email']);
});
