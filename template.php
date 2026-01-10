<?php
    $uid = rand(10000, 99999);
    $excluded = prepare_option_data('product_datepicker_excluded_days');
    global $wp_form_id, $wp_form_field_id, $forminator_form_field_id;
	
	$temp_wp_form_id = null;
	$temp_wp_form_field_id = null;
	$temp_forminator_form_field_id = null;
	
	if(count($wp_form_id) > 0) {
		$temp_wp_form_id = $wp_form_id[0];
	}
	if(count($wp_form_field_id) > 0) {
		$temp_wp_form_field_id = $wp_form_field_id[0];
	}
	if(count($forminator_form_field_id) > 0) {
		$temp_forminator_form_field_id = $forminator_form_field_id[0];
	}

    echo '<div id="pickerWrapper-' . $uid . '" style="position:relative;">
            <input type="text" id="dateField-' . $uid . 
            '" class="date-input date-input-hidden" name="datePickerField" placeholder="YYYY-MM-DD" readonly>
            <div id="calendar-' . $uid . '" class="calendar"></div>
        </div>';
?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const uid = <?= $uid ?>;
        let wrapper = document.getElementById("pickerWrapper-" + uid);
        let dateField = document.getElementById("dateField-" + uid);
        const calendar = document.getElementById("calendar-" + uid);
        const wpFormField = document.getElementById("wpforms-<?= $temp_wp_form_id ?>-field_<?= $temp_wp_form_field_id ?>");
        const forminatorField = document.getElementsByName('<?= $temp_forminator_form_field_id ?>');
        let excludedDates = <?php echo json_encode($excluded); ?>;

        if(wpFormField) {
            dateField.remove();
            wpFormField.setAttribute("placeholder", "YYYY-MM-DD");
            wpFormField.setAttribute("readonly", true);
            wpFormField.setAttribute("id", "dateField-" + uid);
            dateField = wpFormField;
            if(wpFormField.parentNode.childNodes[0]) {
                wpFormField.parentNode.childNodes[0].setAttribute("for", "dateField-" + uid);
            }
            calendar.style.marginTop = "-12px";
        } else if(forminatorField && forminatorField.length > 0) {
            dateField.remove();
            forminatorField[0].setAttribute("placeholder", "YYYY-MM-DD");
            forminatorField[0].setAttribute("readonly", true);
            forminatorField[0].setAttribute("id", "dateField-" + uid);
            dateField = forminatorField[0];
            if(forminatorField[0].parentNode.childNodes[0]) {
                forminatorField[0].parentNode.childNodes[0].setAttribute("for", "dateField-" + uid);
            }
            forminatorField[0].parentNode.appendChild(calendar);
            calendar.style.marginTop = "8px";
        } else {
            dateField.classList.remove('date-input-hidden');

            let table = document.createElement('table');
            let tr = document.createElement('tr');
            let td1 = document.createElement('td');
            let td2 = document.createElement('td');
            let pTag = document.createElement('p');
            pTag.innerText = "Wybierz datę";

            pTag.classList.add('calendar-p-tag');
            table.classList.add('calendar-woo-table');
			
			td1.style.width = "50%";

            td1.appendChild(pTag);
            td2.appendChild(dateField);
            td2.appendChild(calendar);

            tr.appendChild(td1);
            tr.appendChild(td2);

            table.appendChild(tr);
            wrapper.innerHTML = "";
            wrapper.appendChild(table);            
        }

        let current = new Date();
        const current_year = current.getFullYear();
        const current_month = current.getMonth();

        window.changeMonth<?= $uid ?> = function(dir, event) {
            if (event) event.stopPropagation();
            current.setMonth(current.getMonth() + dir);
            renderCalendar(uid, current, current_year, current_month, excludedDates, calendar);
        }

        window.selectDate<?= $uid ?> = function(day) {
            const y = current.getFullYear();
            const m = current.getMonth() + 1;
            const formatted = `${y}-${String(m).padStart(2,"0")}-${String(day).padStart(2,"0")}`;
            dateField.value = formatted;           
            calendar.style.display = "none";
        }

        // Otwieranie kalendarza
        dateField.addEventListener("click", (e) => {
            e.stopPropagation();
            calendar.style.display = "block";

            const now = new Date();
            month = now.getMonth();
            year = now.getFullYear();

            renderCalendar(uid, current, current_year, current_month, excludedDates, calendar);
        });

        // Zamknięcie przy kliknięciu poza
        document.addEventListener("click", (e) => {
            if (!calendar.contains(e.target) && e.target !== dateField) {
                calendar.style.display = "none";
            }
        });

        renderCalendar(uid, current, current_year, current_month, excludedDates, calendar);
    });
</script>