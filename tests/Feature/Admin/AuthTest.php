<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin user for testing
    $this->adminUser = User::factory()->create([
        'email' => 'admin@test.com',
        'password' => Hash::make('password123'),
        'is_admin' => true,
    ]);

    // Create regular user for testing
    $this->regularUser = User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password123'),
        'is_admin' => false,
    ]);
});

describe('Admin Login', function () {
    it('allows admin to login with valid credentials', function () {
        $response = $this->postJson('/api/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'is_admin'],
                    'token',
                    'token_type',
                    'expires_at',
                ],
            ])
            ->assertJson([
                'message' => 'Login successful',
                'data' => [
                    'token_type' => 'Bearer',
                ],
            ]);
    });

    it('rejects login with invalid password', function () {
        $response = $this->postJson('/api/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('rejects login with non-existent email', function () {
        $response = $this->postJson('/api/admin/auth/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('rejects login for non-admin users', function () {
        $response = $this->postJson('/api/admin/auth/login', [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('validates required fields', function () {
        $response = $this->postJson('/api/admin/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    });

    it('validates email format', function () {
        $response = $this->postJson('/api/admin/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('updates last login info on successful login', function () {
        $this->postJson('/api/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $this->adminUser->refresh();
        
        expect($this->adminUser->last_login_at)->not->toBeNull();
        expect($this->adminUser->last_login_ip)->not->toBeNull();
    });
});

describe('Admin Logout', function () {
    it('allows authenticated admin to logout', function () {
        $token = $this->adminUser->createToken('admin-token', ['admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/admin/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        // Verify token is revoked
        expect($this->adminUser->tokens()->count())->toBe(0);
    });

    it('rejects logout without authentication', function () {
        $response = $this->postJson('/api/admin/auth/logout');

        $response->assertStatus(401);
    });
});

describe('Admin Me Endpoint', function () {
    it('returns current admin user info', function () {
        $token = $this->adminUser->createToken('admin-token', ['admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/admin/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'is_admin'],
                ],
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'id' => $this->adminUser->id,
                        'email' => $this->adminUser->email,
                        'is_admin' => true,
                    ],
                ],
            ]);
    });

    it('rejects unauthenticated requests', function () {
        $response = $this->getJson('/api/admin/auth/me');

        $response->assertStatus(401);
    });

    it('rejects non-admin users', function () {
        $token = $this->regularUser->createToken('user-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/admin/auth/me');

        $response->assertStatus(403);
    });
});

describe('Admin Token Refresh', function () {
    it('allows admin to refresh token', function () {
        $token = $this->adminUser->createToken('admin-token', ['admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/admin/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'expires_at',
                ],
            ]);

        // New token should be different
        $newToken = $response->json('data.token');
        expect($newToken)->not->toBe($token);
    });
});

describe('Admin Middleware Protection', function () {
    it('blocks non-admin from admin routes', function () {
        $token = $this->regularUser->createToken('user-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/admin/auth/me');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized.',
            ]);
    });
});
