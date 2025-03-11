<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Carbon\Carbon;


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

    //add organizaion admin and assign as organizaiton admin role

    $this->post("/users",[
        'name' => "Hare Om",
        'contact_number' => "09951613400",
        "email" => "organizationone@gmail.com",
        "role"  => "5",
        "password" => "password",
        "dob" => Carbon::now(),
        "is_active" => 1,
        "email_verified_at" => Carbon::now()
    ]);

});

test('test organization admin create account and access dashboard and can access teacher  student and classrooms ', function () {

    $this->get('/login');

    $SuperAdminLogin = $this->post('/login',[
        "email" => "organizationone@gmail.com",
        "password" => "password"
    ]);

    $SuperAdminLogin->assertStatus(302);
    $SuperAdminLogin->assertRedirect('/home');

    $this->get('/classrooms');
    $this->get('/teachers');
    $this->get('/students');

    //logout
    $this->post('/logout');

    $this->assertFalse(Auth::check());


});


test("create students as organization admin",function(){

    $this->get('/login');

    $this->post('/login',[
        "email" => "organizationone@gmail.com",
        "password" => "password"
    ]);

    $this->post('/students',[
        "name" => "Zaw Zaw Win",
        "nickname" => "zawzawwin328",
        "student_code" => "12344434",
        "description" => "this is defualt",
        "dob" => now(),
        "grade" => "Grade 10",
    ]);

    $this->assertDatabaseHas('students',[
        'name' => "Zaw Zaw Win",
        "nickname" => "zawzawwin328"
    ]);

});


test("create teachers as organization admin",function(){

    $this->get('/login');

    $this->post('/login',[
        "email" => "organizationone@gmail.com",
        "password" => "password"
    ]);


    $this->post('/teachers',[
        "role" => 4, //teacher roles
        "name" => "Teacher One",
        "password" => "password",
        "contact_number" => "09951613400",
        "email" => "teacherone@gmail.com",
        "dob" => now(),
    ]);

    $this->assertDatabaseHas('users',[
        "name" => "Teacher One",
        "email" => "teacherone@gmail.com"
    ]);

});


test("create classrooms as organization admin",function(){

    $this->get('/login');

    $this->post('/login',[
        "email" => "organizationone@gmail.com",
        "password" => "password"
    ]);


    $this->post('/teachers',[
        "role" => 4, //teacher roles
        "name" => "Teacher One",
        "password" => "password",
        "contact_number" => "09951613400",
        "email" => "teacherone@gmail.com",
        "dob" => now(),
    ]);

    $this->post('/students',[
        "name" => "Zaw Zaw Win",
        "nickname" => "zawzawwin328",
        "student_code" => "12344434",
        "description" => "this is defualt",
        "dob" => now(),
        "grade" => "Grade 10",
    ]);

    $this->post('/classrooms',[
        'name' => "Class A",
        'start_date' => now(),
        'teacher_id' => 1,
        'description'=> "this is description",
        'students' => [1]
    ]);

    $this->assertDatabaseHas('classrooms',[
        "name" => "Class A",
    ]);

});



