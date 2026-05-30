<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

// Skenario 1: Tamu (belum login) harus ditolak saat mengakses halaman notes
test('tamu tidak bisa melihat dashboard notes dan akan diarahkan ke login', function () {
    get('/')
        ->assertRedirect(route('login'));
});

// Skenario 2: User yang sudah login bisa membuat note baru dengan sukses
test('user yang terautentikasi dapat membuat catatan baru', function () {
    // 1. Buat user tiruan di memori database tanpa factory
    $user = User::create([
        'name' => 'User Test',
        'email' => 'test_'.uniqid().'@example.com',
        'password' => bcrypt('password123'),
    ]);

    // 2. Kirim request POST ke endpoint store notes sebagai user tersebut
    $response = actingAs($user)
        ->post(route('notes.store'), [
            'title' => 'Catatan Fungsional',
            'content' => 'Ini adalah konten pengujian fungsional menggunakan Pest PHP.',
            'color' => 'blue',
        ]);

    // 3. Pastikan Laravel mengalihkan kembali ke halaman utama (success redirect)
    $response->assertRedirect(route('notes.index'));

    // 4. Pastikan data berhasil masuk ke database dengan data yang cocok
    assertDatabaseHas('notes', [
        'title' => 'Catatan Fungsional',
        'content' => 'Ini adalah konten pengujian fungsional menggunakan Pest PHP.',
        'color' => 'blue',
        'user_id' => $user->id,
    ]);
});
