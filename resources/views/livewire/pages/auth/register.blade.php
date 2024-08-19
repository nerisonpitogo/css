<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.guest');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$register = function () {
    $validated = $this->validate();

    $validated['password'] = Hash::make($validated['password']);

    event(new Registered(($user = User::create($validated))));

    Auth::login($user);

    $this->redirect(route('dashboard', absolute: false), navigate: true);
};

?>

<div>
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full px-4 lg:w-2/4">
            <x-mary-card title="Your stats" class="w-full bg-base-100" subtitle="Register an Account." shadow separator>
                <form wire:submit="register" class="w-full">
                    <!-- Name -->
                    <div>
                        {{-- <x-input-label for="name" :value="__('Name')" />
                        <x-text-input wire:model="name" id="name" class="block w-full mt-1" type="text"
                            name="name" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" /> --}}
                        <x-mary-input label="Name" wire:model="name" type="text" placeholder="Your name"
                            icon="o-user" hint="Your full name" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        {{-- <x-input-label for="email" :value="__('Email')" />
                        <x-text-input wire:model="email" id="email" class="block w-full mt-1" type="email"
                            name="email" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" /> --}}
                        <x-mary-input label="Email" wire:model="email" type="email" placeholder="Your email address"
                            icon="o-envelope" hint="You will use this during login." />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        {{-- <x-input-label for="password" :value="__('Password')" />

                        <x-text-input wire:model="password" id="password" class="block w-full mt-1" type="password"
                            name="password" required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" /> --}}
                        <x-mary-input label="Password" wire:model="password" type="password" placeholder="Your password"
                            icon="o-eye" hint="Your password." />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        {{-- <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                        <x-text-input wire:model="password_confirmation" id="password_confirmation"
                            class="block w-full mt-1" type="password" name="password_confirmation" required
                            autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /> --}}
                        <x-mary-input label="Confirm Password" wire:model="password_confirmation" type="password"
                            placeholder="Confirm Your password" icon="o-eye" hint="Confirm Your password." />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('login') }}" wire:navigate>
                            {{ __('Already registered?') }}
                        </a>

                        <x-primary-button class="ms-4">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-mary-card>

        </div>
    </div>


</div>
