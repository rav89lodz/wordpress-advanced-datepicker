 function renderCalendar(uid, current, current_year, current_month, excludedDates, calendar) {
    const minDate = new Date(); // domyślnie = dziś
    minDate.setHours(0,0,0,0);

    const year  = current.getFullYear();
    const month = current.getMonth();

    const firstDay    = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const weekdays    = ["Pn","Wt","Śr","Czw","Pt","Sb","Nd"];

    let html = `
            <div class="calendar-header">
                <button class="nav-btn" onclick="changeMonth` + uid + `(-1, event)">‹</button>
                <div>${year} - ${String(month+1).padStart(2,"0")}</div>
                <button class="nav-btn" onclick="changeMonth` + uid + `(1, event)">›</button>
            </div>
            <div class="calendar-grid">
        `;

    if(year < current_year || month <= current_month) {                
        html = `
            <div class="calendar-header">
                <div class="nav-btn"></div>
                <div>${year} - ${String(month+1).padStart(2,"0")}</div>
                <button class="nav-btn" onclick="changeMonth` + uid + `(1, event)">›</button>
            </div>
            <div class="calendar-grid">
        `;
    }

    weekdays.forEach(d => html += `<div class="weekday">${d}</div>`);

    const shift = firstDay === 0 ? 6 : firstDay - 1;
    for (let i = 0; i < shift; i++) html += `<div></div>`;

    for (let d = 1; d <= daysInMonth; d++) {
        const date = new Date(year, month, d);
        const day  = date.getDay();
        const dateStr = `${year}-${String(month+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;

        const dateObj = new Date(year, month, d);
        const isPastMin = dateObj < minDate;
        // const disabled = (day === 0 || day === 6 || excludedDates.includes(dateStr) || isPastMin) ? "disabled" : "";
        const disabled = (excludedDates.includes(dateStr) || isPastMin) ? "disabled" : "";

        html += `<div class="day ${disabled}" ${!disabled ? `onclick="selectDate` + uid + `(${d})"` : ""}>${d}</div>`;
    }

    html += `</div>`;
    calendar.innerHTML = html;
}
