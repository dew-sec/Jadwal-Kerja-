<?php
function getDaysInMonth($year, $month) {
    return date('t', mktime(0, 0, 0, $month, 1, $year));
}

function getFirstDayOfMonth($year, $month) {
    return date('w', mktime(0, 0, 0, $month, 1, $year));
}

// Pola Shift
$shiftPattern = ['Libur', 'Siang', 'Pagi', 'Siang', 'Siang', 'Pagi'];

// Referensi Shift (Libur pada 2024-12-21)
$referenceDate = new DateTime('2024-12-21');

// Tanggal hari ini
$currentDate = new DateTime();
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : $currentDate->format('n');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentDate->format('Y');

// Fungsi untuk mendapatkan shift berdasarkan tanggal
function getShiftForDay($date, $referenceDate, $shiftPattern) {
    $diff = $date->diff($referenceDate);
    $diffDays = (int)$diff->format('%r%a'); // Menghitung selisih hari (positif/negatif)

    $patternLength = count($shiftPattern);
    // Menghitung indeks berdasarkan pola shift yang dimulai dari tanggal referensi
    $patternIndex = abs(($diffDays % $patternLength)); // Memastikan indeks selalu positif

    return $shiftPattern[$patternIndex];
}

// Warna untuk setiap shift
function getShiftColor($shift) {
    switch ($shift) {
        case 'Pagi':
            return 'background-color: #fef9c3;';
        case 'Siang':
            return 'background-color: #dbeafe;';
        case 'Libur':
            return 'background-color: #fee2e2;';
        default:
            return 'background-color: #f3f4f6;';
    }
}

$dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
$monthNames = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Navigasi bulan
$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kerja Caca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .calendar-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-title {
            font-size: 24px;
            font-weight: bold;
        }
        .calendar-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .nav-button {
            padding: 8px 16px;
            border: none;
            background: #f3f4f6;
            border-radius: 20px;
            cursor: pointer;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }
        .day-name {
            text-align: center;
            font-weight: bold;
            padding: 10px;
        }
        .day-cell {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }
        .legend {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }
        .today {
            border: 2px solid #3b82f6;
        }
    </style>
</head>
<body>
    <div class="calendar-card">
        <div class="calendar-header">
            <div class="calendar-title">Jadwal Kerja Caca</div>
            <div class="calendar-nav">
                <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="nav-button">&larr;</a>
                <span class="current-month"><?= $monthNames[$currentMonth] ?> <?= $currentYear ?></span>
                <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="nav-button">&rarr;</a>
            </div>
        </div>

        <div class="calendar-grid">
            <?php foreach ($dayNames as $day): ?>
                <div class="day-name"><?= $day ?></div>
            <?php endforeach; ?>

            <?php
            $firstDay = getFirstDayOfMonth($currentYear, $currentMonth);
            $daysInMonth = getDaysInMonth($currentYear, $currentMonth);

            for ($i = 0; $i < $firstDay; $i++) {
                echo '<div class="day-cell"></div>';
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = new DateTime("$currentYear-$currentMonth-$day");
                $shift = getShiftForDay($date, $referenceDate, $shiftPattern);
                $isToday = $date->format('Y-m-d') === (new DateTime())->format('Y-m-d');

                echo sprintf(
                    '<div class="day-cell %s" style="%s">
                        <div style="font-weight: bold;">%d</div>
                        <div style="font-size: 0.875rem;">%s</div>
                    </div>',
                    $isToday ? 'today' : '',
                    getShiftColor($shift),
                    $day,
                    $shift
                );
            }
            ?>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #fef9c3;"></div>
                <span>Pagi</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #dbeafe;"></div>
                <span>Siang</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #fee2e2;"></div>
                <span>Libur</span>
            </div>
        </div>
    </div>
</body>
</html>
