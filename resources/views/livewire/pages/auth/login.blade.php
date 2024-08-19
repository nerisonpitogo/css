<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('components.layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="flex items-center justify-center">
        <div class="w-full px-4 ">
            <x-mary-card title="User Login" class="w-full bg-base-100" subtitle="" shadow separator>
                <form wire:submit="login" class="w-full">
                    <!-- Email Address -->
                    <div>
                        {{-- <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="form.email" id="email" class="block w-full mt-1" type="email" name="email"
                        required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" /> --}}
                        <x-mary-input label="Email" wire:model="form.email" id="email" type="email" name="email"
                            placeholder="Your email" icon="o-envelope" hint="Your email address" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        {{-- <x-input-label for="password" :value="__('Password')" />
                    <x-text-input wire:model="form.password" id="password" class="block w-full mt-1" type="password"
                        name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" /> --}}
                        <x-mary-input label="Password" id="password" name="password" wire:model="form.password"
                            icon="o-eye" type="password" placeholder="Your password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember" class="inline-flex items-center">
                            <input wire:model="form.remember" id="remember" type="checkbox"
                                class="text-indigo-600 border-gray-300 rounded shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                name="remember">
                            <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="grid w-full grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="flex items-center justify-start mt-2">
                            <!-- Assuming you have a route for registration -->
                            {{-- @if (Route::has('register'))
                                <a class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none"
                                    href="{{ route('register') }}">
                                    {{ __('Register') }}
                                </a>
                            @endif --}}
                        </div>
                        <div class="flex items-center justify-center mt-2">
                            {{-- @if (Route::has('password.request'))
                                <a class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    href="{{ route('password.request') }}" wire:navigate>
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif --}}
                            <x-primary-button class="ml-3">
                                {{ __('Log in') }}
                            </x-primary-button>

                        </div>
                    </div>
                </form>
            </x-mary-card>

        </div>
    </div>
</div>
