<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDRRMO Bacoor - Flood Monitoring System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link class="w-4 h-4 object-contain brightness-0 invert" rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-[#EAEAEA] min-h-screen font-sans flex text-gray-800 relative overflow-x-hidden">

    <div id="sidebarOverlay" onclick="toggleSidebarMenu()" class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden"></div>

    <aside id="slidingSidebar" class="w-60 bg-[#0F2942] text-slate-300 flex flex-col fixed inset-y-0 left-0 z-50 shadow-xl shrink-0 -translate-x-full transition-transform duration-300 md:translate-x-0 md:relative">
        
        <div class="p-4 border-b border-slate-700/50 flex items-center justify-between bg-[#0B2035]">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gray-400 border border-gray-500 overflow-hidden flex items-center justify-center font-bold text-white">
                    👤
                </div>
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-tight">Admin</h3>
                    <p class="text-[10px] text-slate-400">Desktop - 1</p>
                </div>
            </div>
            <button onclick="toggleSidebarMenu()" class="text-slate-400 hover:text-white p-1 text-sm md:hidden focus:outline-none">
                ✖
            </button>
        </div>

        <nav class="flex-1 p-2 space-y-0.5 mt-4">
            <a href="#" class="flex items-center space-x-3 px-4 py-2.5 bg-[#1C3D5A] text-white rounded-md font-semibold text-xs tracking-wide">
                <img src="{{ asset('images/home.png') }}" class="w-4 h-4 object-contain brightness-0 invert" alt="Home">
                <span>Dashboard Overview</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-2.5 hover:bg-[#15334E] hover:text-white rounded-md font-medium text-xs text-slate-400 transition-all">
                <img src="{{ asset('images/users.png') }}" class="w-4 h-4 object-contain opacity-70" alt="Users">
                <span>Rescue Teams</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-2.5 hover:bg-[#15334E] hover:text-white rounded-md font-medium text-xs text-slate-400 transition-all">
                <img src="{{ asset('images/paper-plane.png') }}" class="w-4 h-4 object-contain opacity-70" alt="Messages">
                <span>Messages</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-2.5 hover:bg-[#15334E] hover:text-white rounded-md font-medium text-xs text-slate-400 transition-all">
                <img src="{{ asset('images/map.png') }}" class="w-4 h-4 object-contain opacity-70" alt="Map">
                <span>Incident Map</span>
            </a>
            <a href="#" class="flex items-center space-x-3 px-4 py-2.5 hover:bg-[#15334E] hover:text-white rounded-md font-medium text-xs text-slate-400 transition-all">
                <img src="{{ asset('images/log-file.png') }}" class="w-4 h-4 object-contain opacity-70" alt="Logs">
                <span>Emergency Logs</span>
            </a>
            <form method="POST" action="/logout" class="block mt-8">
                @csrf
                <button type="submit" class="w-full text-left flex items-center space-x-3 px-4 py-2.5 hover:bg-red-900/20 hover:text-red-400 rounded-md font-medium text-xs text-slate-400 transition-all">
                    <img src="{{ asset('images/leave.png') }}" class="w-4 h-4 object-contain opacity-70" alt="Log Out">
                    <span>Log out</span>
                </button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col min-h-screen w-full transition-all duration-300">
        
        <header class="bg-[#F8F9FA] h-20 flex items-center px-6 border-b border-gray-300 shadow-sm justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebarMenu()" class="md:hidden p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none shadow-2xs cursor-pointer">
                    <span class="text-lg block leading-none font-bold text-slate-700">☰</span>
                </button>
                
                <img src="{{ asset('images/logo.png') }}" alt="CDRRMO Bacoor Logo" class="w-12 h-12 object-contain drop-shadow-md">
                
                <div>
                    <h1 class="text-md font-black text-slate-900 uppercase tracking-tight">CDRRMO Bacoor</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Risk Reduction Management</p>
                </div>
            </div>
            
            <div class="hidden sm:block text-[11px] font-bold text-gray-400 tracking-wider uppercase bg-white px-3 py-1.5 rounded-md border border-gray-200 shadow-2xs">
                System Workspace Monitoring
            </div>
        </header>

        <div class="p-4 md:p-6 grid grid-cols-12 gap-6 items-start w-full max-w-[1700px] mx-auto">
            
            <div class="col-span-12 lg:col-span-8 space-y-6">
                
                <div id="telemetryBanner" class="bg-[#D32F2F] text-white rounded-xl shadow-md border border-red-700 p-5 flex flex-col justify-center relative overflow-hidden min-h-[110px] transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight">Alert Level</h2>
                            <h3 id="telemetryStateText" class="text-md font-bold tracking-wide mt-0.5 opacity-95">LEVEL 1: MONITORING</h3>
                        </div>
                        <div class="text-right text-xs bg-red-800/50 font-bold uppercase px-3 py-1 rounded-md border border-red-600/50">
                            Telemetry Active
                        </div>
                    </div>
                    <ul class="text-[11px] font-semibold opacity-90 mt-3 space-y-1 list-disc list-inside border-t border-red-600/40 pt-2">
                        <li>Continuous rainfall monitoring initialized</li>
                        <li>Standby rescue teams alerted for deployment paths</li>
                        <li>Public advisory notices broadcast updates active</li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-300 overflow-hidden relative z-0">
                    <div id="map" class="h-[380px] w-full bg-slate-200"></div>
                </div>

                <div class="bg-[#C1D045] rounded-xl shadow-md border border-slate-400/60 overflow-hidden">
                    <div class="bg-[#AEBD39] px-5 py-2.5 border-b border-slate-400/50 flex justify-between items-center">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Recent Rescue Calls</h4>
                        <span class="text-[10px] bg-slate-900/10 font-bold px-2 py-0.5 rounded text-slate-800">Live Queue</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900/10 border-b border-slate-400/40 text-[10px] font-bold text-slate-800 uppercase tracking-wider">
                                    <th class="px-6 py-3 border-r border-slate-400/30">Time</th>
                                    <th class="px-6 py-3 border-r border-slate-400/30">Location</th>
                                    <th class="px-6 py-3 border-r border-slate-400/30">Incident Type</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="telemetryEventTargetRows" class="font-bold text-slate-900 divide-y divide-slate-400/20">
                                <tr class="hover:bg-slate-400/10 transition-colors">
                                    <td class="px-6 py-2.5 border-r border-slate-400/20">--:--</td>
                                    <td class="px-6 py-2.5 border-r border-slate-400/20">Loading Data...</td>
                                    <td class="px-6 py-2.5 border-r border-slate-400/20">--</td>
                                    <td class="px-6 py-2.5">--</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="col-span-12 lg:col-span-4 space-y-6">
                
                <div class="bg-white rounded-xl shadow-md border border-gray-300 p-5 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-2xl text-blue-600">👥</span>
                        <div>
                            <p class="text-2xl font-black tracking-tight text-slate-900">{{ $totalUsers ?? 12 }}</p>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Total User</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-300 p-5 space-y-3">
                    <div class="border-b border-gray-200 pb-2 flex items-center space-x-2">
                        <span class="text-xs">📣</span>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Active Messages</h4>
                    </div>
                    <div id="activeMessagesContainer" class="space-y-2 max-h-[220px] overflow-y-auto pr-1 text-[11px] font-bold text-gray-700 divide-y divide-gray-100">
                        <div class="pt-2"><p class="text-slate-500 text-[10px] mb-0.5">--:--</p> Syncing operational logs feed...</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-300 p-5 space-y-4">
                    <div class="border-b border-gray-200 pb-2">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Flood Waning Indicator</h4>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-3 bg-slate-50 rounded-lg border border-gray-200">
                            <div id="lowGauge" class="w-12 h-12 rounded-full border-4 border-blue-500 flex items-center justify-center mx-auto text-xs font-black text-blue-700 transition-all duration-500">--%</div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-2">Low</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg border border-gray-200">
                            <div id="modGauge" class="w-12 h-12 rounded-full border-4 border-amber-400 flex items-center justify-center mx-auto text-xs font-black text-amber-700 transition-all duration-500">--%</div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-2">Moderate</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg border border-gray-200">
                            <div id="critGauge" class="w-12 h-12 rounded-full border-4 border-red-500 flex items-center justify-center mx-auto text-xs font-black text-red-700 transition-all duration-500">--%</div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-2">Critical</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function toggleSidebarMenu() {
            const sidebar = document.getElementById('slidingSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.add('opacity-100'), 10);
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        let mapInstance;
        let nodeMarkers = {}; 

        const locationCoordinatesMatrix = {
            Talaba_II: { lat: 14.4622, lng: 120.9415 },
            Mambog_I:  { lat: 14.4530, lng: 120.9490 },
            Habay_I:   { lat: 14.4485, lng: 120.9365 },
            Molino_I:  { lat: 14.4310, lng: 120.9520 },
            NIOG_II_Bacoor_cavite: { lat: 14.4560, lng: 120.9450 } 
        };

        const blueIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        const greenIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        const orangeIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        const redIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });

        document.addEventListener("DOMContentLoaded", function() {
            mapInstance = L.map('map').setView([14.4480, 120.9450], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapInstance);

            for (const [key, coords] of Object.entries(locationCoordinatesMatrix)) {
                let displayName = "Brgy. " + key.replace(/_/g, ' ');
                nodeMarkers[key] = L.marker([coords.lat, coords.lng], { icon: blueIcon })
                    .bindPopup(`<b>Monitoring Node</b><br>${displayName}`)
                    .addTo(mapInstance);
            }

            synchronizeTelemetryState();
            setInterval(synchronizeTelemetryState, 4000);
        });

        function synchronizeTelemetryState() {
            fetch('/api/v1/flood-events/latest')
                .then(response => response.json())
                .then(data => {
                    const banner = document.getElementById('telemetryBanner');
                    const stateText = document.getElementById('telemetryStateText');
                    const rowTarget = document.getElementById('telemetryEventTargetRows');
                    const activeMessages = document.getElementById('activeMessagesContainer');
                    
                    const logTime = data.updated_at ? new Date(data.updated_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const displayLocation = data.location ? data.location : 'Brgy. Talaba II';
                    
                    // 🌟 CASE-INSENSITIVE SMART LOOKUP
                    let rawLocation = displayLocation;
                    let cleanInput = rawLocation.replace(/^brgy\.\s*/i, '').replace(/ /g, '_').toLowerCase();
                    let locationKey = Object.keys(locationCoordinatesMatrix).find(k => k.toLowerCase() === cleanInput) || 'Talaba_II';

                    // Reset all markers to Blue first to clear old alerts
                    for (const key in nodeMarkers) {
                        nodeMarkers[key].setIcon(blueIcon);
                        nodeMarkers[key].closePopup();
                    }

                    if (data.water_level === 'CRITICAL') {
                        banner.className = "bg-[#D32F2F] text-white rounded-xl shadow-md border border-red-700 p-5 flex flex-col justify-center relative overflow-hidden min-h-[110px] transition-colors";
                        stateText.innerHTML = `LEVEL 3: CRITICAL EMERGENCY (ALERT ACTIVE IN ${displayLocation.toUpperCase()})`;
                        
                        rowTarget.innerHTML = `
                            <tr class="hover:bg-slate-400/10 transition-colors bg-red-500/10">
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${logTime}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${displayLocation}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20 text-red-600 font-bold">Severe Flooding</td>
                                <td class="px-6 py-2.5 text-red-600 font-black animate-pulse">CRITICAL Alert</td>
                            </tr>
                        `;
                        activeMessages.innerHTML = `<div class="pt-2"><p class="text-red-500 text-[10px] mb-0.5">${logTime} Monitored</p> Critical flooding at ${displayLocation}! Dispatching rescue teams immediately.</div>`;

                        if (nodeMarkers[locationKey]) {
                            nodeMarkers[locationKey].setIcon(redIcon);
                            nodeMarkers[locationKey].setPopupContent(`<b style="color:red;">🚨 CRITICAL FLOOD 🚨</b><br>${displayLocation}`).openPopup();
                            mapInstance.panTo([locationCoordinatesMatrix[locationKey].lat, locationCoordinatesMatrix[locationKey].lng]);
                        }
                        updateWaningIndicators(10, 15, 75);

                    } else if (data.water_level === 'MODERATE') {
                        banner.className = "bg-[#EF6C00] text-white rounded-xl shadow-md border border-orange-700 p-5 flex flex-col justify-center relative overflow-hidden min-h-[110px] transition-colors";
                        stateText.innerHTML = `LEVEL 2: MODERATE WARNING (ALERT ACTIVE IN ${displayLocation.toUpperCase()})`;
                        
                        rowTarget.innerHTML = `
                            <tr class="hover:bg-slate-400/10 transition-colors bg-orange-500/10">
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${logTime}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${displayLocation}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20 text-orange-600 font-bold">Rising Water</td>
                                <td class="px-6 py-2.5 text-orange-600 font-bold">MODERATE Alert</td>
                            </tr>
                        `;
                        activeMessages.innerHTML = `<div class="pt-2"><p class="text-orange-500 text-[10px] mb-0.5">${logTime} Monitored</p> Moderate flooding at ${displayLocation}. Prepare for potential evacuation.</div>`;

                        if (nodeMarkers[locationKey]) {
                            nodeMarkers[locationKey].setIcon(orangeIcon);
                            nodeMarkers[locationKey].setPopupContent(`<b style="color:orange;">⚠️ MODERATE FLOOD ⚠️</b><br>${displayLocation}`).openPopup();
                            mapInstance.panTo([locationCoordinatesMatrix[locationKey].lat, locationCoordinatesMatrix[locationKey].lng]);
                        }
                        updateWaningIndicators(20, 60, 20);

                    } else if (data.water_level === 'LOW') {
                        banner.className = "bg-[#F57F17] text-white rounded-xl shadow-md border border-yellow-700 p-5 flex flex-col justify-center relative overflow-hidden min-h-[110px] transition-colors";
                        stateText.innerHTML = `LEVEL 1: LOW ADVISORY (ALERT ACTIVE IN ${displayLocation.toUpperCase()})`;
                        
                        rowTarget.innerHTML = `
                            <tr class="hover:bg-slate-400/10 transition-colors bg-yellow-500/10">
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${logTime}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${displayLocation}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20 text-yellow-600 font-bold">Gutter Deep</td>
                                <td class="px-6 py-2.5 text-yellow-600 font-bold">LOW Alert</td>
                            </tr>
                        `;
                        activeMessages.innerHTML = `<div class="pt-2"><p class="text-yellow-500 text-[10px] mb-0.5">${logTime} Monitored</p> Low water accumulation at ${displayLocation}. Monitoring situation.</div>`;

                        if (nodeMarkers[locationKey]) {
                            nodeMarkers[locationKey].setIcon(greenIcon);
                            nodeMarkers[locationKey].setPopupContent(`<b style="color:green;">🟢 LOW FLOOD 🟢</b><br>${displayLocation}`).openPopup();
                            mapInstance.panTo([locationCoordinatesMatrix[locationKey].lat, locationCoordinatesMatrix[locationKey].lng]);
                        }
                        updateWaningIndicators(60, 30, 10);

                    } else {
                        // NORMAL / SAFE
                        banner.className = "bg-[#2E7D32] text-white rounded-xl shadow-md border border-green-700 p-5 flex flex-col justify-center relative overflow-hidden min-h-[110px] transition-colors";
                        stateText.innerHTML = "LEVEL 0: NO FLOODING DETECTED (SYSTEM SECURE)";
                        
                        rowTarget.innerHTML = `
                            <tr class="hover:bg-slate-400/10 transition-colors">
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${logTime}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20">${displayLocation}</td>
                                <td class="px-6 py-2.5 border-r border-slate-400/20">None</td>
                                <td class="px-6 py-2.5 text-green-600 font-bold">Receded / Safe</td>
                            </tr>
                        `;
                        activeMessages.innerHTML = `<div class="pt-2"><p class="text-green-500 text-[10px] mb-0.5">${logTime} Monitored</p> Water level normal at ${displayLocation}. Status stable.</div>`;

                        updateWaningIndicators(90, 10, 0);
                    }
                })
                .catch(err => console.log('Data synchronization skipped.'));
        }

        function updateWaningIndicators(low, mod, crit) {
            document.getElementById('lowGauge').innerText = low + '%';
            document.getElementById('modGauge').innerText = mod + '%';
            document.getElementById('critGauge').innerText = crit + '%';
        }
    </script>
</body>
</html>