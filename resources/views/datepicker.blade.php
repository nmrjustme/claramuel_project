
<div class="flex items-center justify-center px-4 py-20">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-7xl mx-4">
        <!-- Header with navigation -->
        <div class="flex items-center justify-between w-full mb-4">
            <div class="flex items-center flex-1">
                <button id="prevMonth" class="text-base md:text-lg font-bold px-2 py-1 dark:text-gray-800">&larr;</button>
                <div id="monthYear" class="text-lg md:text-xl font-bold text-center flex-1 dark:text-gray-800">
                    April 2025 / May 2025
                </div>
                <button id="nextMonth" class="text-base md:text-lg font-bold px-2 py-1 dark:text-gray-800">&rarr;</button>
            </div>
        </div>

        
        <!-- Flex layout for side-by-side calendars -->
        <div class="flex flex-col md:flex-row gap-4 md:gap-6 dark:text-gray-800">
            <!-- Calendar 1 -->
            <div class="flex-1 flex flex-col text-xs md:text-sm">
                <div class="hidden md:block h-px bg-gray-300 mx-2 md:mx-4"></div>
                <div class="grid grid-cols-7 text-center font-bold mb-2">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="py-1">{{ $day }}</div>
                    @endforeach
                </div>
                <div id="calendar1" class="grid grid-cols-7 gap-1 flex-1 min-h-[240px]"></div>
                <div class="hidden md:block h-px bg-gray-300 mx-2 md:mx-4"></div>
            </div>

            <!-- Vertical Divider (hidden on mobile) -->
            <div class="hidden md:block w-px bg-gray-300 mx-2 md:mx-4"></div>

            <!-- Calendar 2 -->
            <div class="flex-1 flex flex-col text-xs md:text-sm">
                <div class="hidden md:block h-px bg-gray-300 mx-2 md:mx-4"></div>
                <div class="grid grid-cols-7 text-center font-bold mb-2">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="py-1">{{ $day }}</div>
                    @endforeach
                </div>
                <div id="calendar2" class="grid grid-cols-7 gap-1 flex-1"></div>
                <div class="hidden md:block h-px bg-gray-300 mx-2 md:mx-4"></div>
            </div>
        </div>
        
        <!-- Date summary -->
        <!--
        <div class="justify-self-start mt-4 md:mt-6 text-sm">
            <p class="font-semibold text-gray-900">
                Check-in: <span id="checkin-date" class="  dark:text-red-700 text-red-500">None</span>
            </p>
            <p class="font-semibold text-gray-900">
                Check-out: <span id="checkout-date" class=" dark:text-red-700 text-red-500">None</span>
            </p>
            <p class="mt-2 font-semibold text-gray-900">
                Number of nights: <span id="number-of-nights">0</span>
            </p>
            <div class="flex flex-col mt-4">
                <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-800">Reminder</h2>
                <ul class="max-w-md space-y-1 text-red-500 list-disc list-inside dark:text-red-700">
                    <li>
                        Leave the checkout field blank if the checkout date is within the checkin day.
                    </li>
                    <li>
                        The check-in time for reservations is firmly set at 12:00 PM.
                    </li>
                </ul>
            </div>
        </div>-->
        
        <!-- Guest Counter -->
        @include('guest_counter')
        
        <!-- Action buttons -->
        <div class="flex flex-col md:flex-row mt-4 md:mt-6">
            <button id="clear-btn"
                class="px-3 py-2 bg-red-600 hover:bg-red-400 text-white w-full md:flex-1 text-sm md:text-base">Clear</button>
            <button id="submit-btn"
                class="px-3 py-2 bg-green-700 hover:bg-green-600 text-white w-full md:flex-1 text-sm md:text-base">Browse using your prefered date</button>
        </div>

    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const calendar1 = document.getElementById("calendar1");
        const calendar2 = document.getElementById("calendar2");
        const monthYear = document.getElementById("monthYear");
        const checkinEl = document.getElementById("checkin-date");
        const checkoutEl = document.getElementById("checkout-date");
        const clearBtn = document.getElementById("clear-btn");
        const submitBtn = document.getElementById("submit-btn");

        let baseDate = new Date();
        let checkin = null;
        let checkout = null;

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        }

        function isSameDay(date1, date2) {
            return (
                date1.getFullYear() === date2.getFullYear() &&
                date1.getMonth() === date2.getMonth() &&
                date1.getDate() === date2.getDate()
            );
        }

        function generateCalendar(calendarElement, monthOffset) {
            calendarElement.innerHTML = "";
            const today = new Date();
            // Create a new date object to avoid modifying the original
            const displayDate = new Date(baseDate);
            displayDate.setMonth(baseDate.getMonth() + monthOffset, 1); // Set to first day of month

            const year = displayDate.getFullYear();
            const month = displayDate.getMonth();
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement("div");
                calendarElement.appendChild(empty);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const isPast = date < new Date(new Date().setHours(0, 0, 0, 0));
                const el = document.createElement("div");

                el.textContent = day;
                el.className = "cursor-pointer p-2 rounded text-center transition" +
                    (isPast ? " dark:text-gray-200 text-gray-200 cursor-not-allowed " :
                        " hover:bg-blue-200 text-white dark:text-gray-800");

                if (!isPast) {
                    el.dataset.date = date.toISOString();
                    el.addEventListener("click", () => handleDateClick(date));
                }

                calendarElement.appendChild(el);
            }
        }

        function renderCalendars() {
            generateCalendar(calendar1, 0);
            generateCalendar(calendar2, 1);

            const m1 = baseDate.toLocaleString("default", {
                month: "long",
                year: "numeric"
            });
            const m2 = new Date(baseDate.getFullYear(), baseDate.getMonth() + 1).toLocaleString("default", {
                month: "long",
                year: "numeric",
            });
            monthYear.textContent = `${m1} / ${m2}`;

            updateUI();
        }

        function handleDateClick(date) {
            if (!checkin || (checkin && checkout)) {
                checkin = date;
                checkout = null;
            } else if (date > checkin) {
                checkout = date;
            } else {
                checkin = date;
                checkout = null;
            }
            updateUI();
        }

        function updateUI() {
            const allCells = [
                ...calendar1.querySelectorAll("div[data-date]"),
                ...calendar2.querySelectorAll("div[data-date]")
            ];

            allCells.forEach(el => {
                const date = new Date(el.dataset.date);
                el.classList.remove("bg-blue-500", "bg-blue-300", "text-white");

                if (checkin && isSameDay(date, checkin)) {
                    el.classList.add("bg-blue-500", "text-white");
                    el.innerHTML = `${date.getDate()}`;
                } else if (checkout && isSameDay(date, checkout)) {
                    el.classList.add("bg-blue-500", "text-white");
                    el.innerHTML = `${date.getDate()}`;
                } else if (checkin && checkout && date > checkin && date < checkout) {
                    el.classList.add("bg-blue-300", "text-white");
                } else {
                    el.innerHTML = date.getDate();
                }
            });

            checkinEl.textContent = checkin ? formatDate(checkin) : "None";
            checkoutEl.textContent = checkout ? formatDate(checkout) : "None";
            const nights = document.getElementById("number-of-nights");

            if (checkin && checkout) {
                const diffTime = Math.abs(checkout - checkin);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                nights.textContent = diffDays;
            } else {
                nights.textContent = "0";
            }
        }

        document.getElementById("prevMonth").addEventListener("click", () => {
            baseDate.setMonth(baseDate.getMonth() - 1);
            renderCalendars();
        });

        document.getElementById("nextMonth").addEventListener("click", () => {
            baseDate.setMonth(baseDate.getMonth() + 1);
            renderCalendars();
        });

        clearBtn.addEventListener("click", () => {
            checkin = null;
            checkout = null;
            updateUI();
        });

        submitBtn.addEventListener("click", () => {
            if (checkin && checkout) {
                alert(`Check-in: ${formatDate(checkin)}\nCheck-out: ${formatDate(checkout)}`);
            } else {
                alert("Please select both Check-in and Check-out dates.");
            }
        });

        renderCalendars();
    });
</script>
