<?php


use Illuminate\Support\Facades\Artisan;
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


test("create user  with superadmin roles",function(){

    $this->assertTrue(Auth::check());

    $response = $this->post("/users",[
        'name' => "Hare Om",
        'contact_number' => "09951613400",
        "email" => "hareom284@gmail.com",
        "role"  => "2",
        "password" => "password",
        "dob" => Carbon::now(),
        "is_active" => 1,
        "email_verified_at" => Carbon::now()
    ]);


    $response->assertStatus(302);
    $response->assertRedirect("/users");
    $this->assertDatabaseHas('users',['name' => "Hare Om",]);
});



test("create user  with missing filed superadmin roles",function(){

    $this->assertTrue(Auth::check());

    $response = $this->post("/users",[
        'name' => "",
        'contact_number' => "",
        "email" => "hareom28",
        "role"  => "",
        "password" => "",
        "dob" => "",
        "is_active" => 1,
        "email_verified_at" => ""
    ]);

    //check backend validation
    $response->assertSessionHasErrors(['role']);
    $response->assertSessionHasErrors(['contact_number']);
    $response->assertSessionHasErrors(['email']);
    $response->assertSessionHasErrors(['name']);
    $response->assertSessionHasErrors(['password']);
});


test("other roles can't access  user module",function(){
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
    $response = $this->get("/users");

    $response->assertStatus(403);


    $otherUser = $this->post("/users",[
        'name' => "Hare Om",
        'contact_number' => "09951613400",
        "email" => "hareom284@gmail.com",
        "role"  => 2,
        "password" => "password",
        "dob" => Carbon::now(),
        "is_active" => 1,
        "email_verified_at" => Carbon::now()
    ]);

    $otherUser->assertStatus(403);
}

});



