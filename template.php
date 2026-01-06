<?php
    $uid = rand(10000, 99999);
    $excluded = prepare_option_data('product_datepicker_excluded_days');
    global $wp_form_id, $wp_form_field_id, $forminator_form_id, $forminator_form_field_id;
?>

<div id="pickerWrapper-<?= $uid ?>" style="position:relative;">
    <input type="text" id="dateField-<?= $uid ?>" class="date-input date-input-hidden" name="datePickerField" placeholder="YYYY-MM-DD" readonly>
    <div id="calendar-<?= $uid ?>" class="calendar"></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const uid = <?= $uid ?>;
        const wrapper = document.getElementById("pickerWrapper-" + uid);
        let dateField = document.getElementById("dateField-" + uid);
        const calendar = document.getElementById("calendar-" + uid);
        const wpFormField = document.getElementById("wpforms-<?= $wp_form_id[0] ?>-field_<?= $wp_form_field_id[0] ?>");
        const forminatorField = document.getElementsByName('<?= $forminator_form_field_id[0] ?>');
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
            calendar.style.top = "-12px";
        } else if(forminatorField && forminatorField.length > 0) {
            dateField.remove();
            forminatorField[0].setAttribute("placeholder", "YYYY-MM-DD");
            forminatorField[0].setAttribute("readonly", true);
            forminatorField[0].setAttribute("id", "dateField-" + uid);
            dateField = forminatorField[0];
            if(forminatorField[0].parentNode.childNodes[0]) {
                forminatorField[0].parentNode.childNodes[0].setAttribute("for", "dateField-" + uid);
            }
            calendar.style.top = "-140px";
        } else {
            dateField.classList.remove('date-input-hidden');
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