<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Weather Impact & Road Status Map - CDRRMO Bacoor</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <style>
        html, body { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
        .leaflet-routing-container { display: none !important; }
        .leaflet-control-zoom { margin-bottom: 80px !important; }
        @media (min-width: 768px) { .leaflet-control-zoom { margin-bottom: 20px !important; } }
    </style>
</head>
<body class="bg-slate-100 font-sans relative w-full h-full text-gray-800">

    <div class="absolute top-4 left-4 right-4 md:top-6 md:left-6 md:right-auto md:w-[400px] z-[9999] flex flex-col gap-2">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200 p-3.5 flex items-center justify-between gap-3 transition-all focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
            <div class="flex items-center gap-3 flex-1">
                <img src="{{ asset('images/marker.png') }}" alt="Location" class="w-5 h-5 object-contain">
                <input type="text" id="mapSearchInput" placeholder="Search Barangay (e.g., Talaba)..." class="bg-transparent border-none text-slate-800 text-sm font-bold w-full focus:outline-none placeholder-gray-400" onfocus="toggleSearchDropdown(true)">
            </div>
            
            <div class="flex items-center gap-3 border-l border-gray-200 pl-3 relative">
                <div onclick="toggleUserDropdown(event)" class="w-8 h-8 rounded-full bg-slate-100 border border-gray-200 hover:bg-slate-200 flex items-center justify-center shadow-xs cursor-pointer transition-colors select-none overflow-hidden">
                    <img src="{{ asset('images/circle-user.png') }}" alt="Profile" class="w-5 h-5 object-contain opacity-75">
                </div>
                <div id="userProfileDropdownMenu" class="hidden absolute top-11 right-0 w-48 bg-white/95 backdrop-blur-md rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-[10000]">
                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-wider">Account Settings</div>
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->full_name ?? 'User' }}</p>
                        <p class="text-[11px] font-semibold text-blue-600 truncate">{{ '@' . (Auth::user()->username ?? 'guest') }}</p>
                    </div>
                    <div class="p-1">
                        <a href="{{ route('resident.logout') ?? '#' }}" class="w-full text-left px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2 transition-colors cursor-pointer text-decoration-none">
                            <img src="{{ asset('images/leave.png') }}" alt="Log Out" class="w-4 h-4 object-contain">
                            Log Out Session
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="searchDropdownMenu" class="hidden bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transition-all max-h-[300px] overflow-y-auto">
            <div class="p-2.5 bg-gray-50 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-wider">Select Monitoring Station Node</div>
            <ul class="divide-y divide-gray-100 font-bold text-xs text-slate-700">
                <li onclick="simulateLocationSelect('Talaba_II', 'CRITICAL')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex justify-between items-center transition-colors">
                    <div class="flex items-center gap-2"><img src="{{ asset('images/people-roof.png') }}" alt="Barangay" class="w-4 h-4 object-contain opacity-75"><span>Brgy. Talaba II</span></div>
                    <span id="badge-Talaba_II" class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-sm uppercase font-black">Critical</span>
                </li>
                <li onclick="simulateLocationSelect('Mambog_I', 'MODERATE')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex justify-between items-center transition-colors">
                    <div class="flex items-center gap-2"><img src="{{ asset('images/people-roof.png') }}" alt="Barangay" class="w-4 h-4 object-contain opacity-75"><span>Brgy. Mambog I</span></div>
                    <span id="badge-Mambog_I" class="text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded-sm uppercase font-black">Moderate</span>
                </li>
                <li onclick="simulateLocationSelect('Habay_I', 'MODERATE')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex justify-between items-center transition-colors">
                    <div class="flex items-center gap-2"><img src="{{ asset('images/people-roof.png') }}" alt="Barangay" class="w-4 h-4 object-contain opacity-75"><span>Brgy. Habay I</span></div>
                    <span id="badge-Habay_I" class="text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded-sm uppercase font-black">Moderate</span>
                </li>
                <li onclick="simulateLocationSelect('Molino_I', 'LOW')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex justify-between items-center transition-colors">
                    <div class="flex items-center gap-2"><img src="{{ asset('images/people-roof.png') }}" alt="Barangay" class="w-4 h-4 object-contain opacity-75"><span>Brgy. Molino I</span></div>
                    <span id="badge-Molino_I" class="text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded-sm uppercase font-black">Low</span>
                </li>
                <li onclick="simulateLocationSelect('NIOG_II_Bacoor_cavite', 'SAFE')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex justify-between items-center transition-colors">
                    <div class="flex items-center gap-2"><img src="{{ asset('images/people-roof.png') }}" alt="Barangay" class="w-4 h-4 object-contain opacity-75"><span>Brgy. Niog II</span></div>
                    <span id="badge-NIOG_II_Bacoor_cavite" class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-sm uppercase font-black">Standby</span>
                </li>
            </ul>
        </div>
    </div>

    <div id="userLiveGpsMap" class="w-full h-full z-10 relative"></div>
    <button id="mobileInfoToggleBtn" onclick="toggleMobileInfoCard()" class="md:hidden absolute bottom-6 left-1/2 -translate-x-1/2 z-[9998] bg-[#0F2942] text-white px-6 py-3.5 rounded-full shadow-[0_10px_25px_rgba(0,0,0,0.3)] font-bold text-xs flex items-center gap-2 border border-slate-700 transition-transform active:scale-95 whitespace-nowrap">🚨 Status & Emergency</button>

    <div id="bottomInfoCard" class="absolute bottom-0 left-0 w-full md:bottom-6 md:left-6 md:w-[420px] z-[9999] bg-white/95 backdrop-blur-md rounded-t-3xl md:rounded-2xl shadow-[0_-15px_40px_rgba(0,0,0,0.15)] md:shadow-2xl border-t md:border border-gray-200 transition-transform duration-300 transform translate-y-full md:translate-y-0 p-6 pt-8 md:pt-6">
        <div class="md:hidden absolute top-0 left-0 w-full h-10 flex items-center justify-center cursor-pointer" onclick="toggleMobileInfoCard()"><div class="w-12 h-1.5 bg-gray-300 rounded-full"></div></div>
        <div id="defaultMenuSection">
            <h2 class="text-sm font-black text-slate-900 tracking-tight uppercase">Active Weather Impact & Road Status</h2>
            <div class="grid grid-cols-3 gap-2 mt-3.5 text-center text-[10px] font-black uppercase text-white">
                <div class="bg-[#D32F2F] py-2 rounded-lg border border-red-700 shadow-xs">Critical</div>
                <div class="bg-[#EF6C00] py-2 rounded-lg border border-orange-700 shadow-xs">Moderate</div>
                <div class="bg-[#2E7D32] py-2 rounded-lg border border-green-700 shadow-xs">Low</div>
            </div>
            <div class="mt-4 border border-gray-100 rounded-xl bg-gray-50/50 overflow-hidden text-xs">
                <div class="bg-white px-4 py-2.5 border-b border-gray-100 font-bold text-slate-800 flex items-center gap-2"><img src="{{ asset('images/phone-call.png') }}" alt="Emergency" class="w-4 h-4 object-contain">Emergency & Information Contact</div>
                <div class="p-3.5 space-y-2 font-bold text-gray-500 text-[11px]">
                    <div>Hotline / Phone: <span class="text-slate-800 font-black">(046) 417-0727</span></div>
                    <div>Facebook Page: <span class="text-blue-600 font-black">facebook.com/CityGovtBacoor/</span></div>
                </div>
            </div>
        </div>
        <div id="telemetryStatusOverlayCard" class="hidden">
            <div class="flex flex-col gap-3">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 id="targetBarangayLabel" class="text-2xl font-black text-slate-900 uppercase tracking-tight">TALABA II</h3>
                        <div id="badgeAlertIndicatorBlock" class="inline-block mt-1 px-3 py-1 text-[10px] font-black text-white uppercase rounded shadow-xs">CRITICAL</div>
                    </div>
                    <button onclick="resetToBaselineView()" class="text-gray-400 hover:text-slate-600 font-bold text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded-md transition-colors cursor-pointer">Clear Map ✖</button>
                </div>
                <p id="evacuationInstructionLabelText" class="text-xs font-bold text-slate-600 leading-relaxed border-l-4 border-red-500 pl-3 bg-red-50/50 py-2.5 rounded-r-xl">Loading maps...</p>
            </div>
        </div>
    </div>

    <script>
        let map, routingControl = null, sensorMarker = null;
        const locationCoordinatesMatrix = { cdrrmo_hq: { lat: 14.4314, lng: 120.9463 }, Talaba_II: { lat: 14.4622, lng: 120.9415 }, Mambog_I: { lat: 14.4530, lng: 120.9490 }, Habay_I: { lat: 14.4485, lng: 120.9365 }, Molino_I: { lat: 14.4310, lng: 120.9520 }, NIOG_II_Bacoor_cavite: { lat: 14.4560, lng: 120.9450 } };
        const redIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        const orangeIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        const greenIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });

        document.addEventListener("DOMContentLoaded", function() {
            map = L.map('userLiveGpsMap', {zoomControl: false}).setView([locationCoordinatesMatrix.cdrrmo_hq.lat, locationCoordinatesMatrix.cdrrmo_hq.lng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.marker([locationCoordinatesMatrix.cdrrmo_hq.lat, locationCoordinatesMatrix.cdrrmo_hq.lng]).bindPopup("<b>CDRRMO Headquarters</b>").addTo(map);
            map.on('click', function() { toggleSearchDropdown(false); closeUserDropdown(); });
            initializeLiveHardwareStream();
        });

        function toggleMobileInfoCard() { const card = document.getElementById('bottomInfoCard'); const btn = document.getElementById('mobileInfoToggleBtn'); if (card.classList.contains('translate-y-full')) { card.classList.remove('translate-y-full'); btn.classList.add('hidden'); } else { card.classList.add('translate-y-full'); btn.classList.remove('hidden'); } }
        function forceOpenMobileInfoCard() { if(window.innerWidth < 768) { document.getElementById('bottomInfoCard').classList.remove('translate-y-full'); document.getElementById('mobileInfoToggleBtn').classList.add('hidden'); } }
        function toggleSearchDropdown(show) { const dropdown = document.getElementById('searchDropdownMenu'); if (show) { dropdown.classList.remove('hidden'); closeUserDropdown(); } else { setTimeout(() => dropdown.classList.add('hidden'), 200); } }
        function toggleUserDropdown(event) { event.stopPropagation(); document.getElementById('userProfileDropdownMenu').classList.toggle('hidden'); document.getElementById('searchDropdownMenu').classList.add('hidden'); }
        function closeUserDropdown() { const m = document.getElementById('userProfileDropdownMenu'); if (m) m.classList.add('hidden'); }

        function updateDropdownBadge(locationKey, level) {
            const badge = document.getElementById(`badge-${locationKey}`); if (!badge) return;
            if (level === 'CRITICAL') { badge.className = "text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-sm uppercase font-black"; badge.innerText = "CRITICAL"; }
            else if (level === 'MODERATE') { badge.className = "text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded-sm uppercase font-black"; badge.innerText = "MODERATE"; }
            else if (level === 'LOW') { badge.className = "text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded-sm uppercase font-black"; badge.innerText = "LOW"; }
            else { badge.className = "text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-sm uppercase font-black"; badge.innerText = "SAFE"; }
        }

        function simulateLocationSelect(locationKey, level = 'CRITICAL') {
            const targetCoords = locationCoordinatesMatrix[locationKey];
            document.getElementById('mapSearchInput').value = "Brgy. " + locationKey.replace(/_/g, ' ');
            updateDropdownBadge(locationKey, level);
            if (level === 'SAFE') { resetToBaselineView(); return; }
            if (sensorMarker) map.removeLayer(sensorMarker);
            let activeIcon = redIcon; if (level === 'MODERATE') activeIcon = orangeIcon; if (level === 'LOW') activeIcon = greenIcon;
            sensorMarker = L.marker([targetCoords.lat, targetCoords.lng], {icon: activeIcon}).bindPopup(`<b>${level} ALERT</b><br>ESP32 Node: ` + locationKey.replace(/_/g, ' ')).addTo(map).openPopup();
            calculateRouteTrail(locationCoordinatesMatrix.cdrrmo_hq, targetCoords, locationKey, level);
        }

        function calculateRouteTrail(origin, destination, key, level = 'CRITICAL') {
            if (routingControl) map.removeControl(routingControl);
            let routeColor = level === 'MODERATE' ? '#EF6C00' : (level === 'LOW' ? '#2E7D32' : '#D32F2F');
            routingControl = L.Routing.control({
                waypoints: [ L.latLng(origin.lat, origin.lng), L.latLng(destination.lat, destination.lng) ],
                lineOptions: { styles: [{color: routeColor, opacity: 0.9, weight: 7}] },
                createMarker: function() { return null; }, show: false, fitSelectedRoutes: true
            }).addTo(map);
            renderStatusHUDCard(key, level);
            if (typeof forceOpenMobileInfoCard === "function") forceOpenMobileInfoCard();
        }

        function renderStatusHUDCard(key, level) {
            document.getElementById('defaultMenuSection').classList.add('hidden');
            document.getElementById('telemetryStatusOverlayCard').classList.remove('hidden');
            document.getElementById('targetBarangayLabel').innerText = key.replace(/_/g, ' ').toUpperCase();
            const badgeBlock = document.getElementById('badgeAlertIndicatorBlock');
            const instructionText = document.getElementById('evacuationInstructionLabelText');
            if (level === 'CRITICAL') {
                badgeBlock.className = "inline-block mt-1 px-3 py-1 text-[10px] font-black text-white bg-[#D32F2F] border border-red-700 rounded shadow-2xs"; badgeBlock.innerText = "CRITICAL ALERT";
                instructionText.innerText = "Evacuate immediately! Marked tracking pathways unpassable due to flash floods.";
            } else if (level === 'MODERATE') {
                badgeBlock.className = "inline-block mt-1 px-3 py-1 text-[10px] font-black text-white bg-[#EF6C00] border border-orange-700 rounded shadow-2xs"; badgeBlock.innerText = "MODERATE ALERT";
                instructionText.innerText = "Be careful! Flooding reported on low-lying curbs. Avoid deep drainage channels.";
            } else {
                badgeBlock.className = "inline-block mt-1 px-3 py-1 text-[10px] font-black text-white bg-[#2E7D32] border border-green-700 rounded shadow-2xs"; badgeBlock.innerText = "LOW WARNING";
                instructionText.innerText = "Monitor rainfall patterns closely. Waterways running normally. Clear clearways.";
            }
        }

        function resetToBaselineView() {
            document.getElementById('defaultMenuSection').classList.remove('hidden'); document.getElementById('telemetryStatusOverlayCard').classList.add('hidden'); document.getElementById('mapSearchInput').value = "";
            if (routingControl) { map.removeControl(routingControl); routingControl = null; }
            if (sensorMarker) { map.removeLayer(sensorMarker); sensorMarker = null; }
            map.setView([locationCoordinatesMatrix.cdrrmo_hq.lat, locationCoordinatesMatrix.cdrrmo_hq.lng], 14);
            if(window.innerWidth < 768) toggleMobileInfoCard();
        }

        let lastKnownLocation = null, lastKnownLevel = null;

        function initializeLiveHardwareStream() {
            setInterval(() => {
                fetch('/api/v1/flood-events/latest')
                    .then(res => res.json())
                    .then(data => {
                        // 🌟 LISTEN FOR THE NEW WATER_LEVEL FIELD
                        if (data.water_level && data.water_level !== 'SAFE') {
                            let locationKey = data.location.replace('Brgy. ', '').replace(/ /g, '_');    
                            if (locationCoordinatesMatrix[locationKey]) {
                                if (lastKnownLocation !== locationKey || lastKnownLevel !== data.water_level) {
                                    lastKnownLocation = locationKey;
                                    lastKnownLevel = data.water_level;
                                    simulateLocationSelect(locationKey, data.water_level);
                                }
                            }
                        } else if (data.water_level === 'SAFE' && lastKnownLocation !== null) {
                            lastKnownLocation = null; lastKnownLevel = 'SAFE'; resetToBaselineView();
                        }
                    }).catch(e => console.log('Hardware offline.'));
            }, 4000);
        }
    </script>
</body>
</html>