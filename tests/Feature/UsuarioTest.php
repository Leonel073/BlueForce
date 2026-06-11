<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class UsuarioTest extends TestCase
{
 use RefreshDatabase;

   
    

    //rechazar contrasena debil
    public function test_rechaza_contrasena_debil()
    {
        $response = $this->post('/register', [
            'name' => 'Ludwin',
            'email' => 'ludwin@test.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        $response->assertSessionHasErrors('password');
    }

    //rechazar email invalido
    public function test_email_invalido()
    {
        $response = $this->post('/register', [
            'name' => 'Ludwin',
            'email' => 'ludwintest.com', //sim @
            'password' => '123456789',
            'password_confirmation' => '123456789'
        ]);

        $response->assertSessionHasErrors('email');
    }
    //registro valido
    public function test_registro_valido()
    {
        $response = $this->post('/register', [
            'name' => 'Ludwin',
            'email' => 'ludwin@test.com',
            'password' => 'Ludwin123.*',
            'password_confirmation' => 'Ludwin123.*'
        ]);

        $response->assertSessionHasNoErrors();
    }
    //verificacion de que el nombre es obligatorio
    public function test_nombre_es_obligatorio()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'ludwin@test.com',
            'password' => 'Ludwin123.*',
            'password_confirmation' => 'Ludwin123.*'
        ]);

        $response->assertSessionHasErrors('name');
    }

    //verificacion de que el email es obligatorio
    public function test_email_es_obligatorio(){
        $response = $this->post('/register', [
            'name' => 'Ludwin',
            'email' => '',
            'password' => 'Ludwin123.*',
            'password_confirmation' => 'Ludwin123.*'
        ]);

        $response->assertSessionHasErrors('email');
    }
     //contrase{a incorrecta
    public function test_confirmacion_password_incorrecta()
    {
        $response = $this->post('/register', [
            'name' => 'Ludwin',
            'email' => 'ludwin@test.com',
            'password' => 'Ludwin123.*',
            'password_confirmation' => 'otra123'
        ]);

        $response->assertSessionHasErrors('password');
    }

    // email duplicado
    public function test_no_permite_email_duplicado()
    {
        User::factory()->create([
            'email' => 'ludwin@test.com'
        ]);

        $response = $this->post('/register', [
            'name' => 'Ludwin',
            'email' => 'ludwin@test.com',
            'password' => 'Ludwin123.*',
            'password_confirmation' => 'Ludwin123.*'
        ]);

        $response->assertSessionHasErrors('email');
    }

    /*====================== */
        //logeo
    /*====================== */

   
    //logeo exitoso
    public function test_login_correcto()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Ludwin123.*')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Ludwin123.*'
        ]);

        $response->assertStatus(302);
    }
    //contraseña incorrecta
    public function test_login_falla_password_incorrecto()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Ludwin123.*')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'mala123'
        ]);

        $response->assertSessionHasErrors();
    }
    //login inexistente
    // email inexistente
    public function test_login_falla_email_inexistente()
    {
        $response = $this->post('/login', [
            'email' => 'noexiste@test.com',
            'password' => 'Ludwin123.*'
        ]);

        $response->assertSessionHasErrors();
    }
    public function test_usuario_no_autenticado_no_puede_ver_dashboard_admin()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
    //lo mismo pero para dashboard de usuario
     public function test_usuario_no_autenticado_no_puede_ver_dashboard_user()
    {
        $response = $this->get('/user/dashboard');

        $response->assertRedirect('/login');
    }
    /*====================== */
    /* ======cambios======== */

   public function test_no_cambia_password_si_password_actual_es_incorrecta()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Ludwin123.*')
        ]);

        $response = $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'incorrecta',
            'password' => 'Nueva123.*',
            'password_confirmation' => 'Nueva123.*'
        ]);

        $response->assertSessionHasErrors([
            'current_password'
        ]);
    }
    //cambio contraseña correcto 
    public function test_cambia_password_correctamente()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Ludwin123.*')
        ]);

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'Ludwin123.*',
            'password' => 'Nueva123.*',
            'password_confirmation' => 'Nueva123.*'
        ]);

        $response->assertSessionHasNoErrors();
    }
    //cambio de nombre 
    public function test_actualiza_nombre_correctamente()
    {
        $user = User::factory()->create([
            'name' => 'Ludwin'
        ]);

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Miguel',
            'email' => $user->email
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Miguel'
        ]);
    }
    //cambio de email
    public function test_actualiza_email_correctamente()
    {
        $user = User::factory()->create([
            'email' => 'viejo@test.com'
        ]);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => 'nuevo@test.com'
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'nuevo@test.com'
        ]);
    }
    //no actualiza email invalido
    public function test_no_actualiza_email_invalido()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => 'correoinvalido'
        ]);

        $response->assertSessionHasErrors('email');
    }
    //email duplicado 
    public function test_no_actualiza_email_duplicado()
    {
        User::factory()->create([
            'email' => 'existente@test.com'
        ]);

        $user = User::factory()->create([
            'email' => 'otro@test.com'
        ]);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => 'existente@test.com'
        ]);

        $response->assertSessionHasErrors('email');
    }
    //no poder ver dashboard
    public function test_no_puede_ver_dashboard_despues_de_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('/logout');

        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
}