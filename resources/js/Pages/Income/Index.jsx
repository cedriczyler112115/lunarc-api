import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar,
  Car,
  Filter,
  RotateCcw,
  CheckCircle2,
  MapPin,
  Clock,
  Eye,
  TrendingUp,
  ArrowRight,
  ChevronLeft,
  ChevronRight,
  Layers
} from 'lucide-react';

export default function Index({
  incomes,
  vehicles = [],
  availableMonths = [],
  totalIncome = 0,
  totalCompletedBookings = 0,
  filters = {},
}) {
  const [selectedMonth, setSelectedMonth] = useState(filters.month || '');
  const [selectedVehicleId, setSelectedVehicleId] = useState(filters.vehicle_id || '');

  const incomeItems = incomes?.data || [];
  const avgIncomePerTrip = totalCompletedBookings > 0 ? Math.round(totalIncome / totalCompletedBookings) : 0;

  const handleFilterChange = (newMonth, newVehicleId) => {
    const month = newMonth !== undefined ? newMonth : selectedMonth;
    const vehicleId = newVehicleId !== undefined ? newVehicleId : selectedVehicleId;

    if (newMonth !== undefined) setSelectedMonth(newMonth);
    if (newVehicleId !== undefined) setSelectedVehicleId(newVehicleId);

    const query = {};
    if (month) query.month = month;
    if (vehicleId) query.vehicle_id = vehicleId;

    router.get('/income', query, { preserveState: true, replace: true });
  };

  const handleReset = () => {
    setSelectedMonth('');
    setSelectedVehicleId('');
    router.get('/income', {}, { preserveState: true, replace: true });
  };

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const cleanStr = String(dateStr).split('T')[0];
    const [y, m, d] = cleanStr.split('-').map(Number);
    if (!y || !m || !d) return dateStr;
    return new Date(y, m - 1, d).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    });
  };

  const getIncomeAmount = (booking) => {
    if (booking.actual_income !== null && booking.actual_income !== undefined) {
      return Number(booking.actual_income);
    }
    return Number(booking.total_price || 0);
  };

  return (
    <AppLayout title="My Income">
      <Head title="My Income" />

      <div className="space-y-6 max-w-7xl mx-auto pb-10">
        {/* Page Header */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div className="flex items-center gap-2.5">
              <span className="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center font-mono shadow-xs shrink-0">
                ₱
              </span>
              <div>
                <h1 className="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                  My Income
                </h1>
                <p className="text-[11px] text-gray-500 dark:text-gray-400">
                  Track actual revenue and completed rental income filtered by month and vehicle
                </p>
              </div>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <Link
              href="/bookings"
              className="px-3 py-1.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-xs flex items-center gap-1.5"
            >
              <Calendar className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
              <span>All Bookings</span>
            </Link>
          </div>
        </div>

        {/* Total Summary Metric Cards (Compact & Refined) */}
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
          {/* Card 1: Total Filtered Income */}
          <div className="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white shadow-md shadow-emerald-700/10 relative overflow-hidden">
            <span className="absolute right-3.5 top-1/2 -translate-y-1/2 text-5xl font-mono font-black text-white/10 pointer-events-none select-none">
              ₱
            </span>
            <div className="relative z-10 space-y-0.5">
              <span className="text-[10px] font-black uppercase tracking-wider text-emerald-100/90 flex items-center gap-1">
                <TrendingUp className="w-3 h-3" />
                Total Income (Filtered)
              </span>
              <div className="text-xl sm:text-2xl font-black font-mono tracking-tight pt-0.5">
                ₱{Number(totalIncome).toLocaleString()}
              </div>
              <p className="text-[10px] text-emerald-100/80 leading-tight">
                {selectedMonth || selectedVehicleId
                  ? 'Calculated from current filter selection'
                  : 'Total earnings across all completed trips'}
              </p>
            </div>
          </div>

          {/* Card 2: Completed Trips */}
          <div className="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-xs relative overflow-hidden">
            <div className="space-y-0.5">
              <span className="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 flex items-center gap-1">
                <CheckCircle2 className="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                Completed Trips
              </span>
              <div className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white font-mono pt-0.5">
                {totalCompletedBookings}
              </div>
              <p className="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                Total finished reservations
              </p>
            </div>
          </div>

          {/* Card 3: Average Income per Trip */}
          <div className="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-xs relative overflow-hidden">
            <div className="space-y-0.5">
              <span className="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 flex items-center gap-1">
                <Layers className="w-3 h-3 text-teal-600 dark:text-teal-400" />
                Avg Income / Trip
              </span>
              <div className="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono pt-0.5">
                ₱{Number(avgIncomePerTrip).toLocaleString()}
              </div>
              <p className="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                Average yield per completed booking
              </p>
            </div>
          </div>
        </div>

        {/* Filter Toolbar */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs">
          <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
              {/* Filter by Month */}
              <div className="flex-1 min-w-[200px]">
                <label className="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1 flex items-center gap-1">
                  <Calendar className="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                  Filter by Month
                </label>
                <div className="relative">
                  <select
                    value={selectedMonth}
                    onChange={(e) => handleFilterChange(e.target.value, undefined)}
                    className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 text-xs font-extrabold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none transition-all cursor-pointer"
                  >
                    <option value="">All Months</option>
                    {availableMonths && availableMonths.length > 0 ? (
                      availableMonths.map((m) => (
                        <option key={m.month_val} value={m.month_val}>
                          {m.month_label}
                        </option>
                      ))
                    ) : (
                      <>
                        {/* Fallback month options */}
                        {Array.from({ length: 12 }).map((_, idx) => {
                          const d = new Date();
                          d.setMonth(d.getMonth() - idx);
                          const val = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                          const label = d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                          return (
                            <option key={val} value={val}>
                              {label}
                            </option>
                          );
                        })}
                      </>
                    )}
                  </select>
                </div>
              </div>

              {/* Filter by Vehicle */}
              <div className="flex-1 min-w-[200px]">
                <label className="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1 flex items-center gap-1">
                  <Car className="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                  Filter by Vehicle
                </label>
                <div className="relative">
                  <select
                    value={selectedVehicleId}
                    onChange={(e) => handleFilterChange(undefined, e.target.value)}
                    className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/80 text-xs font-extrabold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none transition-all cursor-pointer"
                  >
                    <option value="">All Vehicles</option>
                    {vehicles.map((v) => (
                      <option key={v.id} value={v.id}>
                        {v.name} ({v.license_plate})
                      </option>
                    ))}
                  </select>
                </div>
              </div>
            </div>

            {/* Clear / Reset Filters */}
            {(selectedMonth || selectedVehicleId) && (
              <div className="pt-2 md:pt-4 flex items-end">
                <button
                  type="button"
                  onClick={handleReset}
                  className="w-full md:w-auto px-4 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 text-gray-600 dark:text-gray-300 text-xs font-extrabold flex items-center justify-center gap-1.5 transition-colors border border-gray-200 dark:border-gray-700"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                  Reset Filter
                </button>
              </div>
            )}
          </div>
        </div>

        {/* Income Listing Section */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs">
          <div className="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-500" />
              <h2 className="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white">
                Income Records ({incomes?.total || 0})
              </h2>
            </div>
            <span className="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">
              Filtered Total: ₱{Number(totalIncome).toLocaleString()}
            </span>
          </div>

          {incomeItems.length === 0 ? (
            /* Empty State */
            <div className="p-12 text-center space-y-3">
              <div className="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-400 font-mono font-black text-2xl flex items-center justify-center mx-auto border border-gray-200 dark:border-gray-700 select-none">
                ₱
              </div>
              <h3 className="text-sm font-extrabold text-gray-900 dark:text-white">
                No completed income records found
              </h3>
              <p className="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                {selectedMonth || selectedVehicleId
                  ? 'No completed bookings match the selected month or vehicle filters. Try changing or resetting your filter.'
                  : 'Completed bookings with their actual income will appear here automatically.'}
              </p>
              {(selectedMonth || selectedVehicleId) && (
                <button
                  type="button"
                  onClick={handleReset}
                  className="mt-2 px-4 py-2 rounded-2xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-all inline-flex items-center gap-1.5 shadow-xs"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                  Clear Filters
                </button>
              )}
            </div>
          ) : (
            <>
              {/* Desktop Table View */}
              <div className="hidden md:block overflow-x-auto">
                <table className="w-full text-left text-xs">
                  <thead className="bg-gray-50/70 dark:bg-gray-800/50 text-gray-400 dark:text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-gray-100 dark:border-gray-800">
                    <tr>
                      <th className="py-3.5 px-4">Booking Code</th>
                      <th className="py-3.5 px-4">Assigned Vehicle</th>
                      <th className="py-3.5 px-4">Trip Period</th>
                      <th className="py-3.5 px-4">Customer</th>
                      <th className="py-3.5 px-4">Destination / Days</th>
                      <th className="py-3.5 px-4 text-right">Actual Income</th>
                      <th className="py-3.5 px-4 text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                    {incomeItems.map((booking) => {
                      const incAmount = getIncomeAmount(booking);
                      return (
                        <tr
                          key={booking.id}
                          className="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors group"
                        >
                          <td className="py-3.5 px-4 font-mono font-black text-gray-900 dark:text-white">
                            <Link
                              href={`/bookings/${booking.id}`}
                              className="text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1"
                            >
                              #{booking.booking_code}
                            </Link>
                          </td>

                          <td className="py-3.5 px-4">
                            <div className="font-bold text-gray-900 dark:text-white">
                              {booking.vehicle?.name || 'Standard Vehicle'}
                            </div>
                            <div className="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                              {booking.vehicle?.license_plate}
                            </div>
                          </td>

                          <td className="py-3.5 px-4 text-gray-600 dark:text-gray-300">
                            <div className="font-semibold">
                              {formatDate(booking.start_date)} &ndash; {formatDate(booking.end_date)}
                            </div>
                          </td>

                          <td className="py-3.5 px-4">
                            <div className="font-bold text-gray-800 dark:text-gray-200">
                              {booking.customer_name}
                            </div>
                            <div className="text-[10px] text-gray-400">
                              {booking.customer_phone}
                            </div>
                          </td>

                          <td className="py-3.5 px-4">
                            <div className="font-medium text-gray-800 dark:text-gray-200 flex items-center gap-1">
                              <MapPin className="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" />
                              <span className="truncate max-w-[150px]">{booking.destination || 'Standard Destination'}</span>
                            </div>
                            <div className="text-[10px] text-gray-400">
                              {booking.total_days} Day{booking.total_days > 1 ? 's' : ''}
                            </div>
                          </td>

                          <td className="py-3.5 px-4 text-right">
                            <div className="inline-flex flex-col items-end">
                              <span className="px-3 py-1 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-mono font-black text-sm">
                                ₱{Number(incAmount).toLocaleString()}
                              </span>
                              {booking.actual_income === null && (
                                <span className="text-[9px] text-gray-400 mt-0.5">from total bill</span>
                              )}
                            </div>
                          </td>

                          <td className="py-3.5 px-4 text-center">
                            <Link
                              href={`/bookings/${booking.id}`}
                              className="p-1.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-emerald-100 hover:text-emerald-700 dark:hover:bg-emerald-900/40 text-gray-600 dark:text-gray-300 inline-flex items-center justify-center transition-colors"
                              title="View Booking Details"
                            >
                              <Eye className="w-4 h-4" />
                            </Link>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>

              {/* Mobile Card List View (< 768px) */}
              <div className="md:hidden divide-y divide-gray-100 dark:divide-gray-800">
                {incomeItems.map((booking) => {
                  const incAmount = getIncomeAmount(booking);
                  return (
                    <div key={booking.id} className="p-4 space-y-3">
                      <div className="flex items-start justify-between gap-2">
                        <div>
                          <Link
                            href={`/bookings/${booking.id}`}
                            className="font-mono font-black text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1"
                          >
                            #{booking.booking_code}
                          </Link>
                          <h4 className="font-extrabold text-sm text-gray-900 dark:text-white mt-0.5">
                            {booking.vehicle?.name || 'Standard Vehicle'}
                          </h4>
                          <span className="text-[10px] font-mono font-bold text-gray-400">
                            {booking.vehicle?.license_plate}
                          </span>
                        </div>

                        <div className="text-right">
                          <span className="px-3 py-1 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-mono font-black text-sm inline-block">
                            ₱{Number(incAmount).toLocaleString()}
                          </span>
                        </div>
                      </div>

                      <div className="grid grid-cols-2 gap-2 text-[11px] bg-gray-50 dark:bg-gray-800/40 p-2.5 rounded-2xl border border-gray-100 dark:border-gray-800/60">
                        <div>
                          <span className="text-[10px] text-gray-400 uppercase font-bold block">Period</span>
                          <span className="font-semibold text-gray-800 dark:text-gray-200">
                            {formatDate(booking.start_date)} &ndash; {formatDate(booking.end_date)}
                          </span>
                        </div>
                        <div>
                          <span className="text-[10px] text-gray-400 uppercase font-bold block">Customer</span>
                          <span className="font-bold text-gray-800 dark:text-gray-200 truncate block">
                            {booking.customer_name}
                          </span>
                        </div>
                      </div>

                      <div className="flex items-center justify-between text-xs pt-1">
                        <span className="text-gray-500 dark:text-gray-400 flex items-center gap-1 text-[11px]">
                          <MapPin className="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" />
                          <span className="truncate max-w-[180px]">{booking.destination || 'Standard'} ({booking.total_days}d)</span>
                        </span>

                        <Link
                          href={`/bookings/${booking.id}`}
                          className="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1"
                        >
                          View Details
                          <ArrowRight className="w-3 h-3" />
                        </Link>
                      </div>
                    </div>
                  );
                })}
              </div>

              {/* Pagination Controls */}
              {incomes?.links && incomes.links.length > 3 && (
                <div className="p-4 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                  <span className="text-gray-500 dark:text-gray-400">
                    Showing <span className="font-bold text-gray-800 dark:text-gray-200">{incomes.from || 0}</span> to{' '}
                    <span className="font-bold text-gray-800 dark:text-gray-200">{incomes.to || 0}</span> of{' '}
                    <span className="font-bold text-gray-800 dark:text-gray-200">{incomes.total}</span> records
                  </span>

                  <div className="flex items-center gap-1">
                    {incomes.links.map((link, idx) => {
                      if (!link.url) {
                        return (
                          <span
                            key={idx}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                            className="px-3 py-1.5 rounded-xl text-gray-400 dark:text-gray-600 text-xs border border-transparent select-none opacity-50"
                          />
                        );
                      }

                      return (
                        <Link
                          key={idx}
                          href={link.url}
                          dangerouslySetInnerHTML={{ __html: link.label }}
                          className={`px-3 py-1.5 rounded-xl text-xs font-bold transition-all ${link.active
                              ? 'bg-emerald-600 text-white shadow-xs'
                              : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                            }`}
                        />
                      );
                    })}
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
