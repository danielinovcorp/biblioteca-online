<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('Um link de redefinição será enviado se a conta existir.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Esqueceu a password')" :description="__('Digite seu e-mail e receba um link para redefinir sua senha')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Endereço de email')"
            type="email"
            required
            autofocus
            placeholder="email@exemplo.com"
        />

        <flux:button variant="primary" type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white">{{ __('Email para resetar password') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-gray-300">
        <span>{{ __('Ou, volte para') }}</span>
        <flux:link :href="route('login')" class="text-emerald-600 hover:text-emerald-300" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
