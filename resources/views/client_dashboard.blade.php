<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: index.php");
$isAdmin = true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Bacoor Flood EWS</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
    <style>
        #map { height: 500px; width: 100%; }
        .status-box { padding: 20px; margin: 10px; border-radius: 10px; }
        .danger { background: red; color: white; }
        .safe { background: green; color: white; }
    </style>
</head>
<body>
    <h1>Bacoor Flood Monitoring Dashboard</h1>
    <div id="status" class="status-box">Loading status...</div>
    <div id="map"></div>
    
    <?php if($isAdmin): ?>
    <h2>Admin Panel</h2>
    <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse; text-align: left; margin-top: 10px;">
         <tr style="background-color: #f2f2f2;"><th>Time</th><th>Location</th><th>Status</th></tr>
         
         <tr>
             <td>2026-06-09 12:00:00</td>
             <td>Brgy_Talaba_Bacoor</td>
             <td>🟢 SAFE</td>
         </tr>
         <tr>
             <td>2026-06-09 11:45:00</td>
             <td>Brgy_Talaba_Bacoor</td>
             <td>🔴 FLOOD</td>
         </tr>
    </table>
    <?php endif; ?>
    
    <script>
        function initMap() {
            var bacoor = {lat: 14.4590, lng: 120.9399};
            var map = new google.maps.Map(document.getElementById('map'), {zoom: 13, center: bacoor});
            
            // Flood sensor location
            var marker = new google.maps.Marker({
                position: {lat: 14.4590, lng: 120.9399},
                map: map,
                title: 'Flood Sensor - Brgy. Talaba'
            });
            
            // Update status via AJAX
            setInterval(function() {
                fetch('get_status.php')
                    .then(res => res.json())
                    .then(data => {
                        var statusDiv = document.getElementById('status');
                        if(data.is_flooding) {
                            statusDiv.innerHTML = '⚠️ ALERT: On-going flooding in Bacoor! ⚠️';
                            statusDiv.className = 'status-box danger';
                        } else {
                            statusDiv.innerHTML = '✅ SAFE: No flooding detected. ✅';
                            statusDiv.className = 'status-box safe';
                        }
                    });
            }, 30000);
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>
</body>
</html>