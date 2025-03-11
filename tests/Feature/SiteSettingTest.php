<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Src\Company\User\Domain\Model\User;
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
test('only superadmin can access sitesetting and add settings', function () {

    $this->assertTrue(Auth::check());
    $response = $this->get("/settings");
    $response->assertStatus(200);

    $postData = $this->post("/settings",[
        'site_name' => "bclms",
        'timezone' => "UTC",
        'ssl'    => "test",
        'locale' => "mm",
        "email" => "hareom284@gmail.com",
        "contact_number" => "09951613400"
    ]);

    $postData->assertStatus(200);
    $this->assertDatabaseHas("site_settings",[
    'site_name' => "bclms",
    'timezone' => "UTC",]);

});


test("without login access site setting and other role",function(){
    Auth::logout();

    $response = $this->get("/settings");
    $response->assertRedirect("/login");

    $user = UserEloquentModel::create([
        "name" => "testing",
        "email" => "testinguser@gmail.com",
        "password" => "password",
        "email_verified_at" => Carbon::now()
    ]);

    $user->roles()->sync(2);

    if (Auth::attempt(["email" => "testinguser@gmail.com", "password" => "password",])) {
        $checkOtherRoles = $this->get("/settings");
        $checkOtherRoles->assertStatus(403);
    }
});




