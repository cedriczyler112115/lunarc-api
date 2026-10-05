import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  CheckCircle2,
  Clock,
  Plus,
  ArrowRight,
  TrendingUp,
  Calendar,
  Eye,
  MapPin,
  Phone
} from 'lucide-react';

export default function Dashboard({
  totalVehicles,
  availableVehicles,
  activeBookings,
  totalRevenue,
  recentBookings = [],
  vehicles = [],
  myVehicles = []
}) {
  const [loadingToggleId, setLoadingToggleId] = useState(null);

  const handleToggleAvailability = (vehicleId, currentStatus) => {
    setLoadingToggleId(vehicleId);
    router.patch(
      `/vehicles/${vehicleId}/toggle-availability`,
      {},
      {
        preserveScroll: true,
        onFinish: () => setLoadingToggleId(null),
      }
    );
  };

  const getStatusBadge = (status) => {
    switch (status) {
      case 'confirmed':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Confirmed</span>;
      case 'pending':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Pending</span>;
      case 'completed':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Completed</span>;
      case 'cancelled':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Cancelled</span>;
      default:
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">{status}</span>;
    }
  };

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr.includes('T') ? dateStr : `${dateStr}T00:00:00`);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  const formatTime = (timeStr) => {
    if (!timeStr) return '';
    if (/am|pm/i.test(timeStr)) return timeStr;
    const parts = timeStr.split(':');
    if (parts.length >= 2) {
      const h = parseInt(parts[0], 10);
      const m = parts[1].padStart(2, '0');
      const ampm = h >= 12 ? 'PM' : 'AM';
      const formattedHour = h % 12 === 0 ? 12 : h % 12;
      return `${formattedHour}:${m} ${ampm}`;
    }
    return timeStr;
  };

  return (
    <AppLayout title="Dashboard">
      <Head title="Dashboard" />

      <div className="space-y-6 pb-6">
        {/* Welcome Banner */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-6 rounded-3xl shadow-xs transition-colors duration-300">
          <div className="space-y-1">
            <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
              Rental Dashboard
            </h1>
            <p className="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
              Overview of your fleet performance, bookings, and revenue
            </p>
          </div>

          <div className="flex items-center gap-3">
            <Link
              href="/bookings/create"
              className="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-95 transition-transform"
            >
              <Plus className="w-4 h-4" />
              New Booking
            </Link>
            <Link
              href="/vehicles/create"
              className="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-transform"
            >
              <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Add Vehicle
            </Link>
          </div>
        </div>

        {/* Top Metric Cards */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available Fleet</span>
              <div className="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                <CheckCircle2 className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{availableVehicles}</span>
              <span className="text-xs text-gray-500 font-semibold">of {totalVehicles} total</span>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Bookings</span>
              <div className="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200 dark:border-indigo-800">
                <Clock className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">{activeBookings}</span>
              <span className="text-xs text-indigo-600 dark:text-indigo-400 font-semibold">Active Trips</span>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Revenue</span>
              <div className="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-200 dark:border-amber-800">
                <TrendingUp className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">
                ₱{Number(totalRevenue || 0).toLocaleString()}
              </span>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Fleet</span>
              <div className="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-950/70 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-200 dark:border-violet-800">
                <Car className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{totalVehicles}</span>
              <Link href="/vehicles" className="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-bold flex items-center gap-1">
                View fleet <ArrowRight className="w-3 h-3" />
              </Link>
            </div>
          </div>
        </div>

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Recent Reservations */}
          <div className="lg:col-span-2 space-y-4">
            <div className="flex items-center justify-between px-1">
              <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Calendar className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                Recent Reservations
              </h2>
              <Link href="/bookings" className="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                View all <ArrowRight className="w-3 h-3" />
              </Link>
            </div>

            <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs transition-colors">
              {recentBookings.length === 0 ? (
                <div className="p-8 text-center space-y-3">
                  <Calendar className="w-10 h-10 text-gray-400 dark:text-gray-600 mx-auto" />
                  <p className="text-xs text-gray-500 dark:text-gray-400">No recent bookings recorded yet.</p>
                  <Link
                    href="/bookings/create"
                    className="inline-flex px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold"
                  >
                    Create First Booking
                  </Link>
                </div>
              ) : (
                <div className="divide-y divide-gray-100 dark:divide-gray-800">
                  {recentBookings.map((b) => (
                    <div key={b.id} className="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors flex items-start justify-between gap-4">
                      <div className="flex items-start gap-3 min-w-0 flex-1">
                        <div className="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                          <Car className="w-5 h-5" />
                        </div>
                        <div className="min-w-0 flex-1 space-y-1">
                          {/* 1. Plate Number & Booking Reference */}
                          <div className="flex flex-wrap items-center gap-2 mb-[5px]">
                            <span className="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-300 break-all">
                              #{b.booking_code}
                            </span>
                            {b.vehicle?.license_plate && (
                              <span className="px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/70 text-amber-900 dark:text-amber-200 font-mono text-[11px] font-black border border-amber-300 dark:border-amber-800 shrink-0">
                                🚘 {b.vehicle.license_plate}
                              </span>
                            )}
                          </div>

                          {/* 2. Renter Name */}
                          <p className="text-sm font-extrabold text-gray-900 dark:text-white whitespace-nowrap">
                            {b.customer_name}
                          </p>

                          {/* 3. Contact Number */}
                          {b.customer_phone && (
                            <p className="text-xs font-bold text-sky-600 dark:text-sky-400 flex items-center gap-1 whitespace-nowrap">
                              <Phone className="w-3.5 h-3.5 shrink-0" />
                              <span>{b.customer_phone}</span>
                            </p>
                          )}

                          {/* 4. Vehicle Name */}
                          {b.vehicle && (
                            <p className="text-xs font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                              🚘 {b.vehicle.name || `${b.vehicle.make} ${b.vehicle.model}`}
                            </p>
                          )}

                          {/* 5. Destination */}
                          {b.destination && (
                            <p className="text-[11px] text-gray-500 dark:text-gray-400 whitespace-nowrap flex items-center gap-1">
                              <MapPin className="w-3 h-3 text-gray-400 shrink-0" />
                              <span>{b.destination}</span>
                            </p>
                          )}
                        </div>
                      </div>

                      {/* Right Column */}
                      <div className="text-right shrink-0 space-y-1.5">
                        {/* Price */}
                        <p className="text-base font-black text-amber-600 dark:text-amber-400">
                          ₱{Number(b.total_price || 0).toLocaleString()}
                        </p>

                        {/* Pickup & Return Schedule */}
                        <div className="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5 text-right">
                          <p className="flex items-center justify-end gap-1 whitespace-nowrap">
                            <span className="font-semibold text-gray-600 dark:text-gray-300">Pickup:</span>
                            <span className="font-bold text-gray-900 dark:text-white">{formatDate(b.start_date)}</span>
                            <span className="font-bold text-emerald-600 dark:text-emerald-400 ml-0.5">
                              ({formatTime(b.pickup_time || '00:00')})
                            </span>
                          </p>
                          <p className="flex items-center justify-end gap-1 whitespace-nowrap">
                            <span className="font-semibold text-gray-600 dark:text-gray-300">Return:</span>
                            <span className="font-bold text-gray-900 dark:text-white">{formatDate(b.end_date)}</span>
                            <span className="font-bold text-rose-600 dark:text-rose-400 ml-0.5">
                              ({formatTime(b.return_time || '00:00')})
                            </span>
                          </p>
                        </div>

                        {/* Status Pill & Number of Days on Right Side */}
                        <div className="flex items-center justify-end gap-2 pt-1">
                          {b.total_days && (
                            <span className="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-800 dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800 shrink-0">
                              {b.total_days} {b.total_days === 1 ? 'Day' : 'Days'}
                            </span>
                          )}
                          <div>
                            {getStatusBadge(b.status)}
                          </div>
                        </div>

                        {/* Details Link - Very Last on Right */}
                        <div className="pt-1">
                          <Link
                            href={`/bookings/${b.id}`}
                            className="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                          >
                            Details <Eye className="w-3 h-3" />
                          </Link>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* Fleet Availability Sidebar */}
          <div className="space-y-4">
            <div className="flex items-center justify-between px-1">
              <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                My Fleet Availability
              </h2>
              <Link href="/vehicles" className="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                Manage
              </Link>
            </div>

            <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 shadow-xs space-y-3 transition-colors">
              {myVehicles.length === 0 ? (
                <div className="p-6 text-center text-xs text-gray-500 dark:text-gray-400 space-y-2">
                  <p>No vehicles added yet.</p>
                  <Link href="/vehicles/create" className="inline-block px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold">
                    Add Vehicle
                  </Link>
                </div>
              ) : (
                myVehicles.slice(0, 5).map((v) => {
                  const isAvailable = v.status === 'available';
                  const isLoading = loadingToggleId === v.id;
                  return (
                    <div
                      key={v.id}
                      className="p-3 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800 flex items-center justify-between gap-3"
                    >
                      <div className="flex items-center gap-3 min-w-0">
                        <div className="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-800 overflow-hidden shrink-0 border border-gray-300 dark:border-gray-700">
                          {v.image_path ? (
                            <img src={`/${v.image_path}`} alt={v.name} className="w-full h-full object-cover" />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center text-gray-500">
                              <Car className="w-5 h-5" />
                            </div>
                          )}
                        </div>
                        <div className="min-w-0">
                          <p className="text-xs font-bold text-gray-900 dark:text-white truncate">{v.name}</p>
                          <p className="text-[10px] text-gray-500 dark:text-gray-400 font-mono">{v.license_plate}</p>
                        </div>
                      </div>

                      {/* Interactive Toggle Switch */}
                      <button
                        onClick={() => handleToggleAvailability(v.id, v.status)}
                        disabled={isLoading}
                        className={`relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${
                          isAvailable ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-700'
                        }`}
                        title={isAvailable ? 'Status: Available' : 'Status: Out of Service'}
                      >
                        <span
                          className={`pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${
                            isAvailable ? 'translate-x-5' : 'translate-x-0'
                          }`}
                        />
                      </button>
                    </div>
                  );
                })
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
