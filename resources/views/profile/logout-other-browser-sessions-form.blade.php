<x-action-section>
    <x-slot name="title">
        {{ __('Sessões do Navegador') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Gerencie e saia de suas sessões ativas em outros navegadores e dispositivos.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Se necessário, você pode sair de todas as outras sessões do navegador em todos os seus dispositivos. Algumas das suas sessões recentes estão listadas abaixo; no entanto, esta lista pode não ser exaustiva. Se você acha que sua conta foi comprometida, atualize também sua senha.') }}
        </div>

        @if (count($this->sessions) > 0)
        <div class="mt-5 space-y-6">
            <!-- Other Browser Sessions -->
            @foreach ($this->sessions as $session)
            <div class="flex items-center">
                <div>
                    @if ($session->agent->isDesktop())
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                    </svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                    @endif
                </div>

                <div class="ms-3">
                    <div class="text-sm text-gray-600">
                        {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }}
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">
                            {{ $session->ip_address }},

                            @if ($session->is_current_device)
                            <span class="text-green-500 font-semibold">{{ __('This device') }}</span>
                            @else
                            {{ __('Última atividade') }} {{ $session->last_active }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('Log Out de outras sessões do navegador') }}
            </x-button>

            <x-action-message class="ms-3" on="loggedOut">
                {{ __('Feito.') }}
            </x-action-message>
        </div>

        <!-- Log Out Other Devices Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                {{ __('Log Out de outras sessões do navegador') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Digite sua senha para confirmar que você deseja sair das outras sessões do navegador em todos os seus dispositivos.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="logoutOtherBrowserSessions" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-button class="ms-3"
                    wire:click="logoutOtherBrowserSessions"
                    wire:loading.attr="disabled">
                {{ __('Log Out de outras sessões do navegador') }}
                </x-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>