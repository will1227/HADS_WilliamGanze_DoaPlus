<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

// Auditoria de segurança (semana 10): este teste do Breeze exigia que um visitante criasse conta
// na tabela users, que no DoaPlus é a do administrador. Agora garante o contrário.
test('visitante não consegue criar conta de administrador', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertMethodNotAllowed();
    $this->assertGuest();
    $this->assertDatabaseEmpty('users');
});
