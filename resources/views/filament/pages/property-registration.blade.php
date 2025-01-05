<x-filament-panels::page>
    <x-filament::card>
        @if(auth()->user()->isActive == "inactive")
            <div class="text-center">
                <h2 class="text-xl font-bold">Rental Business Registration Fee</h2>
                <p class="mt-2">Please pay ₱300 to activate your Rental Business registration.</p>
                <p class="mt-2 text-sm text-gray-600">Your account will be activated immediately after payment.</p>
                @if(auth()->user()->payment_status == 'pending')
                    <p class="mt-2 text-amber-600">Payment is being processed...</p>
                @endif
            </div>
        @else
            <div class="text-center text-green-600">
                <h2 class="text-xl font-bold">Registration Complete</h2>
                <p class="mt-2">Your account is active.</p>
            </div>
        @endif
    </x-filament::card>
</x-filament-panels::page>