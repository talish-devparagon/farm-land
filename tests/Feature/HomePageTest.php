<?php

use App\Models\User;

test('guests see login and get started links on the landing page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee(route('login'), escape: false);
    $response->assertSee(route('register'), escape: false);
    $response->assertDontSee(route('dashboard'), escape: false);
});

test('authenticated users see a dashboard link on the landing page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSee(route('dashboard'), escape: false);
    $response->assertDontSee(route('login'), escape: false);
});
