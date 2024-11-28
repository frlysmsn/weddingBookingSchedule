<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch user's bookings
$stmt = $db->prepare("
    SELECT id, wedding_date, status, 
           TIME_FORMAT(preferred_time, '%H:%i') as time,
           groom_name, bride_name 
    FROM bookings 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format bookings for calendar
$calendar_events = array_map(function($booking) {
    $color = '';
    switch($booking['status']) {
        case 'pending': $color = '#ffc107'; break;
        case 'approved': $color = '#28a745'; break;
        case 'rejected': $color = '#dc3545'; break;
    }
    
    return [
        'id' => $booking['id'],
        'title' => $booking['groom_name'] . ' & ' . $booking['bride_name'],
        'start' => $booking['wedding_date'] . 'T' . $booking['time'],
        'color' => $color,
        'extendedProps' => [
            'status' => $booking['status']
        ]
    ];
}, $user_bookings);
?>

<div class="calendar-container">
    <div class="calendar-header">
        <h2>Wedding Calendar</h2>
        <div class="calendar-legend">
            <span class="legend-item">
                <span class="dot pending"></span> Pending
            </span>
            <span class="legend-item">
                <span class="dot approved"></span> Approved
            </span>
            <span class="legend-item">
                <span class="dot rejected"></span> Rejected
            </span>
            <span class="legend-item">
                <span class="dot unavailable"></span> Unavailable
            </span>
        </div>
    </div>

    <div id="calendar"></div>
</div>

<style>
.calendar-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.calendar-legend {
    display: flex;
    gap: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 5px;
}

.dot.pending { background: #ffc107; }
.dot.approved { background: #28a745; }
.dot.rejected { background: #dc3545; }
.dot.unavailable { background: #6c757d; }

/* FullCalendar Customizations */
.fc-event {
    cursor: pointer;
}

.fc-day-past {
    background-color: #f8f9fa;
}

.fc-day-today {
    background-color: #e8f4ff !important;
}

.fc-button-primary {
    background-color: #007bff !important;
    border-color: #007bff !important;
}

.fc-button-primary:hover {
    background-color: #0056b3 !important;
    border-color: #0056b3 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: [
            // User's bookings
            ...<?= json_encode($calendar_events) ?>,
            
            // Fetch other booked dates (approved bookings)
            async function(info, successCallback, failureCallback) {
                try {
                    const response = await fetch('../api/get-booked-dates.php?' + new URLSearchParams({
                        start: info.startStr,
                        end: info.endStr
                    }));
                    const data = await response.json();
                    successCallback(data);
                } catch (error) {
                    failureCallback(error);
                }
            }
        ],
        eventClick: function(info) {
            // Only show details for user's own bookings
            if(info.event.extendedProps.isUserBooking) {
                Swal.fire({
                    title: 'Booking Details',
                    html: `
                        <div class="booking-details">
                            <p><strong>Date:</strong> ${info.event.startStr.split('T')[0]}</p>
                            <p><strong>Time:</strong> ${info.event.startStr.split('T')[1]}</p>
                            <p><strong>Status:</strong> ${info.event.extendedProps.status}</p>
                            <p><strong>Couple:</strong> ${info.event.title}</p>
                        </div>
                    `,
                    icon: 'info'
                });
            }
        },
        dateClick: function(info) {
            // Prevent booking on past dates
            if(info.date < new Date()) {
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Cannot book dates in the past',
                    icon: 'error'
                });
                return;
            }

            // Redirect to booking form with selected date
            window.location.href = `index.php?page=booking&date=${info.dateStr}`;
        }
    });
    calendar.render();
});
</script> 