import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar,
  Search,
  Car,
  MapPin,
  Eye,
  Trash2,
  Phone,
  FileText,
  X,
  RotateCcw,
  Clock,
  User,
  Edit
} from 'lucide-react';
import confirmDialog from '@/Utils/confirm';

export default function Index({ bookings, vehicles = [], filters = {} }) {
  const [search, setSearch] = useState(filters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(filters.status || '');
  const [selectedVehicleId, setSelectedVehicleId] = useState(filters.vehicle_id || '');

  const bookingItems = bookings?.data || [];

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

  const formatTime = (timeStr) => {
    if (!timeStr) return '';
    if (/am|pm/i.test(timeStr)) return timeStr;
    const parts = String(timeStr).split(':');
    if (parts.length < 2) return timeStr;
    let hour = parseInt(parts[0], 10);
    const minute = parts[1].padStart(2, '0');
    if (isNaN(hour)) return timeStr;
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const formattedHour = hour % 12 === 0 ? 12 : hour % 12;
    return `${formattedHour}:${minute} ${ampm}`;
  };

  const handleFilter = (updates = {}) => {
    const nextSearch = updates.search !== undefined ? updates.search : search;
    const nextStatus = updates.status !== undefined ? updates.status : selectedStatus;
    const nextVehicleId = updates.vehicle_id !== undefined ? updates.vehicle_id : selectedVehicleId;

    if (updates.search !== undefined) setSearch(updates.search);
    if (updates.status !== undefined) setSelectedStatus(updates.status);
    if (updates.vehicle_id !== undefined) setSelectedVehicleId(updates.vehicle_id);

    const query = {};
    if (nextSearch) query.search = nextSearch;
    if (nextStatus) query.status = nextStatus;
    if (nextVehicleId) query.vehicle_id = nextVehicleId;

    router.get('/bookings', query, { preserveState: true, replace: true });
  };

  const handleClear = () => {
    setSearch('');
    setSelectedStatus('');
    setSelectedVehicleId('');
    router.get('/bookings', {}, { preserveState: true, replace: true });
  };

  const getStatusBadge = (status) => {
    switch (status) {
      case 'confirmed':
        return <span className="px-3 py-1 text-xs font-bold uppercase rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Confirmed</span>;
      case 'pending':
        return <span className="px-3 py-1 text-xs font-bold uppercase rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Pending</span>;
      case 'completed':
        return <span className="px-3 py-1 text-xs font-bold uppercase rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">Completed</span>;
      case 'cancelled':
        return <span className="px-3 py-1 text-xs font-bold uppercase rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">Cancelled</span>;
      default:
        return <span className="px-3 py-1 text-xs font-bold uppercase rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">{status}</span>;
    }
  };

  const handleDelete = (id, code) => {
    confirmDialog({
      title: 'Delete Booking',
      content: `Are you sure you want to permanently delete reservation <strong>#${code}</strong>?`,
      type: 'red',
      confirmButtonText: 'Delete Booking',
      confirmButtonClass: 'btn-red',
      onConfirm: () => {
        router.delete(`/bookings/${id}`);
      },
    });
  };

  const hasFilters = Boolean(search || selectedStatus || selectedVehicleId);

  return (
    <AppLayout title="My Bookings">
      <Head title="My Bookings" />

      <div className="space-y-6 pb-6">
        {/* Header */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
          <div>
            <h2 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <Calendar className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
              My Bookings
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              View customer reservations, confirmed schedules, and status tracking.
            </p>
          </div>

          <div className="flex items-center gap-3">
            <Link
              href="/calendar"
              className="inline-flex items-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl shadow-xs transition duration-150"
            >
              <Calendar className="w-5 h-5 mr-1.5" />
              Booking Calendar
            </Link>
          </div>
        </div>

        {/* Search and Filter Bar */}
        <div className="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Search Customer / Code</label>
              <div className="relative">
                <input
                  type="text"
                  value={search}
                  onChange={(e) => {
                    const val = e.target.value;
                    setSearch(val);
                  }}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter') {
                      e.preventDefault();
                      handleFilter({ search });
                    }
                  }}
                  placeholder="Type & press Enter to search..."
                  className="w-full text-sm pl-9 pr-8 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                />
                <Search className="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                {search && (
                  <button
                    type="button"
                    onClick={() => {
                      setSearch('');
                      handleFilter({ search: '' });
                    }}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    title="Clear search"
                  >
                    <X className="w-3.5 h-3.5" />
                  </button>
                )}
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Filter Vehicle</label>
              <select
                value={selectedVehicleId}
                onChange={(e) => {
                  const val = e.target.value;
                  setSelectedVehicleId(val);
                  handleFilter({ vehicle_id: val });
                }}
                className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Vehicles</option>
                {vehicles.map((v) => (
                  <option key={v.id} value={v.id}>
                    {v.name} ({v.license_plate})
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Filter Status</label>
              <select
                value={selectedStatus}
                onChange={(e) => {
                  const val = e.target.value;
                  setSelectedStatus(val);
                  handleFilter({ status: val });
                }}
                className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Statuses</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          {hasFilters && (
            <div className="flex items-center justify-between pt-1 border-t border-gray-100 dark:border-gray-700/60">
              <span className="text-xs text-gray-500 dark:text-gray-400">
                Filters active
              </span>
              <button
                type="button"
                onClick={handleClear}
                className="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 transition-colors"
              >
                <RotateCcw className="w-3.5 h-3.5" />
                Reset all filters
              </button>
            </div>
          )}
        </div>

        {/* Desktop Table View */}
        <div className="hidden md:block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
          <table className="w-full text-left text-sm text-gray-600 dark:text-gray-300">
            <thead className="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
              <tr>
                <th className="px-4 py-3.5 whitespace-nowrap">Booking Code</th>
                <th className="px-4 py-3.5">Booked Vehicle</th>
                <th className="px-4 py-3.5">Customer Info</th>
                <th className="px-4 py-3.5 whitespace-nowrap">Pickup & Return</th>
                <th className="px-3 py-3.5 whitespace-nowrap w-24 text-center">Total Amount</th>
                <th className="px-3 py-3.5 whitespace-nowrap w-20 text-center">Status</th>
                <th className="px-3 py-3.5 text-center whitespace-nowrap w-12">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
              {bookingItems.length === 0 ? (
                <tr>
                  <td colSpan="7" className="px-6 py-12 text-center text-gray-400">
                    No bookings found matching your search.
                  </td>
                </tr>
              ) : (
                bookingItems.map((b) => (
                  <tr key={b.id} className="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                    <td className="px-4 py-3.5 whitespace-nowrap">
                      <Link href={`/bookings/${b.id}`} className="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        #{b.booking_code}
                      </Link>
                      <div className="font-sans font-normal text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Booked by:<br />
                        <span className="font-medium text-gray-700 dark:text-gray-300">{b.user?.name || b.customer_name || 'Guest'}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3.5">
                      <div className="font-bold text-gray-900 dark:text-white leading-snug">{b.vehicle?.name || 'Deleted Vehicle'}</div>
                      <div className="text-xs text-gray-400 mt-0.5">{b.vehicle?.license_plate || ''} • ₱{Number(b.daily_rate || 0).toFixed(2)}/day</div>
                    </td>
                    <td className="px-4 py-3.5">
                      <div className="font-bold text-gray-900 dark:text-white leading-snug">{b.customer_name}</div>
                      <div className="text-xs text-gray-400 mt-0.5">{b.customer_phone}</div>
                      {b.destination && (
                        <div className="mt-1 text-[11px] font-bold text-sky-600 dark:text-sky-400 leading-tight">
                          <span>📍 {b.destination}</span><br />
                          {Number(b.destination_rate) > 0 && (
                            <span className="text-[10px] text-gray-500 dark:text-gray-400 font-normal">
                              (₱{Number(b.destination_rate).toLocaleString()} / day)
                            </span>
                          )}
                        </div>
                      )}
                    </td>
                    <td className="px-4 py-3.5 whitespace-nowrap">
                      <div className="space-y-1.5">
                        <div className="text-xs">
                          <span className="font-bold text-gray-400 dark:text-gray-500 uppercase text-[10px] block tracking-wide">Pickup</span>
                          <div className="flex items-center gap-1.5 font-semibold text-gray-800 dark:text-gray-200">
                            <span>{formatDate(b.start_date)}</span>
                            {b.pickup_time && (
                              <span className="text-indigo-600 dark:text-indigo-400 font-bold text-xs bg-indigo-50 dark:bg-indigo-950/60 px-1.5 py-0.5 rounded border border-indigo-100 dark:border-indigo-900/40">
                                {formatTime(b.pickup_time)}
                              </span>
                            )}
                          </div>
                        </div>
                        <div className="text-xs">
                          <span className="font-bold text-gray-400 dark:text-gray-500 uppercase text-[10px] block tracking-wide">Return</span>
                          <div className="flex items-center gap-1.5 font-semibold text-gray-800 dark:text-gray-200">
                            <span>{formatDate(b.end_date)}</span>
                            {b.return_time && (
                              <span className="text-indigo-600 dark:text-indigo-400 font-bold text-xs bg-indigo-50 dark:bg-indigo-950/60 px-1.5 py-0.5 rounded border border-indigo-100 dark:border-indigo-900/40">
                                {formatTime(b.return_time)}
                              </span>
                            )}
                          </div>
                        </div>
                        <div className="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold pt-0.5">
                          {b.total_days} {b.total_days === 1 ? 'Day' : 'Days'}
                        </div>
                      </div>
                    </td>
                    <td className="px-3 py-3.5 whitespace-nowrap w-24 text-center">
                      <div className="font-extrabold text-sm text-gray-900 dark:text-white">
                        ₱{Number(b.total_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                      </div>
                      {b.actual_income !== null && b.actual_income !== undefined && (
                        <div className="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                          Actual Income:<br />
                          <span>₱{Number(b.actual_income).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                      )}
                    </td>
                    <td className="px-3 py-3.5 whitespace-nowrap w-20 text-center">
                      {getStatusBadge(b.status)}
                    </td>
                    <td className="px-3 py-3.5 text-center whitespace-nowrap w-12">
                      <div className="flex flex-col items-center justify-center gap-1.5">
                        <Link
                          href={`/bookings/${b.id}`}
                          className="p-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 rounded-lg transition inline-flex items-center justify-center shadow-xs"
                          title="View Details"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          href={`/bookings/${b.id}/edit`}
                          className="p-1.5 bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-950/60 dark:text-amber-300 rounded-lg transition inline-flex items-center justify-center shadow-xs"
                          title="Edit Booking"
                        >
                          <Edit className="w-4 h-4" />
                        </Link>
                        <button
                          type="button"
                          onClick={() => handleDelete(b.id, b.booking_code)}
                          className="p-1.5 bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-950/60 dark:text-rose-300 rounded-lg transition inline-flex items-center justify-center shadow-xs"
                          title="Delete Booking"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Mobile Card List View */}
        <div className="block md:hidden space-y-4">
          {bookingItems.length === 0 ? (
            <div className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-8 text-center text-gray-400 text-sm shadow-xs">
              No bookings found matching your search.
            </div>
          ) : (
            bookingItems.map((b) => (
              <div
                key={b.id}
                className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/80 p-4 space-y-3.5 shadow-sm hover:shadow-md transition-shadow"
              >
                {/* Header: Code & Status */}
                <div className="flex items-start justify-between pb-2 border-b border-gray-100 dark:border-gray-700/60 gap-2">
                  <div>
                    <Link
                      href={`/bookings/${b.id}`}
                      className="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 px-2.5 py-1 rounded-lg hover:underline inline-flex items-center gap-1"
                    >
                      #{b.booking_code}
                    </Link>
                    <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                      Booked by:<br />
                      <span className="font-semibold text-gray-700 dark:text-gray-300">{b.user?.name || b.customer_name || 'Guest'}</span>
                    </div>
                  </div>
                  {getStatusBadge(b.status)}
                </div>

                {/* Vehicle & Customer Row */}
                <div className="flex items-start gap-3">
                  {b.vehicle?.image_path ? (
                    <img
                      src={b.vehicle.image_path.startsWith('http') ? b.vehicle.image_path : `/${b.vehicle.image_path}`}
                      alt={b.vehicle.name}
                      className="w-16 h-16 rounded-xl object-cover border border-gray-200 dark:border-gray-700 shrink-0"
                    />
                  ) : (
                    <div className="w-16 h-16 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 shrink-0">
                      <Car className="w-7 h-7 opacity-60" />
                    </div>
                  )}

                  <div className="flex-1 min-w-0 space-y-1">
                    <h3 className="font-extrabold text-sm text-gray-900 dark:text-white truncate">
                      {b.vehicle?.name || 'Deleted Vehicle'}
                    </h3>
                    <div className="flex flex-wrap items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                      {b.vehicle?.license_plate && (
                        <span className="font-mono font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 px-1.5 py-0.5 rounded text-[11px] border border-amber-200/60 dark:border-amber-800/40">
                          {b.vehicle.license_plate}
                        </span>
                      )}
                      <span className="text-[11px]">₱{Number(b.daily_rate || 0).toFixed(2)}/day</span>
                    </div>

                    <div className="pt-0.5">
                      <p className="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate flex items-center gap-1">
                        <User className="w-3.5 h-3.5 text-gray-400 shrink-0" />
                        {b.customer_name}
                      </p>
                      {b.customer_phone && (
                        <p className="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                          <Phone className="w-3 h-3 text-gray-400 shrink-0" />
                          <a href={`tel:${b.customer_phone}`} className="hover:underline">
                            {b.customer_phone}
                          </a>
                        </p>
                      )}
                    </div>
                  </div>
                </div>

                {/* Destination Badge (if present) */}
                {b.destination && (
                  <div className="text-xs font-semibold text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/40 px-3 py-1.5 rounded-xl border border-sky-100 dark:border-sky-900/50">
                    <div className="flex items-center gap-1.5">
                      <MapPin className="w-3.5 h-3.5 text-sky-600 dark:text-sky-400 shrink-0" />
                      <span className="truncate">{b.destination}</span>
                    </div>
                    {Number(b.destination_rate) > 0 && (
                      <div className="text-[10px] text-gray-500 dark:text-gray-400 font-normal pl-5 mt-0.5">
                        (₱{Number(b.destination_rate).toLocaleString()} / day)
                      </div>
                    )}
                  </div>
                )}

                {/* Pickup & Return Schedule Box */}
                <div className="bg-gray-50 dark:bg-gray-900/60 p-3 rounded-xl border border-gray-100 dark:border-gray-700/60 space-y-2">
                  <div className="grid grid-cols-2 gap-3 text-xs">
                    <div>
                      <span className="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">
                        Pickup Date & Time
                      </span>
                      <p className="font-bold text-gray-900 dark:text-white mt-0.5">{formatDate(b.start_date)}</p>
                      {b.pickup_time ? (
                        <p className="text-xs font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 mt-0.5">
                          <Clock className="w-3 h-3" />
                          {formatTime(b.pickup_time)}
                        </p>
                      ) : null}
                    </div>

                    <div>
                      <span className="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">
                        Return Date & Time
                      </span>
                      <p className="font-bold text-gray-900 dark:text-white mt-0.5">{formatDate(b.end_date)}</p>
                      {b.return_time ? (
                        <p className="text-xs font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 mt-0.5">
                          <Clock className="w-3 h-3" />
                          {formatTime(b.return_time)}
                        </p>
                      ) : null}
                    </div>
                  </div>

                  <div className="flex items-center justify-between pt-2 border-t border-gray-200/60 dark:border-gray-800">
                    <span className="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                      {b.total_days} {b.total_days === 1 ? 'Day' : 'Days'}
                    </span>
                    <div className="text-right">
                      <div>
                        <span className="text-[10px] text-gray-400 uppercase font-semibold mr-1.5">Total:</span>
                        <span className="font-black text-emerald-600 dark:text-emerald-400 text-base">
                          ₱{Number(b.total_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </span>
                      </div>
                      {b.actual_income !== null && b.actual_income !== undefined && (
                        <div className="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                          Actual Income:<br />
                          <span>₱{Number(b.actual_income).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                      )}
                    </div>
                  </div>
                </div>

                {/* Actions */}
                <div className="pt-1 flex items-center gap-2">
                  <Link
                    href={`/bookings/${b.id}`}
                    className="flex-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl transition inline-flex items-center justify-center shadow-xs"
                    title="View Details"
                  >
                    <Eye className="w-4 h-4" />
                  </Link>
                  <Link
                    href={`/bookings/${b.id}/edit`}
                    className="py-2 px-3 bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/60 dark:hover:bg-amber-900/80 text-amber-800 dark:text-amber-300 rounded-xl transition inline-flex items-center justify-center shadow-xs"
                    title="Edit Booking"
                  >
                    <Edit className="w-4 h-4" />
                  </Link>
                  <button
                    type="button"
                    onClick={() => handleDelete(b.id, b.booking_code)}
                    className="py-2 px-3 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-700 dark:text-rose-300 rounded-xl transition inline-flex items-center justify-center shadow-xs"
                    title="Delete Booking"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </AppLayout>
  );
}
