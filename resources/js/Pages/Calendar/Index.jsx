import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar as CalendarIcon,
  ChevronLeft,
  ChevronRight,
  Plus,
  Car,
  User,
  Phone,
  MapPin,
  Clock,
  Filter,
  Grid,
  CheckCircle2
} from 'lucide-react';

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
  bookings = []
}) {
  const [selectedMobileDate, setSelectedMobileDate] = useState(
    selectedMonth === new Date().toISOString().slice(0, 7)
      ? new Date().toISOString().slice(0, 10)
      : startOfWeek
  );

  const handleMonthChange = (m) => {
    router.get('/calendar', {
      month: m,
      vehicle_id: selectedVehicleId,
      user_id: selectedUserId,
    }, { preserveState: true });
  };

  const handleVehicleFilter = (vId) => {
    router.get('/calendar', {
      month: selectedMonth,
      vehicle_id: vId,
      user_id: selectedUserId,
    }, { preserveState: true });
  };

  const handleUserFilter = (uId) => {
    router.get('/calendar', {
      month: selectedMonth,
      vehicle_id: selectedVehicleId,
      user_id: uId,
    }, { preserveState: true });
  };

  // Helper to generate days range between startOfWeek and endOfWeek
  const daysGrid = [];
  let curr = new Date(startOfWeek);
  const end = new Date(endOfWeek);

  while (curr <= end) {
    daysGrid.push(new Date(curr));
    curr.setDate(curr.getDate() + 1);
  }

  const getStatusBadge = (status) => {
    switch (status) {
      case 'confirmed':
        return <span className="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Confirmed</span>;
      case 'pending':
        return <span className="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Pending</span>;
      case 'completed':
        return <span className="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Completed</span>;
      default:
        return <span className="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">{status}</span>;
    }
  };

  const selectedDateBookings = bookings.filter((b) => {
    const dStr = selectedMobileDate;
    return dStr >= b.start_date && dStr <= b.end_date;
  });

  return (
    <AppLayout title="Rental Schedule Calendar">
      <Head title="Calendar Schedule" />

      <div className="space-y-6 pb-6">
        {/* Header & Controls */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-5 rounded-3xl shadow-xs transition-colors duration-300">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800 flex items-center justify-center shrink-0">
              <CalendarIcon className="w-5 h-5" />
            </div>
            <div>
              <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Fleet Calendar Schedule
              </h1>
              <p className="text-xs text-gray-500 dark:text-gray-400">Interactive monthly availability and trip timeline</p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <Link
              href={`/bookings/create?start_date=${selectedMobileDate}&end_date=${selectedMobileDate}`}
              className="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-600/20 active:scale-95 transition-transform"
            >
              <Plus className="w-4 h-4" />
              Book Selected Date
            </Link>
          </div>
        </div>

        {/* Month Navigator & Filters Bar */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-4 rounded-3xl shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 transition-colors duration-300">
          <div className="flex items-center justify-between md:justify-start gap-3">
            <button
              onClick={() => handleMonthChange(prevMonth)}
              className="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-950 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800"
            >
              <ChevronLeft className="w-5 h-5" />
            </button>
            <h2 className="text-base font-extrabold text-gray-900 dark:text-white min-w-[140px] text-center">
              {new Date(`${selectedMonth}-01`).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
            </h2>
            <button
              onClick={() => handleMonthChange(nextMonth)}
              className="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-950 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800"
            >
              <ChevronRight className="w-5 h-5" />
            </button>

            <button
              onClick={() => handleMonthChange(new Date().toISOString().slice(0, 7))}
              className="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold"
            >
              Today
            </button>
          </div>

          <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <select
              value={selectedUserId}
              onChange={(e) => handleUserFilter(e.target.value)}
              className="py-2.5 px-3 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-700 dark:text-gray-300"
            >
              <option value="all">All Host Users</option>
              {allUsers.map((u) => (
                <option key={u.id} value={u.id}>
                  👤 {u.name}
                </option>
              ))}
            </select>

            <select
              value={selectedVehicleId}
              onChange={(e) => handleVehicleFilter(e.target.value)}
              className="py-2.5 px-3 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-700 dark:text-gray-300"
            >
              <option value="">All Fleet Vehicles</option>
              {allVehicles.map((v) => (
                <option key={v.id} value={v.id}>
                  {v.name} ({v.license_plate})
                </option>
              ))}
            </select>
          </div>
        </div>

        {/* Calendar Grid View */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs transition-colors duration-300">
          {/* Day Headers */}
          <div className="grid grid-cols-7 bg-gray-50 dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 text-center py-3 text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
            <div className="text-rose-500">Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
          </div>

          {/* Grid Cells */}
          <div className="grid grid-cols-7 divide-x divide-y divide-gray-200/80 dark:divide-gray-800/80">
            {daysGrid.map((dayObj, idx) => {
              const dateStr = dayObj.toISOString().slice(0, 10);
              const isSelected = selectedMobileDate === dateStr;
              const isToday = new Date().toISOString().slice(0, 10) === dateStr;

              const dayBookings = bookings.filter((b) => dateStr >= b.start_date && dateStr <= b.end_date);

              return (
                <button
                  key={idx}
                  type="button"
                  onClick={() => setSelectedMobileDate(dateStr)}
                  className={`min-h-[90px] sm:min-h-[120px] p-2 text-left flex flex-col justify-between transition-colors relative ${
                    isSelected ? 'bg-emerald-500/10 ring-2 ring-emerald-500 z-10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/40'
                  }`}
                >
                  <div className="flex items-center justify-between w-full">
                    <span
                      className={`text-xs font-extrabold w-6 h-6 rounded-full flex items-center justify-center ${
                        isToday ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-800 dark:text-gray-200'
                      }`}
                    >
                      {dayObj.getDate()}
                    </span>

                    {dayBookings.length > 0 && (
                      <span className="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        {dayBookings.length}
                      </span>
                    )}
                  </div>

                  <div className="space-y-1 w-full mt-2">
                    {dayBookings.slice(0, 2).map((bk) => (
                      <div
                        key={bk.id}
                        className="px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-[9px] font-bold text-emerald-800 dark:text-emerald-200 truncate"
                      >
                        🚗 {bk.vehicle?.name || 'Vehicle'}
                      </div>
                    ))}
                    {dayBookings.length > 2 && (
                      <span className="text-[9px] text-gray-500 dark:text-gray-400 font-bold block text-center">
                        +{dayBookings.length - 2} more
                      </span>
                    )}
                  </div>
                </button>
              );
            })}
          </div>
        </div>

        {/* Selected Date Schedule Panel */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 shadow-xs space-y-4 transition-colors duration-300">
          <div className="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
            <div>
              <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Selected Schedule Date</span>
              <h3 className="text-base font-extrabold text-gray-900 dark:text-white">
                {new Date(selectedMobileDate).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}
              </h3>
            </div>
            <Link
              href={`/bookings/create?start_date=${selectedMobileDate}&end_date=${selectedMobileDate}`}
              className="px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700"
            >
              + Book for Date
            </Link>
          </div>

          {selectedDateBookings.length === 0 ? (
            <div className="p-6 text-center text-gray-400 dark:text-gray-500 text-xs">
              No car reservations scheduled for this date.
            </div>
          ) : (
            <div className="space-y-3">
              {selectedDateBookings.map((b) => (
                <div
                  key={b.id}
                  className="p-4 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                >
                  <div className="space-y-1">
                    <div className="flex items-center gap-2">
                      <span className="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-300">#{b.booking_code}</span>
                      {getStatusBadge(b.status)}
                    </div>
                    <h4 className="text-sm font-extrabold text-gray-900 dark:text-white">{b.customer_name}</h4>
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                      Vehicle: <strong className="text-gray-800 dark:text-gray-200">{b.vehicle?.name}</strong> ({b.vehicle?.license_plate})
                    </p>
                    <p className="text-[11px] text-amber-600 dark:text-amber-400">
                      📍 {b.destination || 'Standard Route'} • 📞 {b.customer_phone}
                    </p>
                  </div>

                  <Link
                    href={`/bookings/${b.id}`}
                    className="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-bold text-center hover:bg-gray-200 dark:hover:bg-gray-700"
                  >
                    View Ticket
                  </Link>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
