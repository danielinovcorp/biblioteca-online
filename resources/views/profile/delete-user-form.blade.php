<x-action-section>
    <x-slot name="title">
        {{ __('Excluir Conta') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Exclua sua conta permanentemente.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir sua conta, baixe todos os dados ou informações que deseja manter.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Excluir Conta') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('Excluir Conta') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Tem certeza de que deseja excluir sua conta? Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Digite sua senha para confirmar que deseja excluir sua conta permanentemente.') }}
                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Excluir Conta') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>