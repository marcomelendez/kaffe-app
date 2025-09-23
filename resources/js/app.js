import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.FullCalendar = {
	Calendar,
	plugins: [dayGridPlugin, interactionPlugin],
};

window.initFlatpickr = function (selector, callback) {
	flatpickr(selector, {
		mode: 'range',
		dateFormat: 'd/m/Y',
		onChange: function (selectedDates) {
			if (selectedDates.length === 2) {
				callback({
					start: flatpickr.formatDate(selectedDates[0], 'Y-m-d'),
					end: flatpickr.formatDate(selectedDates[1], 'Y-m-d'),
				});
			}
		},
	});
};
