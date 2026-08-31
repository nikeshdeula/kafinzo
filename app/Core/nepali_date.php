<?php

if (!function_exists('nepali_date')) {
    function nepali_date(string $format = 'Y-m-d', string $englishDate = '', bool $isEnglishDate = true): string {
        if ($isEnglishDate && !empty($englishDate)) {
            $bs = ad_to_bs($englishDate);
            if (!$bs) return $englishDate;
            return format_bs_date($bs, $format);
        }
        $ad = date('Y-m-d');
        $bs = ad_to_bs($ad);
        if (!$bs) return date($format);
        return format_bs_date($bs, $format);
    }
}

if (!function_exists('ad_to_bs')) {
    function ad_to_bs(string $adDate): ?array {
        $ad = strtotime($adDate);
        if (!$ad) return null;

        $adYear = (int)date('Y', $ad);
        $adMonth = (int)date('m', $ad);
        $adDay = (int)date('d', $ad);

        $referenceAdDate = strtotime('2014-04-13');
        $referenceBsDate = ['year' => 2071, 'month' => 1, 'day' => 1];

        $daysDiff = ($ad - $referenceAdDate) / (60 * 60 * 24);
        $bsDate = $referenceBsDate;

        if ($daysDiff >= 0) {
            for ($i = 0; $i < $daysDiff; $i++) {
                $bsDate = addBsDay($bsDate);
            }
        } else {
            for ($i = 0; $i < abs($daysDiff); $i++) {
                $bsDate = subtractBsDay($bsDate);
            }
        }

        return $bsDate;
    }
}

if (!function_exists('bs_to_ad')) {
    function bs_to_ad(int $bsYear, int $bsMonth, int $bsDay): ?string {
        $referenceBsDate = ['year' => 2071, 'month' => 1, 'day' => 1];
        $referenceAdDate = strtotime('2014-04-13');
        $bsDate = ['year' => $bsYear, 'month' => $bsMonth, 'day' => $bsDay];
        $totalDays = 0;
        $currentBs = ['year' => 2071, 'month' => 1, 'day' => 1];
        $found = false;
        while (!$found) {
            if ($currentBs['year'] == $bsYear && $currentBs['month'] == $bsMonth && $currentBs['day'] == $bsDay) {
                $found = true;
                break;
            }
            $totalDays++;
            $currentBs = addBsDay($currentBs);
            if ($totalDays > 50000) break;
        }
        if (!$found) return null;
        $adTimestamp = $referenceAdDate + ($totalDays * 60 * 60 * 24);
        return date('Y-m-d', $adTimestamp);
    }
}

if (!function_exists('addBsDay')) {
    function addBsDay(array $bsDate): array {
        $bsDate['day']++;
        $daysInMonth = getBsDaysInMonth($bsDate['year'], $bsDate['month']);
        if ($bsDate['day'] > $daysInMonth) {
            $bsDate['day'] = 1;
            $bsDate['month']++;
            if ($bsDate['month'] > 12) {
                $bsDate['month'] = 1;
                $bsDate['year']++;
            }
        }
        return $bsDate;
    }
}

if (!function_exists('subtractBsDay')) {
    function subtractBsDay(array $bsDate): array {
        $bsDate['day']--;
        if ($bsDate['day'] < 1) {
            $bsDate['month']--;
            if ($bsDate['month'] < 1) {
                $bsDate['month'] = 12;
                $bsDate['year']--;
            }
            $bsDate['day'] = getBsDaysInMonth($bsDate['year'], $bsDate['month']);
        }
        return $bsDate;
    }
}

if (!function_exists('getBsDaysInMonth')) {
    function getBsDaysInMonth(int $year, int $month): int {
        $monthDays = [
            1 => 31, 2 => 31, 3 => 31, 4 => 32, 5 => 31, 6 => 30,
            7 => 29, 8 => 29, 9 => 30, 10 => 29, 11 => 30, 12 => 30
        ];

        if (isset($monthDays[$month])) {
            return $monthDays[$month];
        }
        return 30;
    }
}

if (!function_exists('format_bs_date')) {
    function format_bs_date(array $bsDate, string $format): string {
        $replacements = [
            'Y' => $bsDate['year'],
            'y' => substr($bsDate['year'], -2),
            'm' => str_pad($bsDate['month'], 2, '0', STR_PAD_LEFT),
            'n' => $bsDate['month'],
            'd' => str_pad($bsDate['day'], 2, '0', STR_PAD_LEFT),
            'j' => $bsDate['day'],
        ];

        $nepaliMonths = [
            1 => 'Baisakh', 2 => 'Jestha', 3 => 'Ashadh', 4 => 'Shrawan',
            5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 8 => 'Mangsir',
            9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra'
        ];

        $nepaliMonthsShort = [
            1 => 'Baisakh', 2 => 'Jestha', 3 => 'Ashadh', 4 => 'Shrawan',
            5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 8 => 'Mangsir',
            9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra'
        ];

        $search = array_keys($replacements);
        $result = str_replace($search, array_values($replacements), $format);

        if (strpos($result, 'F') !== false) {
            $result = str_replace('F', $nepaliMonths[$bsDate['month']] ?? '', $result);
        }
        if (strpos($result, 'M') !== false) {
            $result = str_replace('M', $nepaliMonthsShort[$bsDate['month']] ?? '', $result);
        }

        return $result;
    }
}

if (!function_exists('format_ad_date_for_input')) {
    function format_ad_date_for_input(string $adDate = ''): string {
        if (empty($adDate)) return date('Y-m-d');
        $date = strtotime($adDate);
        if (!$date) return date('Y-m-d');
        return date('Y-m-d', $date);
    }
}

if (!function_exists('current_nepali_date')) {
    function current_nepali_date(string $format = 'Y-m-d'): string {
        $ad = date('Y-m-d');
        $bs = ad_to_bs($ad);
        if (!$bs) return date($format);
        return format_bs_date($bs, $format);
    }
}

if (!function_exists('get_nepali_month_year')) {
    function get_nepali_month_year(string $adDate = ''): array {
        if (empty($adDate)) $adDate = date('Y-m-d');
        $bs = ad_to_bs($adDate);
        if (!$bs) return ['year' => '', 'month' => ''];
        return ['year' => $bs['year'], 'month' => $bs['month']];
    }
}

if (!function_exists('nepali_date_input')) {
    function nepali_date_input(string $name, string $value = '', string $label = '', array $attrs = []): string {
        $defaultAttrs = [
            'type' => 'date',
            'name' => $name,
            'class' => 'form-control',
            'value' => $value ?: date('Y-m-d')
        ];
        $attrs = array_merge($defaultAttrs, $attrs);
        $attrStr = '';
        foreach ($attrs as $k => $v) {
            if ($k !== 'value') $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
        }
        $bs = ad_to_bs($value ?: date('Y-m-d'));
        $nepaliDate = $bs ? format_bs_date($bs, 'd M Y') : '';
        $required = isset($attrs['required']) && $attrs['required'] ? ' <span class="text-danger">*</span>' : '';
        return '<label class="form-label">' . htmlspecialchars($label) . $required . '</label>
                <div class="input-group">
                    <input' . $attrStr . ' value="' . htmlspecialchars($value ?: date('Y-m-d')) . '" onchange="updateNepaliDate(this)">
                    <span class="input-group-text nepali-date-display" style="font-size:0.85rem;min-width:120px;">' . ($nepaliDate ? 'BS ' . $nepaliDate : '') . '</span>
                </div>';
    }
}

if (!function_exists('nepali_date_picker')) {
    function nepali_date_picker(string $name, string $value = '', string $label = '', array $attrs = []): string {
        $uniqueId = 'np_' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '_' . uniqid();
        $required = isset($attrs['required']) && $attrs['required'] ? ' <span class="text-danger">*</span>' : '';
        $readonlyAttr = isset($attrs['readonly']) && $attrs['readonly'] ? ' readonly' : '';
        $placeholder = isset($attrs['placeholder']) ? $attrs['placeholder'] : 'YYYY-MM-DD';
        $currentAd = !empty($value) ? $value : date('Y-m-d');
        $bs = ad_to_bs($currentAd);
        if (!$bs) {
            $bs = ['year' => 2080, 'month' => 1, 'day' => 1];
        }
        $nepaliValue = format_bs_date($bs, 'Y-m-d');
        $monthName = format_bs_date($bs, 'F');
        $year = $bs['year'];
        $month = $bs['month'];
        $day = $bs['day'];
        $monthDays = getBsDaysInMonth($year, $month);
        $startDayOfWeek = date('N', strtotime(bs_to_ad($year, $month, 1))) % 7;
        $nepaliMonths = [
            1 => 'Baisakh', 2 => 'Jestha', 3 => 'Ashadh', 4 => 'Shrawan',
            5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 8 => 'Mangsir',
            9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra'
        ];
        $calendarHtml = '<div class="nepali-calendar-popup" id="cal_' . $uniqueId . '" style="display:none;position:absolute;z-index:1050;background:#fff;border:1px solid #dee2e6;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);width:280px;padding:12px;margin-top:4px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="npPrevMonth(\'' . $uniqueId . '\')"><i class="bi bi-chevron-left"></i></button>
                <span class="fw-600 small" id="np_month_' . $uniqueId . '">' . $monthName . ' ' . $year . '</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="npNextMonth(\'' . $uniqueId . '\')"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="row g-1 mb-2">
                <div class="col-4"><select class="form-select form-select-sm" id="np_month_select_' . $uniqueId . '" onchange="npChangeMonth(\'' . $uniqueId . '\', this.value)">';
        for ($m = 1; $m <= 12; $m++) {
            $calendarHtml .= '<option value="' . $m . '"' . ($m == $month ? ' selected' : '') . '>' . $nepaliMonths[$m] . '</option>';
        }
        $calendarHtml .= '</select></div>
                <div class="col-4"><input type="number" class="form-control form-control-sm" id="np_year_' . $uniqueId . '" value="' . $year . '" min="2000" max="2100" onchange="npChangeYear(\'' . $uniqueId . '\', this.value)"></div>
                <div class="col-4"><button type="button" class="btn btn-primary btn-sm w-100" onclick="npGoToToday(\'' . $uniqueId . '\')">Today</button></div>
            </div>
            <div class="row g-1 text-center mb-1">
                <div class="col-1 small text-muted fw-600">Su</div>
                <div class="col-1 small text-muted fw-600">Mo</div>
                <div class="col-1 small text-muted fw-600">Tu</div>
                <div class="col-1 small text-muted fw-600">We</div>
                <div class="col-1 small text-muted fw-600">Th</div>
                <div class="col-1 small text-muted fw-600">Fr</div>
                <div class="col-1 small text-muted fw-600">Sa</div>
            </div>
            <div class="row g-1 text-center" id="np_days_' . $uniqueId . '">';
        $daysInMonth = $monthDays;
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $calendarHtml .= '<div class="col-1"></div>';
        }
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $selected = ($d == $day && $month == $bs['month'] && $year == $bs['year']) ? 'btn-primary' : 'btn-light';
            $calendarHtml .= '<div class="col-1"><button type="button" class="btn btn-sm ' . $selected . ' w-100 py-1" onclick="npSelectDay(\'' . $uniqueId . '\', ' . $d . ')">' . $d . '</button></div>';
        }
        $calendarHtml .= '</div></div>';
        $jsMonthsData = [];
        foreach (range(2070, 2095) as $y) {
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $months[] = $m . ':' . getBsDaysInMonth($y, $m);
            }
            $jsMonthsData[] = 'window.npMonthsData[' . $y . '] = {' . implode(',', $months) . '}';
        }
        $jsMonthsDataStr = implode(";\n                    ", $jsMonthsData);
        return '<div class="input-group nepali-date-picker" style="position:relative;">
                    <input type="text" class="form-control" id="' . $uniqueId . '" value="' . htmlspecialchars($nepaliValue) . '" placeholder="' . $placeholder . '"' . $readonlyAttr . ' onfocus="npOpenCalendar(\'' . $uniqueId . '\')" onchange="npSyncHidden(this, \'' . $name . '\')">
                    <span class="input-group-text" style="font-size:0.75rem;padding:4px 10px;color:#6c757d;font-weight:600;letter-spacing:0.5px;">BS</span>
                    <button type="button" class="btn btn-outline-secondary" style="padding:4px 10px;" onclick="npOpenCalendar(\'' . $uniqueId . '\')"><i class="bi bi-calendar3"></i></button>
                    <input type="hidden" name="' . htmlspecialchars($name) . '" id="' . $uniqueId . '_hidden" value="' . htmlspecialchars($currentAd) . '">
                    ' . $calendarHtml . '
                </div>
                <style>
                .nepali-date-picker .form-control {{ border-right: 0; }}
                .nepali-date-picker .input-group-text {{ border-left: 0; border-right: 0; background: #f8f9fa; }}
                .nepali-date-picker .btn-outline-secondary {{ border-left: 0; }}
                .nepali-date-picker .form-control:focus {{ box-shadow: none; border-color: #dee2e6; }}
                .nepali-date-picker .form-control:focus + .input-group-text {{ border-color: #86b7fe; }}
                .nepali-date-picker .form-control:focus + .input-group-text + .btn {{ border-color: #86b7fe; }}
                </style>
                <script>
                if (!window.npMonthsData) {
                    window.npMonthsData = {};
                    ' . $jsMonthsDataStr . ';
                }
                if (!window.bsToAd) {
                    window.bsToAd = function(bsYear, bsMonth, bsDay) {
                        if (!window.npMonthsData) return null;
                        var totalDays = 0;
                        for (var y = 2071; y < bsYear; y++) {
                            if (window.npMonthsData[y]) {
                                for (var m = 1; m <= 12; m++) {
                                    if (window.npMonthsData[y][m]) totalDays += window.npMonthsData[y][m];
                                }
                            }
                        }
                        if (window.npMonthsData[bsYear]) {
                            for (var m = 1; m < bsMonth; m++) {
                                if (window.npMonthsData[bsYear][m]) totalDays += window.npMonthsData[bsYear][m];
                            }
                        }
                        totalDays += bsDay - 1;
                        var referenceAd = new Date(2014, 3, 13);
                        var adDate = new Date(referenceAd.getTime() + totalDays * 24 * 60 * 60 * 1000);
                        var y = adDate.getFullYear();
                        var m = String(adDate.getMonth() + 1).padStart(2, "0");
                        var d = String(adDate.getDate()).padStart(2, "0");
                        return y + "-" + m + "-" + d;
                    };
                }
                function npGetBsDaysInMonth(year, month) {
                    if (window.npMonthsData && window.npMonthsData[year] && window.npMonthsData[year][month] !== undefined) {
                        return window.npMonthsData[year][month];
                    }
                    return 30;
                }
                var npState_' . $uniqueId . ' = {year: ' . $year . ', month: ' . $month . ', day: ' . $day . '};
                function npOpenCalendar(id) {
                    document.querySelectorAll(".nepali-calendar-popup").forEach(function(el) { if (el.id !== "cal_" + id) el.style.display = "none"; });
                    document.getElementById("cal_" + id).style.display = "block";
                }
                function npCloseCalendar(id) {
                    document.getElementById("cal_" + id).style.display = "none";
                }
                function npPrevMonth(id) {
                    var state = npState_' . $uniqueId . ';
                    state.month--;
                    if (state.month < 1) { state.month = 12; state.year--; }
                    npRenderCalendar(id);
                }
                function npNextMonth(id) {
                    var state = npState_' . $uniqueId . ';
                    state.month++;
                    if (state.month > 12) { state.month = 1; state.year++; }
                    npRenderCalendar(id);
                }
                function npChangeMonth(id, m) {
                    npState_' . $uniqueId . '.month = parseInt(m);
                    npRenderCalendar(id);
                }
                function npChangeYear(id, y) {
                    npState_' . $uniqueId . '.year = parseInt(y);
                    npRenderCalendar(id);
                }
                function npGoToToday(id) {
                    var now = new Date();
                    var ad = now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0") + "-" + String(now.getDate()).padStart(2, "0");
                    var bs = ' . json_encode(ad_to_bs(date('Y-m-d'))) . ';
                    if (bs) {
                        npState_' . $uniqueId . ' = {year: bs.year, month: bs.month, day: bs.day};
                        npRenderCalendar(id);
                        npSelectDay(id, bs.day);
                    }
                }
                function npSelectDay(id, d) {
                    var state = npState_' . $uniqueId . ';
                    state.day = d;
                    var bsDate = state.year + "-" + String(state.month).padStart(2, "0") + "-" + String(d).padStart(2, "0");
                    document.getElementById(id).value = bsDate;
                    var ad = bsToAd(state.year, state.month, d);
                    if (ad) {
                        document.getElementById(id + "_hidden").value = ad;
                    }
                    npCloseCalendar(id);
                    npRenderCalendar(id);
                }
                function npSyncHidden(input, name) {
                    var parts = input.value.split("-");
                    if (parts.length === 3) {
                        var y = parseInt(parts[0]), m = parseInt(parts[1]), d = parseInt(parts[2]);
                        var ad = bsToAd(y, m, d);
                        if (ad) {
                            document.getElementById(input.id + "_hidden").value = ad;
                            npState_' . $uniqueId . ' = {year: y, month: m, day: d};
                        }
                    }
                }
                function npRenderCalendar(id) {
                    var state = npState_' . $uniqueId . ';
                    var months = ["Baisakh","Jestha","Ashadh","Shrawan","Bhadra","Ashwin","Kartik","Mangsir","Poush","Magh","Falgun","Chaitra"];
                    document.getElementById("np_month_" + id).textContent = months[state.month - 1] + " " + state.year;
                    document.getElementById("np_month_select_" + id).value = state.month;
                    document.getElementById("np_year_" + id).value = state.year;
                    var daysInMonth = npGetBsDaysInMonth(state.year, state.month);
                    var adDate = bsToAd(state.year, state.month, 1);
                    var startDayOfWeek = adDate ? new Date(adDate).getDay() : 0;
                    var html = "";
                    for (var i = 0; i < startDayOfWeek; i++) { html += "<div class=\"col-1\"></div>"; }
                    for (var d = 1; d <= daysInMonth; d++) {
                        var selected = (d == state.day) ? "btn-primary" : "btn-light";
                        html += "<div class=\"col-1\"><button type=\"button\" class=\"btn btn-sm " + selected + " w-100 py-1\" onclick=\"npSelectDay(\'" + id + "\', " + d + ")\" >" + d + "</button></div>";
                    }
                    document.getElementById("np_days_" + id).innerHTML = html;
                }
                document.addEventListener("click", function(e) {
                    var cal = document.getElementById("cal_" + id);
                    if (cal && cal.style.display === "block" && !cal.contains(e.target) && e.target !== document.getElementById(id) && !e.target.closest("button[onclick*=\"npOpenCalendar\"]")) {
                        cal.style.display = "none";
                    }
                });
                </script>';
    }
}
