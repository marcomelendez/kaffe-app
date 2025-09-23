<div id="calendar" wire:ignore></div>

@push('scripts')
<script>
    document.addEventListener('init-calendar', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new window.FullCalendar.Calendar(calendarEl, {
      plugins: window.FullCalendar.plugins,
      initialView: 'dayGridMonth',
      events: @json($event),
       eventClick: function(info) {
                @this.call('eventClick', info.event.id);
        }
    });
    calendar.render();
  });
</script>
@endpush
