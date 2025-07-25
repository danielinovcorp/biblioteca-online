<x-action-section>
    <x-slot name="title">
        {{ __('Autenticação de Dois Fatores') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Adicione segurança adicional à sua conta usando autenticação de Dois Fatores.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900">
            @if ($this->enabled)
            @if ($showingConfirmation)
            {{ __('Concluir ativação da autenticação de Dois Fatores.') }}
            @else
            {{ __('Você ativou a autenticação de Dois Fatores.') }}
            @endif
            @else
            {{ __('Você não ativou a autenticação de Dois Fatores.') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                {{ __('Quando a autenticação de dois fatores estiver ativada, você será solicitado a fornecer um token seguro e aleatório durante a autenticação. Você pode recuperar esse token no aplicativo Google Authenticator do seu telefone.') }}
            </p>
        </div>

        @if ($this->enabled)
        @if ($showingQrCode)
        <div class="mt-4 max-w-xl text-sm text-gray-600">
            <p class="font-semibold">
                @if ($showingConfirmation)
                {{ __('Para finalizar a ativação da autenticação de dois fatores, escaneie o seguinte código QR usando o aplicativo autenticador do seu telefone ou insira a chave de configuração e informe o código OTP gerado.') }}
                @else
                {{ __('A autenticação de dois fatores agora está ativada. Escaneie o seguinte código QR usando o aplicativo autenticador do seu telefone ou insira a chave de configuração.') }}
                @endif
            </p>
        </div>

        <div class="mt-4 p-2 inline-block bg-white">
            {!! $this->user->twoFactorQrCodeSvg() !!}
        </div>

        <div class="mt-4 max-w-xl text-sm text-gray-600">
            <p class="font-semibold">
                {{ __('Chave de configuração') }}: {{ decrypt($this->user->two_factor_secret) }}
            </p>
        </div>

        @if ($showingConfirmation)
        <div class="mt-4">
            <x-label for="code" value="{{ __('Code') }}" />

            <x-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                wire:model="code"
                wire:keydown.enter="confirmTwoFactorAuthentication" />

            <x-input-error for="code" class="mt-2" />
        </div>
        @endif
        @endif

        @if ($showingRecoveryCodes)
        <div class="mt-4 max-w-xl text-sm text-gray-600">
            <p class="font-semibold">
                {{ __('Armazene esses códigos de recuperação em um gerenciador de senhas seguro. Eles podem ser usados para recuperar o acesso à sua conta caso seu dispositivo de autenticação de dois fatores seja perdido.') }}
            </p>
        </div>

        <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
            @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
            <div>{{ $code }}</div>
            @endforeach
        </div>
        @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <x-button type="button" wire:loading.attr="disabled">
                    {{ __('Habilitar') }}
                </x-button>
            </x-confirms-password>
            @else
            @if ($showingRecoveryCodes)
            <x-confirms-password wire:then="regenerateRecoveryCodes">
                <x-secondary-button class="me-3">
                    {{ __('Regenerar códigos de recuperação') }}
                </x-secondary-button>
            </x-confirms-password>
            @elseif ($showingConfirmation)
            <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                <x-button type="button" class="me-3" wire:loading.attr="disabled">
                    {{ __('Confirmar') }}
                </x-button>
            </x-confirms-password>
            @else
            <x-confirms-password wire:then="showRecoveryCodes">
                <x-secondary-button class="me-3">
                    {{ __('Exibir códigos de recuperação') }}
                </x-secondary-button>
            </x-confirms-password>
            @endif

            @if ($showingConfirmation)
            <x-confirms-password wire:then="disableTwoFactorAuthentication">
                <x-secondary-button wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-secondary-button>
            </x-confirms-password>
            @else
            <x-confirms-password wire:then="disableTwoFactorAuthentication">
                <x-danger-button wire:loading.attr="disabled">
                    {{ __('Desabilitar') }}
                </x-danger-button>
            </x-confirms-password>
            @endif

            @endif
        </div>
    </x-slot>
</x-action-section>