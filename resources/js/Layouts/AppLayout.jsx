import React, { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
  LayoutDashboard,
  Car,
  Calendar,
  Grid,
  MapPin,
  Users,
  User,
  LogOut,
  ChevronDown,
  Sun,
  Moon,
  Plus,
  CheckCircle2,
  AlertCircle,
  ShieldCheck,
  Home,
  PhoneCall,
  DollarSign
} from 'lucide-react';

import confirmDialog from '@/Utils/confirm';

export default function AppLayout({ children, title, header }) {
  const { props, url } = usePage();
  const auth = props?.auth || {};
  const flash = props?.flash || {};
  const user = auth?.user;
  const currentRoute = url || window.location.pathname;

  const [darkMode, setDarkMode] = useState(false);
  const [myAccountOpen, setMyAccountOpen] = useState(false);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const isDark = document.documentElement.classList.contains('dark');
    setDarkMode(isDark);

    const removeStart = router.on('start', () => setLoading(true));
    const removeFinish = router.on('finish', () => setLoading(false));

    return () => {
      removeStart();
      removeFinish();
    };
  }, []);

  const toggleTheme = () => {
    const nextDark = !darkMode;
    setDarkMode(nextDark);
    if (nextDark) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  };

  const isMyAccountActive = /^\/(vehicles|bookings|calendar|income)/.test(currentRoute) && !currentRoute.includes('all-listing');

  return (
    <div className="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300 font-sans antialiased selection:bg-emerald-500 selection:text-white">
      {/* Green Accent Top Bar */}
      <div className="h-1 bg-gradient-to-r from-emerald-500 via-green-400 to-teal-500" />

      {/* Mobile-only Centered App Loader */}
      {loading && (
        <div className="fixed inset-0 z-[200] md:hidden flex flex-col items-center justify-center bg-black/50 dark:bg-black/70 backdrop-blur-xs animate-in fade-in duration-150">
          <div className="relative flex flex-col items-center justify-center p-6 rounded-3xl bg-white/95 dark:bg-gray-900/95 shadow-2xl border border-gray-100 dark:border-gray-800">
            {/* Spinning Circle Animation Container */}
            <div className="relative flex items-center justify-center w-32 h-32">
              {/* Outer Spinning Loader Circle */}
              <div className="absolute inset-0 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 border-r-emerald-400 animate-spin" />

              {/* Inner Circled Container with Centered Logo */}
              <div className="w-26 h-26 rounded-full bg-white dark:bg-gray-950 p-3 flex items-center justify-center shadow-inner border border-gray-100 dark:border-gray-800/80 overflow-hidden">
                <img
                  src="/images/logo-full.png"
                  alt="Loading..."
                  className="w-full h-auto max-h-16 object-contain drop-shadow-sm dark:brightness-110 animate-pulse"
                />
              </div>
            </div>

            {/* Loading Badge */}
            <span className="mt-4 text-[10px] font-black text-emerald-600 dark:text-emerald-400 tracking-widest uppercase bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 px-3.5 py-1 rounded-full shadow-xs">
              Loading...
            </span>
          </div>
        </div>
      )}

      {/* Flash Messages */}
      {flash?.success && (
        <div className="fixed top-4 right-4 left-4 md:left-auto z-[150] max-w-md bg-emerald-500/15 border border-emerald-500/40 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-2xl shadow-2xl backdrop-blur-xl flex items-center gap-3 animate-in fade-in duration-200">
          <CheckCircle2 className="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
          <span className="text-xs font-extrabold">{flash.success}</span>
        </div>
      )}
      {flash?.error && (
        <div className="fixed top-4 right-4 left-4 md:left-auto z-[150] max-w-md bg-rose-500/15 border border-rose-500/40 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-2xl shadow-2xl backdrop-blur-xl flex items-center gap-3 animate-in fade-in duration-200">
          <AlertCircle className="w-5 h-5 shrink-0 text-rose-600 dark:text-rose-400" />
          <span className="text-xs font-extrabold">{flash.error}</span>
        </div>
      )}

      {/* Desktop Navigation Header (md: and above) */}
      <nav className="sticky top-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200/80 dark:border-gray-800 shadow-xs transition-colors duration-300 hidden md:block">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-[4.5rem]">
            <div className="flex items-center gap-2">
              {/* Desktop Logo */}
              <Link href="/dashboard" className="flex items-center gap-2 group mr-3">
                <img
                  src="/images/logo-full.png"
                  alt="ONEDRIVE WHEELS"
                  className="h-14 sm:h-16 w-auto max-w-[240px] object-contain drop-shadow-xs dark:brightness-110 group-hover:scale-105 transition-all duration-200"
                />
              </Link>

              <div className="hidden lg:block w-px h-8 bg-gray-200 dark:bg-gray-800 mx-2" />

              {/* Navigation Links */}
              <div className="flex items-center gap-1">
                <Link
                  href="/dashboard"
                  className={`px-3 py-2 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-colors ${currentRoute === '/dashboard'
                    ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60'
                    : 'text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                    }`}
                >
                  <LayoutDashboard className="w-4 h-4" />
                  Dashboard
                </Link>

                {/* My Account Dropdown */}
                <div className="relative">
                  <button
                    onClick={() => {
                      setMyAccountOpen(!myAccountOpen);
                      setUserMenuOpen(false);
                    }}
                    className={`px-3 py-2 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-colors ${isMyAccountActive
                      ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60'
                      : 'text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                      }`}
                  >
                    <User className="w-4 h-4" />
                    <span>My Account</span>
                    <ChevronDown className="w-3.5 h-3.5 opacity-70" />
                  </button>

                  {myAccountOpen && (
                    <div
                      className="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl py-2 z-[100] animate-in fade-in slide-in-from-top-2 duration-150"
                      onClick={() => setMyAccountOpen(false)}
                    >
                      <div className="px-3 py-2 border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/50">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Host & Operations Hub</p>
                      </div>

                      <Link
                        href="/vehicles"
                        className="flex items-center gap-2.5 px-3 py-2.5 hover:bg-emerald-50/80 dark:hover:bg-emerald-950/50 text-gray-800 dark:text-gray-200 transition-colors"
                      >
                        <div className="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                          <Car className="w-4 h-4" />
                        </div>
                        <div>
                          <div className="font-bold text-xs">My Fleet</div>
                          <div className="text-[10px] text-gray-400">Manage vehicles</div>
                        </div>
                      </Link>

                      <Link
                        href="/bookings"
                        className="flex items-center gap-2.5 px-3 py-2.5 hover:bg-emerald-50/80 dark:hover:bg-emerald-950/50 text-gray-800 dark:text-gray-200 transition-colors"
                      >
                        <div className="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                          <Calendar className="w-4 h-4" />
                        </div>
                        <div>
                          <div className="font-bold text-xs">My Bookings</div>
                          <div className="text-[10px] text-gray-400">Rental reservations</div>
                        </div>
                      </Link>

                      <Link
                        href="/calendar"
                        className="flex items-center gap-2.5 px-3 py-2.5 hover:bg-emerald-50/80 dark:hover:bg-emerald-950/50 text-gray-800 dark:text-gray-200 transition-colors"
                      >
                        <div className="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                          <Calendar className="w-4 h-4" />
                        </div>
                        <div>
                          <div className="font-bold text-xs">My Calendar</div>
                          <div className="text-[10px] text-gray-400">Schedule & dispatch</div>
                        </div>
                      </Link>

                      <Link
                        href="/income"
                        className="flex items-center gap-2.5 px-3 py-2.5 hover:bg-emerald-50/80 dark:hover:bg-emerald-950/50 text-gray-800 dark:text-gray-200 transition-colors"
                      >
                        <div className="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                          <DollarSign className="w-4 h-4" />
                        </div>
                        <div>
                          <div className="font-bold text-xs">My Income</div>
                          <div className="text-[10px] text-gray-400">Earnings & revenue</div>
                        </div>
                      </Link>
                    </div>
                  )}
                </div>

                <Link
                  href="/vehicles/all-listing"
                  className={`px-3 py-2 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-colors ${currentRoute === '/vehicles/all-listing'
                    ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60'
                    : 'text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                    }`}
                >
                  <Grid className="w-4 h-4" />
                  All Listing
                </Link>

                {user?.is_admin && (
                  <>
                    <Link
                      href="/destinations"
                      className={`px-3 py-2 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-colors ${currentRoute.includes('/destinations')
                        ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60'
                        : 'text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                        }`}
                    >
                      <MapPin className="w-4 h-4" />
                      Rates
                    </Link>

                    <Link
                      href="/admin/users"
                      className={`px-3 py-2 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-colors ${currentRoute.includes('/admin/users')
                        ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60'
                        : 'text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-gray-100 dark:hover:bg-gray-800'
                        }`}
                    >
                      <Users className="w-4 h-4" />
                      Users
                    </Link>
                  </>
                )}
              </div>
            </div>

            {/* Right Tools */}
            <div className="flex items-center gap-3">
              <button
                onClick={toggleTheme}
                type="button"
                className="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"
                title="Toggle Dark Mode"
              >
                {darkMode ? <Sun className="w-4 h-4 text-amber-400" /> : <Moon className="w-4 h-4 text-gray-600" />}
              </button>

              <div className="relative">
                <button
                  onClick={() => {
                    setUserMenuOpen(!userMenuOpen);
                    setMyAccountOpen(false);
                  }}
                  className="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors"
                >
                  <div className="w-7 h-7 rounded-full overflow-hidden ring-1 ring-emerald-500/30 flex items-center justify-center bg-gray-100 dark:bg-gray-800 shrink-0">
                    <img
                      src={
                        user?.avatar_url ||
                        (user?.avatar_path
                          ? user.avatar_path.startsWith('http')
                            ? user.avatar_path
                            : `/${user.avatar_path}`
                          : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&background=059669&color=ffffff&bold=true`)
                      }
                      alt={user?.name || 'Avatar'}
                      className="w-full h-full object-cover"
                    />
                  </div>
                  <span className="font-extrabold text-xs text-gray-800 dark:text-gray-200">{user?.name}</span>
                  <ChevronDown className="w-3.5 h-3.5 text-gray-400" />
                </button>

                {userMenuOpen && (
                  <div
                    className="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                    onClick={() => setUserMenuOpen(false)}
                  >
                    <div className="px-3 py-2 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2.5">
                      <div className="w-8 h-8 rounded-full overflow-hidden ring-1 ring-emerald-500/30 flex items-center justify-center bg-gray-100 dark:bg-gray-800 shrink-0">
                        <img
                          src={
                            user?.avatar_url ||
                            (user?.avatar_path
                              ? user.avatar_path.startsWith('http')
                                ? user.avatar_path
                                : `/${user.avatar_path}`
                              : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&background=059669&color=ffffff&bold=true`)
                          }
                          alt={user?.name || 'Avatar'}
                          className="w-full h-full object-cover"
                        />
                      </div>
                      <div className="min-w-0">
                        <p className="text-[10px] font-bold text-gray-400 uppercase">Signed in as</p>
                        <p className="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{user?.name}</p>
                      </div>
                    </div>

                    <Link
                      href="/profile"
                      className="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    >
                      <User className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                      Profile Settings
                    </Link>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </nav>

      {/* Opaque Fixed Top Header (< 768px / Mobile App Shell) */}
      <header className="fixed top-0 left-0 right-0 z-50 md:hidden bg-white dark:bg-gray-900 pt-[env(safe-area-inset-top)] border-b border-gray-200/80 dark:border-gray-800 px-3.5 py-2.5 transition-colors duration-300 shadow-xs">
        <div className="flex items-center justify-between gap-2">
          {/* Mobile Logo */}
          <Link href="/menu" className="flex items-center gap-2.5 shrink-0 group active:scale-95 transition-transform duration-150 min-w-0">
            <img
              src="/images/logo.png"
              alt="ONEDRIVE WHEELS"
              className="h-10 sm:h-11 w-auto object-contain drop-shadow-xs dark:brightness-110 group-hover:scale-105 transition-transform duration-200"
            />
            <div className="flex flex-col justify-center">
              <span className="font-black text-sm text-gray-900 dark:text-white tracking-tight leading-none">ONEDRIVE</span>
              <span className="text-[9px] font-extrabold text-emerald-600 dark:text-emerald-400 tracking-wider uppercase leading-none mt-0.5">WHEELS</span>
            </div>
          </Link>

          {/* Right Mobile Actions */}
          <div className="flex items-center gap-1.5 shrink-0">
            <button
              onClick={toggleTheme}
              type="button"
              className="w-8 h-8 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-300 bg-gray-100/80 dark:bg-gray-800/80 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-emerald-600 dark:hover:text-emerald-400 active:scale-90 transition-all duration-150 border border-gray-200/80 dark:border-gray-700/80"
              title="Toggle Dark Mode"
            >
              {darkMode ? <Sun className="w-4 h-4 text-amber-400" /> : <Moon className="w-4 h-4 text-gray-600" />}
            </button>

            <Link
              href="/menu"
              className={`inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-xl border active:scale-90 transition-all duration-150 shadow-xs ${currentRoute === '/menu'
                ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/70 border-emerald-300/80 dark:border-emerald-800/80'
                : 'text-gray-700 dark:text-gray-300 bg-gray-100/80 dark:bg-gray-800/80 hover:bg-gray-200 dark:hover:bg-gray-700 border-gray-200/80 dark:border-gray-700/80'
                }`}
            >
              <Home className="w-3.5 h-3.5" />
              <span>Menu</span>
            </Link>
          </div>
        </div>
      </header>

      {/* Main Content Area: padded on mobile (pt-16 pb-20 md:p-0) to prevent scrolling content from being hidden underneath fixed bars */}
      <main className="flex-1 pt-16 pb-20 md:p-0 max-w-7xl w-full mx-auto px-3.5 sm:px-6 lg:px-8">
        {children}
      </main>

      {/* Desktop Footer (md: and above) */}
      <footer className="hidden md:block bg-slate-950 text-slate-300 border-t border-slate-800/80 relative overflow-hidden transition-colors duration-300 selection:bg-emerald-500 selection:text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
          <div className="grid grid-cols-12 gap-8">
            {/* Brand Column */}
            <div className="col-span-4 space-y-4">
              <div className="flex items-center gap-3">
                <img src="/images/logo.png" alt="ONEDRIVE WHEELS" className="h-12 w-auto object-contain drop-shadow-md" />
                <div>
                  <span className="block font-black text-lg text-white tracking-tight leading-none">ONEDRIVE</span>
                  <span className="text-[11px] font-semibold text-emerald-400 tracking-widest uppercase mt-0.5 block">Car Booking & Fleet Systems</span>
                </div>
              </div>

              <p className="text-xs text-slate-400 leading-relaxed pr-4">
                Mindanao's dependable car rental and fleet dispatch platform. Offering self-drive and chauffeured rentals with seamless booking workflows, transparent destination pricing, and verified fleet security.
              </p>

              <div className="pt-2 flex items-center gap-3 text-xs">
                <span className="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-mono flex items-center gap-1.5">
                  <span className="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                  FLEET ACTIVE
                </span>
                <span className="text-slate-500">|</span>
                <span className="text-slate-400 font-medium">Butuan City, Davao City &bull; CDO &bull; Gensan</span>
              </div>
            </div>

            {/* Fleet Classes Column */}
            <div className="col-span-2 space-y-3">
              <h4 className="text-xs font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                <Car className="w-3.5 h-3.5 text-emerald-400" />
                Vehicle Fleet
              </h4>
              <ul className="space-y-2 text-xs text-slate-400">
                <li>
                  <Link href="/vehicles" className="hover:text-emerald-400 transition flex items-center justify-between">
                    <span>Sedans & City Cars</span> <span className="text-[10px] text-slate-600 font-mono">ECO</span>
                  </Link>
                </li>
                <li>
                  <Link href="/vehicles" className="hover:text-emerald-400 transition flex items-center justify-between">
                    <span>SUVs & Crossovers</span> <span className="text-[10px] text-slate-600 font-mono">7-SEAT</span>
                  </Link>
                </li>
                <li>
                  <Link href="/vehicles" className="hover:text-emerald-400 transition flex items-center justify-between">
                    <span>Passenger Vans</span> <span className="text-[10px] text-slate-600 font-mono">HIACE</span>
                  </Link>
                </li>
                <li>
                  <Link href="/vehicles" className="hover:text-emerald-400 transition flex items-center justify-between">
                    <span>4x4 Pickups</span> <span className="text-[10px] text-slate-600 font-mono">DIESEL</span>
                  </Link>
                </li>
                <li>
                  <Link href="/vehicles/all-listing" className="text-emerald-400 hover:underline font-semibold text-[11px] pt-1 inline-block">
                    Browse All Inventory &rarr;
                  </Link>
                </li>
              </ul>
            </div>

            {/* Rental Operations Column */}
            <div className="col-span-3 space-y-3">
              <h4 className="text-xs font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                <Calendar className="w-3.5 h-3.5 text-emerald-400" />
                Trip Services & Rates
              </h4>
              <ul className="space-y-2 text-xs text-slate-400">
                <li><Link href="/destinations" className="hover:text-emerald-400 transition">Destination Rates & Calculator</Link></li>
                <li><Link href="/calendar" className="hover:text-emerald-400 transition">Trip Schedule & Dispatch Calendar</Link></li>
                <li><Link href="/bookings" className="hover:text-emerald-400 transition">Active Booking Manager</Link></li>
                <li><Link href="/dashboard" className="hover:text-emerald-400 transition">Fleet Telemetry & Revenue</Link></li>
                <li><span className="text-[11px] text-slate-500">Cross-border travel permits ready upon booking</span></li>
              </ul>
            </div>

            {/* Emergency Roadside & Hub Column */}
            <div className="col-span-3 space-y-3">
              <h4 className="text-xs font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                <PhoneCall className="w-3.5 h-3.5 text-rose-400" />
                24/7 Roadside Assistance
              </h4>
              <div className="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-2 text-xs">
                <div className="flex items-center gap-2">
                  <span className="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                  <span className="text-slate-400 text-[11px]">Emergency Hotline:</span>
                </div>
                <p className="font-mono text-emerald-400 font-extrabold text-sm tracking-wide">(082) 285-DRIVE</p>
                <p className="text-[11px] text-slate-400 font-mono">Mobile: +63 917 800 6637</p>
                <div className="pt-1 text-[10px] text-slate-500 border-t border-slate-800">
                  Dispatch Center: Butuan City, Agusan del Norte, Mindanao
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Strip */}
        <div className="bg-black/90 border-t border-slate-800/80 py-4 px-4 sm:px-6 lg:px-8 text-xs">
          <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 text-slate-500">
            <div className="flex items-center gap-3">
              <div className="flex items-center gap-1 font-mono text-[10px] px-2 py-0.5 rounded bg-slate-900 border border-slate-800">
                <span className="text-slate-600">P</span>
                <span className="text-slate-600">R</span>
                <span className="text-slate-600">N</span>
                <span className="text-emerald-400 font-bold bg-emerald-950 px-1 rounded border border-emerald-500/40">D</span>
                <span className="text-slate-500 ml-1">CRUISE MODE</span>
              </div>
              <span>&copy; {new Date().getFullYear()} ONEDRIVE Car Rental System. All rights reserved.</span>
            </div>

            <div className="flex items-center gap-4 text-[11px] text-slate-400">
              <span className="flex items-center gap-1 text-slate-500">
                <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
                Mindanao Fleet Certified
              </span>
              <span>&bull;</span>
              <span className="text-slate-400">GPS Telematics Active</span>
              <span>&bull;</span>
              <span className="text-slate-400">Full Collision Waiver</span>
            </div>
          </div>
        </div>
      </footer>

      {/* Opaque Fixed Bottom Tab Bar (< 768px / Mobile App Shell) */}
      <nav className="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-white dark:bg-gray-900 pb-[env(safe-area-inset-bottom)] border-t border-gray-200 dark:border-gray-800 px-1.5 py-1.5 shadow-[0_-8px_24px_rgba(0,0,0,0.12)] transition-colors duration-300">
        <div className="grid grid-cols-5 text-center items-center gap-1 max-w-md mx-auto">
          {/* Home */}
          <Link
            href="/menu"
            className={`flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl transition-all duration-200 active:scale-90 ${currentRoute === '/menu'
              ? 'text-emerald-600 dark:text-emerald-400 font-black bg-emerald-500/10 dark:bg-emerald-400/10 border border-emerald-500/20'
              : 'text-gray-500 dark:text-gray-400 font-bold hover:text-gray-900 dark:hover:text-gray-200'
              }`}
          >
            <Home className={`w-5 h-5 mb-0.5 transition-transform duration-200 ${currentRoute === '/menu' ? 'scale-110 text-emerald-600 dark:text-emerald-400' : ''}`} />
            <span className="text-[10px] leading-tight tracking-tight">Home</span>
          </Link>

          {/* My Fleet */}
          <Link
            href="/vehicles"
            className={`flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl transition-all duration-200 active:scale-90 ${/^\/vehicles($|\?)/.test(currentRoute) && !currentRoute.includes('all-listing')
              ? 'text-emerald-600 dark:text-emerald-400 font-black bg-emerald-500/10 dark:bg-emerald-400/10 border border-emerald-500/20'
              : 'text-gray-500 dark:text-gray-400 font-bold hover:text-gray-900 dark:hover:text-gray-200'
              }`}
          >
            <Car className={`w-5 h-5 mb-0.5 transition-transform duration-200 ${/^\/vehicles($|\?)/.test(currentRoute) && !currentRoute.includes('all-listing') ? 'scale-110 text-emerald-600 dark:text-emerald-400' : ''}`} />
            <span className="text-[10px] leading-tight tracking-tight">My Fleet</span>
          </Link>

          {/* Bookings */}
          <Link
            href="/bookings"
            className={`flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl transition-all duration-200 active:scale-90 ${currentRoute.startsWith('/bookings')
              ? 'text-emerald-600 dark:text-emerald-400 font-black bg-emerald-500/10 dark:bg-emerald-400/10 border border-emerald-500/20'
              : 'text-gray-500 dark:text-gray-400 font-bold hover:text-gray-900 dark:hover:text-gray-200'
              }`}
          >
            <Calendar className={`w-5 h-5 mb-0.5 transition-transform duration-200 ${currentRoute.startsWith('/bookings') ? 'scale-110 text-emerald-600 dark:text-emerald-400' : ''}`} />
            <span className="text-[10px] leading-tight tracking-tight">Bookings</span>
          </Link>

          {/* Calendar */}
          <Link
            href="/calendar"
            className={`flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl transition-all duration-200 active:scale-90 ${currentRoute.startsWith('/calendar')
              ? 'text-emerald-600 dark:text-emerald-400 font-black bg-emerald-500/10 dark:bg-emerald-400/10 border border-emerald-500/20'
              : 'text-gray-500 dark:text-gray-400 font-bold hover:text-gray-900 dark:hover:text-gray-200'
              }`}
          >
            <Calendar className={`w-5 h-5 mb-0.5 transition-transform duration-200 ${currentRoute.startsWith('/calendar') ? 'scale-110 text-emerald-600 dark:text-emerald-400' : ''}`} />
            <span className="text-[10px] leading-tight tracking-tight">Calendar</span>
          </Link>

          {/* Profile */}
          <Link
            href="/profile"
            className={`flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl transition-all duration-200 active:scale-90 ${currentRoute.startsWith('/profile')
              ? 'text-emerald-600 dark:text-emerald-400 font-black bg-emerald-500/10 dark:bg-emerald-400/10 border border-emerald-500/20'
              : 'text-gray-500 dark:text-gray-400 font-bold hover:text-gray-900 dark:hover:text-gray-200'
              }`}
          >
            <User className={`w-5 h-5 mb-0.5 transition-transform duration-200 ${currentRoute.startsWith('/profile') ? 'scale-110 text-emerald-600 dark:text-emerald-400' : ''}`} />
            <span className="text-[10px] leading-tight tracking-tight">Profile</span>
          </Link>
        </div>
      </nav>
    </div>
  );
}
