import React, { useMemo, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

/**
 * Per-vehicle color palettes (ported 1:1 from the old Blade calendar).
 */
const COLOR_PALETTES = [
  {
    name: 'Indigo',
    bg: 'bg-indigo-50 dark:bg-indigo-950/70',
    border: 'border-indigo-200 dark:border-indigo-700',
    text: 'text-indigo-900 dark:text-indigo-200',
    badge: 'bg-indigo-600 text-white',
    dot: 'bg-indigo-500',
    pill: 'bg-indigo-100 text-indigo-900 dark:bg-indigo-900/60 dark:text-indigo-200 border-indigo-200 dark:border-indigo-700',
  },
  {
    name: 'Emerald',
    bg: 'bg-emerald-50 dark:bg-emerald-950/70',
    border: 'border-emerald-200 dark:border-emerald-700',
    text: 'text-emerald-900 dark:text-emerald-200',
    badge: 'bg-emerald-600 text-white',
    dot: 'bg-emerald-500',
    pill: 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/60 dark:text-emerald-200 border-emerald-200 dark:border-emerald-700',
  },
  {
    name: 'Amber',
    bg: 'bg-amber-50 dark:bg-amber-950/70',
    border: 'border-amber-200 dark:border-amber-700',
    text: 'text-amber-900 dark:text-amber-200',
    badge: 'bg-amber-600 text-white',
    dot: 'bg-amber-500',
    pill: 'bg-amber-100 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 border-amber-200 dark:border-amber-700',
  },
  {
    name: 'Rose',
    bg: 'bg-rose-50 dark:bg-rose-950/70',
    border: 'border-rose-200 dark:border-rose-700',
    text: 'text-rose-900 dark:text-rose-200',
    badge: 'bg-rose-600 text-white',
    dot: 'bg-rose-500',
    pill: 'bg-rose-100 text-rose-900 dark:bg-rose-900/60 dark:text-rose-200 border-rose-200 dark:border-rose-700',
  },
  {
    name: 'Purple',
    bg: 'bg-purple-50 dark:bg-purple-950/70',
    border: 'border-purple-200 dark:border-purple-700',
    text: 'text-purple-900 dark:text-purple-200',
    badge: 'bg-purple-600 text-white',
    dot: 'bg-purple-500',
    pill: 'bg-purple-100 text-purple-900 dark:bg-purple-900/60 dark:text-purple-200 border-purple-200 dark:border-purple-700',
  },
  {
    name: 'Cyan',
    bg: 'bg-cyan-50 dark:bg-cyan-950/70',
    border: 'border-cyan-200 dark:border-cyan-700',
    text: 'text-cyan-900 dark:text-cyan-200',
    badge: 'bg-cyan-600 text-white',
    dot: 'bg-cyan-500',
    pill: 'bg-cyan-100 text-cyan-900 dark:bg-cyan-900/60 dark:text-cyan-200 border-cyan-200 dark:border-cyan-700',
  },
  {
    name: 'Fuchsia',
    bg: 'bg-fuchsia-50 dark:bg-fuchsia-950/70',
    border: 'border-fuchsia-200 dark:border-fuchsia-700',
    text: 'text-fuchsia-900 dark:text-fuchsia-200',
    badge: 'bg-fuchsia-600 text-white',
    dot: 'bg-fuchsia-500',
    pill: 'bg-fuchsia-100 text-fuchsia-900 dark:bg-fuchsia-900/60 dark:text-fuchsia-200 border-fuchsia-200 dark:border-fuchsia-700',
  },
];

/* ---------- Date helpers (string based, timezone-safe) ---------- */

const pad = (n) => String(n).padStart(2, '0');

const parseYmd = (ymd) => {
  const [y, m, d] = ymd.slice(0, 10).split('-').map(Number);
  return new Date(y, m - 1, d);
};

const toYmd = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

const dayDiff = (fromYmd, toYmdStr) => Math.round((parseYmd(toYmdStr) - parseYmd(fromYmd)) / 86400000);

const formatDate = (ymd, options) => parseYmd(ymd).toLocaleDateString('en-US', options);

const formatShort = (ymd) => formatDate(ymd, { month: 'short', day: '2-digit', year: 'numeric' });

const formatTime = (time) => {
  const [h = 0, m = 0] = String(time || '00:00').split(':').map(Number);
  const suffix = h >= 12 ? 'PM' : 'AM';
  return `${h % 12 || 12}:${pad(m)} ${suffix}`;
};

const solidStatusClass = (status, shade = 600) => {
  if (status === 'confirmed') return shade === 500 ? 'bg-emerald-500' : 'bg-emerald-600';
  if (status === 'completed') return shade === 500 ? 'bg-blue-500' : 'bg-blue-600';
  if (status === 'pending') return 'bg-amber-500';
  return shade === 500 ? 'bg-rose-500' : 'bg-rose-600';
};

const vehicleStatusClass = (status) => {
  if (status === 'available') return 'bg-emerald-500 text-white';
  if (status === 'maintenance') return 'bg-amber-500 text-white';
  return 'bg-rose-600 text-white';
};

const buildQuery = (params) => {
  const query = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      query.set(key, value);
    }
  });
  const str = query.toString();
  return str ? `?${str}` : '';
};

/* ---------- Icons ---------- */

const CalendarSvg = ({ className }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
  </svg>
);

/* ---------- Hover popover (desktop grid + timeline pills) ---------- */

function BookingPopover({ booking, vehicle }) {
  return (
    <div className="invisible opacity-0 scale-95 translate-y-1 group-hover/popover:visible group-hover/popover:opacity-100 group-hover/popover:scale-100 group-hover/popover:translate-y-0 transition ease-out duration-150 absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-md text-white rounded-2xl shadow-2xl z-[100] text-left pointer-events-none border border-slate-700/80 space-y-2.5">
      {/* Header: Vehicle & Status */}
      <div className="flex items-center justify-between border-b border-slate-800 pb-2 gap-2">
        <span className="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider truncate">
          🚗 {vehicle?.name ?? 'Vehicle'} ({vehicle?.license_plate ?? 'N/A'})
        </span>
        <span className={`px-2 py-0.5 text-[9px] font-black uppercase rounded-full text-white shrink-0 ${solidStatusClass(booking.status, 500)}`}>
          {booking.status}
        </span>
      </div>

      {/* 1. Renter Name & 2. Contact Number */}
      <div className="space-y-1">
        <p className="text-xs font-black text-white flex items-center justify-between gap-2">
          <span className="text-slate-400 font-semibold text-[11px]">1. Renter Name:</span>
          <span className="text-emerald-400 font-bold truncate">{booking.customer_name}</span>
        </p>
        <p className="text-xs font-black text-white flex items-center justify-between gap-2">
          <span className="text-slate-400 font-semibold text-[11px]">2. Contact Number:</span>
          <span className="text-sky-300 font-bold">📞 {booking.customer_phone || 'N/A'}</span>
        </p>
      </div>

      {/* 3. Destination */}
      <div className="pt-1.5 border-t border-slate-800">
        <span className="text-[10px] text-slate-400 font-semibold block">3. Destination:</span>
        <span className="text-xs font-extrabold text-amber-300 block">📍 {booking.destination_label}</span>
      </div>

      {/* 4. Pickup & 5. Return */}
      <div className="pt-1.5 border-t border-slate-800 grid grid-cols-2 gap-2 text-[11px]">
        <div className="bg-slate-800/80 p-2 rounded-xl">
          <span className="text-[9px] text-emerald-400 font-bold uppercase block">4. Pickup Date/Time</span>
          <span className="font-extrabold text-white block">{formatShort(booking.start_date)}</span>
          <span className="font-bold text-emerald-300 text-[10px]">⏰ {formatTime(booking.pickup_time)}</span>
        </div>
        <div className="bg-slate-800/80 p-2 rounded-xl">
          <span className="text-[9px] text-rose-400 font-bold uppercase block">5. Return Date/Time</span>
          <span className="font-extrabold text-white block">{formatShort(booking.end_date)}</span>
          <span className="font-bold text-rose-300 text-[10px]">⏰ {formatTime(booking.return_time)}</span>
        </div>
      </div>
    </div>
  );
}

export default function Index({
  currentDate,
  selectedMonth,
  prevMonth,
  nextMonth,
  startOfWeek,
  endOfWeek,
  vehicles = [],
  allVehicles = [],
  selectedVehicleId = '',
  allUsers = [],
  selectedUserId = '',
  bookings = [],
}) {
  const { auth } = usePage().props;
  const todayYmd = toYmd(new Date());
  const currentMonthNumber = parseYmd(currentDate).getMonth();
  const monthLabel = formatDate(currentDate, { month: 'long', year: 'numeric' });
  const monthShortLabel = formatDate(currentDate, { month: 'short', year: 'numeric' });

  const [activeTab, setActiveTab] = useState('grid');
  const [selectedMobileDate, setSelectedMobileDate] = useState(
    todayYmd.slice(0, 7) === selectedMonth ? todayYmd : currentDate
  );

  /* ----- Navigation ----- */

  const visitCalendar = (params) => {
    router.get(
      '/calendar',
      {
        month: selectedMonth,
        vehicle_id: selectedVehicleId || undefined,
        user_id: selectedUserId || undefined,
        ...params,
      },
      { preserveScroll: true }
    );
  };

  /* ----- Derived data ----- */

  const vehicleColorMap = useMemo(() => {
    const map = {};
    allVehicles.forEach((v, index) => {
      map[v.id] = COLOR_PALETTES[index % COLOR_PALETTES.length];
    });
    return map;
  }, [allVehicles]);

  const paletteFor = (vehicleId) => vehicleColorMap[vehicleId] ?? COLOR_PALETTES[0];

  const daysGrid = useMemo(() => {
    const days = [];
    const cursor = parseYmd(startOfWeek);
    const end = parseYmd(endOfWeek);
    while (cursor <= end) {
      days.push(new Date(cursor));
      cursor.setDate(cursor.getDate() + 1);
    }
    return days;
  }, [startOfWeek, endOfWeek]);

  const bookingsOn = (ymd) => bookings.filter((b) => ymd >= b.start_date && ymd <= b.end_date);

  // Persistent slot (track) assignment so multi-day bookings stay on one line in the mobile grid.
  const { bookingSlots, maxSlotsToDisplay } = useMemo(() => {
    const sorted = [...bookings].sort((a, b) => {
      if (a.start_date !== b.start_date) return a.start_date < b.start_date ? -1 : 1;
      const durationDiff = dayDiff(b.start_date, b.end_date) - dayDiff(a.start_date, a.end_date);
      if (durationDiff !== 0) return durationDiff;
      return a.id - b.id;
    });

    const slots = {};
    const trackEndDates = [];

    sorted.forEach((bk) => {
      let assigned = trackEndDates.findIndex((lastEnd) => lastEnd < bk.start_date);
      if (assigned === -1) {
        assigned = trackEndDates.length;
        trackEndDates.push(bk.end_date);
      } else {
        trackEndDates[assigned] = bk.end_date;
      }
      slots[bk.id] = assigned;
    });

    return {
      bookingSlots: slots,
      maxSlotsToDisplay: Math.max(2, Math.min(4, trackEndDates.length)),
    };
  }, [bookings]);

  const selectedDateBookings = bookingsOn(selectedMobileDate);

  return (
    <AppLayout title="My Calendar">
      <Head title="My Calendar" />

      <div className="space-y-6 pb-6">
        {/* Page Header */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <CalendarSvg className="w-7 h-7 text-amber-500" />
              My Calendar
            </h1>
            <p className="text-sm text-gray-500 dark:text-gray-400">
              Integrated vehicle schedule &amp; reservation calendar with status tracking and unique vehicle colors.
            </p>
          </div>
        </div>

        {/* Month Navigation & Vehicle Filter Header */}
        <div className="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
          {/* Month Navigator */}
          <div className="flex items-center justify-between md:justify-start w-full md:w-auto gap-2 sm:gap-3">
            <div className="flex items-center space-x-2">
              <button
                type="button"
                onClick={() => visitCalendar({ month: prevMonth })}
                className="p-2 sm:p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 rounded-xl transition"
                aria-label="Previous month"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <h3 className="text-base sm:text-xl font-extrabold text-gray-900 dark:text-white min-w-[130px] sm:min-w-[180px] text-center">
                {monthLabel}
              </h3>
              <button
                type="button"
                onClick={() => visitCalendar({ month: nextMonth })}
                className="p-2 sm:p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 rounded-xl transition"
                aria-label="Next month"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>
            <button
              type="button"
              onClick={() => visitCalendar({ month: todayYmd.slice(0, 7) })}
              className="px-3 py-2 text-xs font-bold bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-xl shadow-2xs hover:bg-indigo-100 transition"
            >
              Today
            </button>
          </div>

          {/* User & Vehicle Filters & View Switcher */}
          <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full md:w-auto">
              <select
                value={selectedUserId ?? 'all'}
                onChange={(e) => visitCalendar({ user_id: e.target.value })}
                className="w-full text-xs font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-amber-500 p-2.5"
              >
                <option value="all">All Users</option>
                {allUsers.map((u) => (
                  <option key={u.id} value={String(u.id)}>
                    👤 {u.name} {auth?.user?.id === u.id ? '(You)' : ''}
                  </option>
                ))}
              </select>

              <select
                value={selectedVehicleId ?? ''}
                onChange={(e) => visitCalendar({ vehicle_id: e.target.value || undefined })}
                className="w-full text-xs font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-amber-500 p-2.5"
              >
                <option value="">All Vehicles Fleet</option>
                {allVehicles.map((v) => (
                  <option key={v.id} value={String(v.id)}>
                    {v.name} ({v.license_plate})
                  </option>
                ))}
              </select>
            </div>

            {/* View Switch Buttons */}
            <div className="flex items-center bg-gray-100 dark:bg-gray-700 p-1 rounded-xl w-full sm:w-auto">
              {[
                { key: 'grid', label: 'Month Calendar' },
                { key: 'timeline', label: 'Vehicle Timelines' },
              ].map((tab) => (
                <button
                  key={tab.key}
                  type="button"
                  onClick={() => setActiveTab(tab.key)}
                  className={`flex-1 sm:flex-initial px-3 py-2 text-xs font-bold rounded-lg transition text-center ${
                    activeTab === tab.key
                      ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm'
                      : 'text-gray-500 dark:text-gray-400'
                  }`}
                >
                  {tab.label}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* TAB 1: Monthly Calendar Grid */}
        {activeTab === 'grid' && (
          <div className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-visible">
            {/* DESKTOP CALENDAR VIEW */}
            <div className="hidden md:block">
              <div className="grid grid-cols-7 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-center py-3 text-xs font-bold uppercase text-gray-500">
                <div className="text-rose-500">Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
              </div>

              <div className="grid grid-cols-7 divide-x divide-y divide-gray-100 dark:divide-gray-700">
                {daysGrid.map((day) => {
                  const dateStr = toYmd(day);
                  const isCurrentMonth = day.getMonth() === currentMonthNumber;
                  const isToday = dateStr === todayYmd;
                  const dayBookings = bookingsOn(dateStr);

                  return (
                    <div
                      key={dateStr}
                      className={`min-h-[140px] h-auto p-2 transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30 group relative hover:z-30 flex flex-col justify-start space-y-1.5 ${
                        !isCurrentMonth ? 'bg-gray-50/40 dark:bg-gray-900/40 text-gray-400' : ''
                      }`}
                    >
                      {/* Date Number & Quick Add */}
                      <div className="flex items-center justify-between">
                        <span
                          className={`text-xs font-extrabold px-2 py-0.5 rounded-full ${
                            isToday
                              ? 'bg-indigo-600 text-white'
                              : isCurrentMonth
                                ? 'text-gray-900 dark:text-white'
                                : 'text-gray-400'
                          }`}
                        >
                          {day.getDate()}
                        </span>
                        <Link
                          href={`/bookings/create${buildQuery({ start_date: dateStr, end_date: dateStr, vehicle_id: selectedVehicleId })}`}
                          title="Book on this date"
                          className="opacity-0 group-hover:opacity-100 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded transition"
                        >
                          + Book
                        </Link>
                      </div>

                      {/* Bookings on this day */}
                      <div className="space-y-1.5 flex-1">
                        {dayBookings.map((bk) => {
                          const pal = paletteFor(bk.vehicle_id);
                          return (
                            <div key={bk.id} className="w-full relative z-[1] hover:z-50 group/popover">
                              <Link
                                href={`/bookings/${bk.id}`}
                                className={`block p-1.5 rounded-xl text-[10px] leading-tight font-bold transition shadow-xs border ${pal.bg} ${pal.border} ${pal.text} hover:scale-[1.02]`}
                              >
                                <div className="flex items-center justify-between gap-1">
                                  <span className="truncate font-extrabold">🚗 {bk.vehicle?.name ?? 'Vehicle'}</span>
                                  <span className={`px-1.5 text-[8px] font-black uppercase tracking-wider rounded text-white flex-shrink-0 ${solidStatusClass(bk.status)}`}>
                                    {bk.status}
                                  </span>
                                </div>
                                <span className="block font-semibold text-[9px] opacity-80 mt-0.5 truncate">{bk.customer_name}</span>
                                {bk.destination && (
                                  <span className="block text-[8px] font-extrabold truncate text-sky-700 dark:text-sky-300 mt-0.5">
                                    📍 {bk.destination}
                                  </span>
                                )}
                              </Link>
                              <BookingPopover booking={bk} vehicle={bk.vehicle} />
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* MOBILE CALENDAR VIEW */}
            <div className="block md:hidden">
              <div className="grid grid-cols-7 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-center py-2.5 text-xs font-black uppercase text-gray-500">
                <div className="text-rose-500">S</div>
                <div>M</div>
                <div>T</div>
                <div>W</div>
                <div>T</div>
                <div>F</div>
                <div>S</div>
              </div>

              <div className="grid grid-cols-7 gap-0.5 p-2 bg-gray-50/50 dark:bg-gray-900/40">
                {daysGrid.map((day) => {
                  const dateStr = toYmd(day);
                  const isCurrentMonth = day.getMonth() === currentMonthNumber;
                  const isToday = dateStr === todayYmd;
                  const isSelected = selectedMobileDate === dateStr;
                  const dayBookings = bookingsOn(dateStr);

                  return (
                    <button
                      key={dateStr}
                      type="button"
                      onClick={() => setSelectedMobileDate(dateStr)}
                      className={`min-h-[64px] h-auto p-1 py-1.5 cursor-pointer rounded-xl border flex flex-col justify-between items-center transition relative overflow-hidden ${
                        isSelected
                          ? 'ring-2 ring-indigo-600 dark:ring-indigo-400 font-extrabold bg-indigo-50 dark:bg-indigo-950/80 scale-[1.04] shadow-sm z-20'
                          : isCurrentMonth
                            ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-100 dark:border-gray-700'
                            : 'bg-gray-100/60 dark:bg-gray-900/60 text-gray-400 border-transparent'
                      }`}
                    >
                      <span
                        className={`text-xs font-black px-1.5 rounded-full ${
                          isToday
                            ? 'bg-indigo-600 text-white'
                            : isCurrentMonth
                              ? 'text-gray-900 dark:text-white'
                              : 'text-gray-400'
                        }`}
                      >
                        {day.getDate()}
                      </span>

                      {/* Continuous aligned booking lines */}
                      <div className="w-full flex flex-col gap-1 px-0 pb-1 mt-1">
                        {Array.from({ length: maxSlotsToDisplay }).map((_, slot) => {
                          const slotBooking = dayBookings.find((b) => bookingSlots[b.id] === slot);

                          if (!slotBooking) {
                            return <div key={slot} className="h-2 w-full opacity-0 pointer-events-none" />;
                          }

                          const pal = paletteFor(slotBooking.vehicle_id);
                          const renderStart = dateStr === slotBooking.start_date || day.getDay() === 0;
                          const renderEnd = dateStr === slotBooking.end_date || day.getDay() === 6;
                          const title = `${slotBooking.vehicle?.name ?? 'Vehicle'} (${slotBooking.customer_name})`;

                          let shapeClass = 'w-full rounded-full';
                          if (renderStart && !renderEnd) {
                            shapeClass = 'w-[calc(100%+0.75rem)] -mr-3 rounded-l-full rounded-r-none relative z-10';
                          } else if (!renderStart && !renderEnd) {
                            shapeClass = 'w-[calc(100%+1.5rem)] -mx-3 rounded-none relative z-10';
                          } else if (!renderStart && renderEnd) {
                            shapeClass = 'w-[calc(100%+0.75rem)] -ml-3 rounded-r-full rounded-l-none relative z-10';
                          }

                          return (
                            <div
                              key={slot}
                              className={`h-2 ${pal.dot} ${shapeClass} shadow-2xs transition-all`}
                              title={title}
                            />
                          );
                        })}
                      </div>
                    </button>
                  );
                })}
              </div>

              {/* Mobile Selected Date Booking Details Panel */}
              <div className="p-4 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 space-y-4">
                <div className="space-y-3">
                  <div className="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <div>
                      <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Selected Schedule Date</span>
                      <h4 className="text-base font-extrabold text-gray-900 dark:text-white">
                        {formatDate(selectedMobileDate, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}
                      </h4>
                    </div>
                    <Link
                      href={`/bookings/create${buildQuery({ start_date: selectedMobileDate, end_date: selectedMobileDate, vehicle_id: selectedVehicleId })}`}
                      className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition"
                    >
                      + Book For Date
                    </Link>
                  </div>

                  {selectedDateBookings.length === 0 ? (
                    <div className="p-6 text-center text-gray-400 space-y-2 bg-gray-50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-700">
                      <CalendarSvg className="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600" />
                      <p className="text-xs font-bold text-gray-500 dark:text-gray-400">No events or car reservations scheduled for this date.</p>
                    </div>
                  ) : (
                    selectedDateBookings.map((bk) => {
                      const pal = paletteFor(bk.vehicle_id);
                      return (
                        <div key={bk.id} className={`p-4 rounded-2xl border space-y-3 transition ${pal.bg} ${pal.border}`}>
                          <div className="flex items-center justify-between gap-2">
                            <div className="flex items-center space-x-2 min-w-0">
                              <span className={`w-3 h-3 rounded-full shrink-0 ${pal.dot}`} />
                              <h5 className={`text-sm font-extrabold truncate ${pal.text}`}>🚗 {bk.vehicle?.name ?? 'Vehicle'}</h5>
                              <span className={`text-xs font-mono font-bold opacity-75 shrink-0 ${pal.text}`}>
                                ({bk.vehicle?.license_plate ?? 'N/A'})
                              </span>
                            </div>
                            <span className={`px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-full text-white shrink-0 ${solidStatusClass(bk.status)}`}>
                              {bk.status}
                            </span>
                          </div>

                          <div className="grid grid-cols-1 gap-2 text-xs pt-2 border-t border-black/5 dark:border-white/10">
                            <div className="flex justify-between items-center gap-2">
                              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400">1. Renter Name:</span>
                              <span className="font-black text-gray-900 dark:text-white text-right">{bk.customer_name}</span>
                            </div>
                            <div className="flex justify-between items-center gap-2">
                              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400">2. Contact Number:</span>
                              <span className="font-extrabold text-sky-600 dark:text-sky-400">📞 {bk.customer_phone || 'N/A'}</span>
                            </div>
                            <div className="flex justify-between items-center gap-2">
                              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400">3. Destination:</span>
                              <span className="font-extrabold text-amber-600 dark:text-amber-400 text-right">📍 {bk.destination_label}</span>
                            </div>
                            <div className="flex justify-between items-center gap-2">
                              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400">4. Pickup Schedule:</span>
                              <span className="font-extrabold text-emerald-600 dark:text-emerald-400 text-right">
                                {formatShort(bk.start_date)} ⏰ {formatTime(bk.pickup_time)}
                              </span>
                            </div>
                            <div className="flex justify-between items-center gap-2">
                              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400">5. Return Schedule:</span>
                              <span className="font-extrabold text-rose-600 dark:text-rose-400 text-right">
                                {formatShort(bk.end_date)} ⏰ {formatTime(bk.return_time)}
                              </span>
                            </div>
                          </div>

                          <div className="pt-2 border-t border-black/5 dark:border-white/10 flex justify-end">
                            <Link
                              href={`/bookings/${bk.id}`}
                              className="text-xs font-black text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1"
                            >
                              <span>View Reservation Receipt</span>
                              <span>&rarr;</span>
                            </Link>
                          </div>
                        </div>
                      );
                    })
                  )}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* TAB 2: Vehicle Timeline Schedule Grid */}
        {activeTab === 'timeline' && (
          <div className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-visible p-6 space-y-6">
            <h3 className="text-base font-bold text-gray-900 dark:text-white border-b pb-3 border-gray-100 dark:border-gray-700">
              Vehicle Fleet Schedule Matrix &amp; Operational Statuses — {monthLabel}
            </h3>

            <div className="space-y-4">
              {vehicles.map((vehicle) => {
                const vBookings = bookings.filter((b) => b.vehicle_id === vehicle.id);
                const pal = paletteFor(vehicle.id);

                return (
                  <div key={vehicle.id} className={`p-4 rounded-2xl border space-y-3 transition ${pal.bg} ${pal.border}`}>
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-3">
                      <div className="flex items-center space-x-3">
                        <span className={`px-2.5 py-1 text-xs font-black rounded-lg shadow-xs font-mono ${pal.badge}`}>
                          {vehicle.license_plate}
                        </span>
                        <div>
                          <div className="flex items-center space-x-2">
                            <h4 className={`font-extrabold text-sm ${pal.text}`}>{vehicle.name}</h4>
                            <span className={`px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider ${vehicleStatusClass(vehicle.status)}`}>
                              {String(vehicle.status ?? '').replace(/_/g, ' ')}
                            </span>
                          </div>
                          <p className={`text-xs opacity-75 ${pal.text}`}>
                            ₱{Number(vehicle.daily_rate || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}/day • {vehicle.transmission} • {vehicle.seats} Seats
                          </p>
                        </div>
                      </div>
                      <Link
                        href={`/bookings/create${buildQuery({ vehicle_id: vehicle.id })}`}
                        className="px-3.5 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:opacity-90 font-bold text-xs rounded-xl shadow-xs transition text-center"
                      >
                        Book This Vehicle
                      </Link>
                    </div>

                    <div className="pt-2">
                      <span className={`text-[11px] font-bold opacity-75 uppercase tracking-wider block mb-1 ${pal.text}`}>
                        Booked Days in {monthShortLabel}:
                      </span>
                      <div className="flex flex-wrap gap-2">
                        {vBookings.length === 0 ? (
                          <span className={`text-xs font-bold bg-white/60 dark:bg-black/20 px-3 py-1 rounded-lg ${pal.text}`}>
                            ✓ Fully Available All Days
                          </span>
                        ) : (
                          vBookings.map((vb) => (
                            <div key={vb.id} className="inline-block relative z-[1] hover:z-50 group/popover">
                              <Link
                                href={`/bookings/${vb.id}`}
                                className={`inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl text-xs font-bold border transition shadow-xs hover:scale-105 ${pal.pill}`}
                              >
                                <span>
                                  📅 {formatDate(vb.start_date, { month: 'short', day: '2-digit' })} to {formatDate(vb.end_date, { month: 'short', day: '2-digit' })}
                                </span>
                                <span className={`px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider text-white ${solidStatusClass(vb.status)}`}>
                                  {vb.status}
                                </span>
                                <span className={`px-2 py-0.5 rounded text-[10px] font-mono ${pal.badge}`}>{vb.customer_name}</span>
                              </Link>
                              <BookingPopover booking={vb} vehicle={vehicle} />
                            </div>
                          ))
                        )}
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  );
}
