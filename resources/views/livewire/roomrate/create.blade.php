<div>
    <form wire:submit="save">
        <div class="flex flex-wrap gap-3 mb-4" wire:ignore>
            <div style="width: 30%">
                <flux:label>Seleccione un rango de fecha</flux:label>
                <flux:input type="text" id="dateRange" placeholder="dd/mm/YYYY  to  dd/mm/YYYY" readonly/>
                @dump($errors)
                <flux:error name="rangoFechas" />
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
                            @foreach($room['unit_adults'] as $unitAdult)
                                @if($unitAdult->bookable->plan_id ==  $board->id)
                                    <flux:field>
                                    <flux:input wire:model="unitAdultIds.{{ $unitAdult->id }}" type="number" placeholder="{{ \App\Admin\Properties\Constants::TYPE_OCCUPANCY[$i] }}"/>
                                    </flux:field>
                                    @php $i++; @endphp
                                @endif
                            @endforeach
                            @foreach($room['unit_children'] as $unitChild)
                                @if($unitChild->bookable->plan_id ==  $board->id)
                                    <flux:field>
                                    <flux:input wire:model="unitChildIds.{{ $unitChild->id }}" placeholder="Child (< {{ $unitChild->bookable->age }} yr) "/>
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
    document.addEventListener('DOMContentLoaded', function () {
        window.initFlatpickr("#dateRange", function(rango) {
            @this.set('rangoFechas', rango);
        });
    });
</script>
@endpush

