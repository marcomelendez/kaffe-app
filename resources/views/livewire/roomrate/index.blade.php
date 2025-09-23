<div>
    <flux:button variant="primary" wire:navigate href="{{ route('room_rate.create',['id'=>$propertyId]) }}">Nueva tarifa</flux:button>
    @livewire('roomrate.fullcalendar',['id'=>$propertyId])
</div>
