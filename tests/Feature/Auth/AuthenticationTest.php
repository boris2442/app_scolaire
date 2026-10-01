<?php

// use App\Models\User;

// test('login screen can be rendered', function () {
//     $response = $this->get('/login');

//     $response->assertStatus(200);
// });

// test('users can authenticate using the login screen', function () {
//     $user = User::factory()->create();

//     $response = $this->post('/login', [
//         'email' => $user->email,
//         'password' => 'password',
//     ]);

//     $this->assertAuthenticated();
//     $response->assertRedirect(route('after.login.page', absolute: false));
// });

// test('users can not authenticate with invalid password', function () {
//     $user = User::factory()->create();

//     $this->post('/login', [
//         'email' => $user->email,
//         'password' => 'wrong-password',
//     ]);

//     $this->assertGuest();
// });

// test('users can logout', function () {
//     $user = User::factory()->create();

//     $response = $this->actingAs($user)->post('/logout');

//     $this->assertGuest();
//     $response->assertRedirect('/');
// });



use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    // 1. On s'assure du mot de passe hashé
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    // 2. Envoi de la requête de connexion
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Si la connexion échoue encore, décommentez la ligne ci-dessous pour voir le message d'erreur/redirection :
    // $response->assertSessionHasNoErrors();

    $this->assertAuthenticated();
    $response->assertRedirect(route('after.login.page', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
