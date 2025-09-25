<div>
    <flux:button variant="primary" wire:navigate href="{{ route('room_rate.create',['id'=>$propertyId]) }}">Nueva tarifa</flux:button>
    <div id="calendar" wire:ignore></div>


    <flux:modal wire:model.self="showRates">
        <form wire:submit="save">
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
                                         <flux:label>{{ \App\Admin\Properties\Constants::TYPE_OCCUPANCY[$i] }}</flux:label>
                                        <flux:input wire:model="unitAdultIds.{{ $unitAdult->id }}" type="number"
                                            placeholder="{{ \App\Admin\Properties\Constants::TYPE_OCCUPANCY[$i] }}" />
                                    </flux:field>
                                    @php $i++; @endphp
                                @endif
                            @endforeach
                            @foreach ($room['unit_children'] as $unitChild)
                                @if ($unitChild->bookable->plan_id == $board->id)
                                    <flux:field>
                                        <flux:label>Child (< {{ $unitChild->bookable->age }} yr) </flux:label>
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

            <div class="flex justify-end mt-4">
            <flux:button type="submit" variant="primary">Guardar</flux:button>
        </div>
        </form>
    </flux:modal>
</div>

@push('scripts')
<script>
    document.addEventListener('init-calendar', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new window.FullCalendar.Calendar(calendarEl, {
      plugins: window.FullCalendar.plugins,
      initialView: 'dayGridMonth',
      selectable: true,
      events: @json($event),
        dateClick: function(info) {
                @this.call('dateClick', {'start':info.dateStr });
        },
       eventClick: function(info) {
                @this.call('eventClick', info.event.id);
        }
    });
    calendar.render();
  });
</script>
@endpush
