<!-- Desktop Unique Car Rental Theme Footer -->
<footer class="hidden sm:block bg-slate-950 text-slate-300 border-t border-slate-800/80 relative overflow-hidden transition-colors duration-300 selection:bg-emerald-500 selection:text-white">

    <!-- 3. Main Footer Cockpit Columns -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-12 gap-8">
            <!-- Brand Column (5 cols) -->
            <div class="col-span-4 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'OneDrive') }}" class="h-12 w-auto object-contain drop-shadow-md" />
                    <div>
                        <span class="block font-black text-lg text-white tracking-tight leading-none">ONEDRIVE</span>
                        <span class="text-[11px] font-semibold text-emerald-400 tracking-widest uppercase mt-0.5 block">Car Booking & Fleet Systems</span>
                    </div>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed pr-4">
                    Mindanao's dependable car rental and fleet dispatch platform. Offering self-drive and chauffeured rentals with seamless booking workflows, transparent destination pricing, and verified fleet security.
                </p>

                <div class="pt-2 flex items-center gap-3 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-mono flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        FLEET ACTIVE
                    </span>
                    <span class="text-slate-500">|</span>
                    <span class="text-slate-400 font-medium">Butuan City, Davao City · CDO · Gensan</span>
                </div>
            </div>

            <!-- Fleet Classes Column (2 cols) -->
            <div class="col-span-2 space-y-3">
                <h4 class="text-xs font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 9l2-4h14l2 4M3 9v7a1 1 0 001 1h1m16-8v7a1 1 0 01-1 1h-1M3 9h18"/></svg>
                    Vehicle Fleet
                </h4>
                <ul class="space-y-2 text-xs text-slate-400">
                    <li><a href="{{ route('vehicles.index') }}" class="hover:text-emerald-400 transition flex items-center justify-between"><span>Sedans & City Cars</span> <span class="text-[10px] text-slate-600 font-mono">ECO</span></a></li>
                    <li><a href="{{ route('vehicles.index') }}" class="hover:text-emerald-400 transition flex items-center justify-between"><span>SUVs & Crossovers</span> <span class="text-[10px] text-slate-600 font-mono">7-SEAT</span></a></li>
                    <li><a href="{{ route('vehicles.index') }}" class="hover:text-emerald-400 transition flex items-center justify-between"><span>Passenger Vans</span> <span class="text-[10px] text-slate-600 font-mono">HIACE</span></a></li>
                    <li><a href="{{ route('vehicles.index') }}" class="hover:text-emerald-400 transition flex items-center justify-between"><span>4x4 Pickups</span> <span class="text-[10px] text-slate-600 font-mono">DIESEL</span></a></li>
                    <li><a href="{{ route('vehicles.index') }}" class="text-emerald-400 hover:underline font-semibold text-[11px] pt-1 inline-block">Browse All Inventory &rarr;</a></li>
                </ul>
            </div>

            <!-- Rental Operations Column (3 cols) -->
            <div class="col-span-3 space-y-3">
                <h4 class="text-xs font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Trip Services & Rates
                </h4>
                <ul class="space-y-2 text-xs text-slate-400">
                    <li><a href="{{ route('destinations.index') }}" class="hover:text-emerald-400 transition">Destination Rates & Calculator</a></li>
                    <li><a href="{{ route('calendar.index') }}" class="hover:text-emerald-400 transition">Trip Schedule & Dispatch Calendar</a></li>
                    <li><a href="{{ route('bookings.index') }}" class="hover:text-emerald-400 transition">Active Booking Manager</a></li>
                    <li><a href="{{ route('dashboard') }}" class="hover:text-emerald-400 transition">Fleet Telemetry & Revenue</a></li>
                    <li><span class="text-[11px] text-slate-500">Cross-border travel permits ready upon booking</span></li>
                </ul>
            </div>

            <!-- Emergency Roadside & Hub Column (3 cols) -->
            <div class="col-span-3 space-y-3">
                <h4 class="text-xs font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    24/7 Roadside Assistance
                </h4>
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        <span class="text-slate-400 text-[11px]">Emergency Hotline:</span>
                    </div>
                    <p class="font-mono text-emerald-400 font-extrabold text-sm tracking-wide">(082) 285-DRIVE</p>
                    <p class="text-[11px] text-slate-400 font-mono">Mobile: +63 917 800 6637</p>
                    <div class="pt-1 text-[10px] text-slate-500 border-t border-slate-800">
                        Dispatch Center: Butuan City, Agusan del Norte, Mindanao
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Transmission / Gear Selector & Dashboard Bottom Strip -->
    <div class="bg-black/90 border-t border-slate-800/80 py-4 px-4 sm:px-6 lg:px-8 text-xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 text-slate-500">
            <!-- Left: Gearbox / Drive Mode Badge -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 font-mono text-[10px] px-2 py-0.5 rounded bg-slate-900 border border-slate-800">
                    <span class="text-slate-600">P</span>
                    <span class="text-slate-600">R</span>
                    <span class="text-slate-600">N</span>
                    <span class="text-emerald-400 font-bold bg-emerald-950 px-1 rounded border border-emerald-500/40">D</span>
                    <span class="text-slate-500 ml-1">CRUISE MODE</span>
                </div>
                <span>&copy; {{ date('Y') }} ONEDRIVE Car Rental System. All rights reserved.</span>
            </div>

            <!-- Right: Rental Badges & Policies -->
            <div class="flex items-center gap-4 text-[11px] text-slate-400">
                <span class="flex items-center gap-1 text-slate-500">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mindanao Fleet Certified
                </span>
                <span>&bull;</span>
                <span class="text-slate-400">GPS Telematics Active</span>
                <span>&bull;</span>
                <span class="text-slate-400">Full Collision Waiver</span>
            </div>
        </div>
    </div>
</footer>
