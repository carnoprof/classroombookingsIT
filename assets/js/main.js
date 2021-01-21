// up.motion.config.duration = 150;
up.motion.config.enabled = false;


/**
 * Settings: Initialise sortable tables
 *
 */
up.compiler('.sort-table', function(element, cols) {
	new SortableTable(element, cols);
});


/**
 * Bookings: Automatically submit form when picking a date.
 *
 */
up.compiler('#bookings_date', function(element) {
	var input = document.getElementById("chosen_date"),
		form = document.getElementById("bookings_date");
	input.addEventListener("change", function(event) {
		form.submit();
	});
});


/**
 * Settings: LDAP: Form for testing the settings
 *
 */
up.compiler('[ldap-settings]', function(element) {

	function populateTestForm() {
		var attrs = up.Params.fromForm('#ldap_settings'),
			attrsArray = attrs.toArray();
		for (var i = 0; i < attrsArray.length; i++) {
			var sel = "[ldap-settings] [name='" + attrsArray[i]['name'] + "']";
			var dest = "[ldap-test] [name='" + attrsArray[i]['name'] + "']";
			var testEl = up.element.get(dest);
			if (testEl) {
				up.element.setAttrs(testEl, { value: attrsArray[i]['value'] });
			}
		}
	};

	populateTestForm();

	up.observe(element, { batch: true }, function(diff) {
		// console.log('Observed one or more changes: %o', diff)
		populateTestForm();
	});

});


/**
 * Session calendars: Config mode: Click on dates to change Timetable Week assignments.
 *
 */
up.compiler('.session-calendars.mode-config', function(sessionEl, data) {

	var weekIds = [];
	var weekClassNames = [];
	var numWeeks = data && data.weeks ? data.weeks.length : 0;

	// Process list of available weeks
	for (var i = 0; i < numWeeks; i++) {
		var weekId = parseInt(data.weeks[i], 10);
		className = 'week-' + weekId;
		weekIds.push(weekId);
		weekClassNames.push(className);
	}
	// Add blank entry for cycling through items
	weekIds.push(null);

	// Click event for changing week assignment of a given date
	up.on(sessionEl, 'click', '.date-btn', function(event, el) {

		var cell = el.closest('.date-cell');

		var weekstart = cell.getAttribute('data-weekstart');
		var weekid = cell.getAttribute('data-weekid');

		// Current Week ID
		var curWeekId = weekid ? '' + weekid : null;
		// Array index of the current selected week
		var curWeekIdx = weekIds.indexOf(parseInt(curWeekId, 10));
		// Array index of next week to select
		var nextWeekIdx = (curWeekIdx + 1) % weekIds.length;

		// Next Week ID from the list
		var newWeekId = weekIds[ (curWeekIdx + 1) % weekIds.length ];

		// All cells (potentially on other months) whose week starts on `weekstart`
		var cells = up.element.all(sessionEl, '.date-cell[data-weekstart="' + weekstart + '"]');

		// Process all cells that match start of week
		up.util.each(cells, function(cell) {

			// Remove existing week classes
			for (var i = 0; i < weekClassNames.length; i++) {
				cell.classList.remove(weekClassNames[i]);
			}

			// An actual Week ID to add
			if (newWeekId !== null) {
				cell.classList.add('week-' + newWeekId);
			}

			// Update value of hidden input
			var input = up.element.first(cell, 'input[type=hidden]');
			input.value = newWeekId;

			// Update the data- attr with the new Week ID
			cell.setAttribute('data-weekid', newWeekId);

		});

	});

});
