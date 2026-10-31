<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => UserRole::Prodi,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            if ($user->role === UserRole::Perti) {
                if (!\App\Models\Perti::where('user_id', $user->id)->exists()) {
                    $user->pertiProfile()->create([
                        'kode_pt' => fake()->numerify('######'),
                        'alamat' => fake()->address(),
                    ]);
                }
            } elseif ($user->role === UserRole::Prodi) {
                if (!\App\Models\Prodi::where('user_id', $user->id)->exists()) {
                    $perti = \App\Models\Perti::first();
                    if (!$perti) {
                        $pertiUser = User::factory()->create([
                            'role' => UserRole::Perti,
                        ]);
                        $perti = \App\Models\Perti::where('user_id', $pertiUser->id)->first();
                    }
                    $user->prodiProfile()->create([
                        'perti_id' => $perti->id,
                        'kode_prodi' => fake()->numerify('#####'),
                    ]);
                }
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
