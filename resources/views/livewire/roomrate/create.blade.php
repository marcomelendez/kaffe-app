<div>
    <form wire:submit="save">
        <input type="text" wire:model="propertyId" hidden>
        @if(!$rangeValid)
            <div class="relative w-full overflow-hidden rounded-sm border border-red-500 bg-white text-neutral-600 dark:bg-neutral-950 dark:text-neutral-300"
                role="alert">
                <div class="flex w-full items-center gap-2 bg-red-500/10 p-4">
                    <div class="bg-red-500/15 text-red-500 rounded-full p-1" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-2">
                        <h3 class="text-sm font-semibold text-red-500">Invalid Email Address</h3>
                        <p class="text-xs font-medium sm:text-sm">The email address you entered is invalid. Please try
                            again.</p>
                    </div>
                    <button class="ml-auto" aria-label="dismiss alert">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor"
                            fill="none" stroke-width="2.5" class="size-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        <div class="flex flex-wrap gap-3 mb-4" wire:ignore>
            <div style="width: 30%">
                <flux:label>Seleccione un rango de fecha</flux:label>
                <flux:input type="text" id="dateRange" placeholder="dd/mm/YYYY  to  dd/mm/YYYY" disable />
                @if ($errors->has('dateRange'))
                    <p class="text-red-600 text-sm mt-1">{{ $errors->first('dateRange') }}</p>
                @endif
            </div>
        </div>

        <div>
            @foreach ($boards as $board)
                <h2>REGIMEN: {{ $board->translate('es')->name }}</h2>
                <hr>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 mt-3">
                    @foreach ($rooms as $room)
                        <div class="col-span-2">
                            {{ $room['name'] }}
                            <p>Descripcion</p>
                        </div>

                        @php $i=1; @endphp
                        <div class="flex flex-col gap-2" class="mb-4">
                            @foreach ($room['unit_adults'] as $unitAdult)
                                @if ($unitAdult->bookable->plan_id == $board->id)
                                    <flux:field>
                                        <flux:input wire:model="unitAdultIds.{{ $unitAdult->id }}" type="number"
                                            placeholder="{{ \App\Admin\Properties\Constants::TYPE_OCCUPANCY[$i] }}" />
                                    </flux:field>
                                    @php $i++; @endphp
                                @endif
                            @endforeach
                            @foreach ($room['unit_children'] as $unitChild)
                                @if ($unitChild->bookable->plan_id == $board->id)
                                    <flux:field>
                                        <flux:input wire:model="unitChildIds.{{ $unitChild->id }}"
                                            placeholder="Child (< {{ $unitChild->bookable->age }} yr) " />
                                    </flux:field>
                                    @php $i++; @endphp
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="flex justify-end mt-4">
            <flux:button type="submit" variant="primary">Guardar</flux:button>
        </div>
    </form>
</div>
@push('scripts')
    <script>
        document.addEventListener('init-daterange', function() {
            window.initFlatpickr("#dateRange", function(range) {
                @this.set('dateRange', range);

            });
        });s
    </script>
@endpush
