import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar,
  Plus,
  Search,
  Car,
  MapPin,
  Eye,
  Trash2,
  Phone,
  FileText,
  X,
  RotateCcw
} from 'lucide-react';
import confirmDialog from '@/Utils/confirm';

export default function Index({ bookings, vehicles = [], filters = {} }) {
  const [search, setSearch] = useState(filters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(filters.status || '');
  const [selectedVehicleId, setSelectedVehicleId] = useState(filters.vehicle_id || '');

  const bookingItems = bookings?.data || [];

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const [y, m, d] = String(dateStr).slice(0, 10).split('-').map(Number);
    if (!y || !m || !d) return dateStr;
    return new Date(y, m - 1, d).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    });
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
            <Link
              href="/bookings/create"
              className="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150"
            >
              <Plus className="w-5 h-5 mr-1.5" />
              + New Car Booking
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
                <option value="pending">Pending</option>
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

        {/* Bookings Table (Desktop) / Cards (Mobile) */}
        <div className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
          {/* Desktop Table View */}
          <div className="hidden md:block overflow-x-auto">
            <table className="w-full text-left text-sm text-gray-600 dark:text-gray-300">
              <thead className="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                <tr>
                  <th className="px-6 py-4">Booking Code</th>
                  <th className="px-6 py-4">Booked Vehicle</th>
                  <th className="px-6 py-4">Customer Info</th>
                  <th className="px-6 py-4">Dates & Duration</th>
                  <th className="px-6 py-4">Total Amount</th>
                  <th className="px-6 py-4">Status</th>
                  <th className="px-6 py-4 text-right">Actions</th>
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
                      <td className="px-6 py-4 font-mono font-bold text-gray-900 dark:text-white">
                        <Link href={`/bookings/${b.id}`} className="text-indigo-600 dark:text-indigo-400 hover:underline">
                          #{b.booking_code}
                        </Link>
                      </td>
                      <td className="px-6 py-4">
                        <div className="font-bold text-gray-900 dark:text-white">{b.vehicle?.name || 'Deleted Vehicle'}</div>
                        <div className="text-xs text-gray-400">{b.vehicle?.license_plate || ''} • ₱{Number(b.daily_rate || 0).toFixed(2)}/day</div>
                      </td>
                      <td className="px-6 py-4">
                        <div className="font-bold text-gray-900 dark:text-white">{b.customer_name}</div>
                        <div className="text-xs text-gray-400">{b.customer_phone}</div>
                        {b.destination && (
                          <div className="mt-1 text-[11px] font-bold text-sky-600 dark:text-sky-400 flex items-center gap-1">
                            <span>📍 {b.destination}</span>
                            {Number(b.destination_rate) > 0 && (
                              <span className="text-[10px] text-gray-400 font-normal">(+₱{Number(b.destination_rate).toFixed(2)})</span>
                            )}
                          </div>
                        )}
                      </td>
                      <td className="px-6 py-4">
                        <div className="font-semibold text-gray-800 dark:text-gray-200">
                          {formatDate(b.start_date)} — {formatDate(b.end_date)}
                        </div>
                        <div className="text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                          {b.total_days} {b.total_days === 1 ? 'Day' : 'Days'}
                        </div>
                      </td>
                      <td className="px-6 py-4 font-extrabold text-base text-gray-900 dark:text-white">
                        ₱{Number(b.total_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                      </td>
                      <td className="px-6 py-4">
                        {getStatusBadge(b.status)}
                      </td>
                      <td className="px-6 py-4 text-right whitespace-nowrap">
                        <div className="flex items-center justify-end gap-1.5">
                          <Link
                            href={`/bookings/${b.id}`}
                            className="px-2.5 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg transition inline-flex items-center gap-1"
                          >
                            <Eye className="w-3.5 h-3.5" />
                            View
                          </Link>
                          <Link
                            href={`/bookings/${b.id}/edit`}
                            className="px-2.5 py-1.5 bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-950/60 dark:text-amber-300 text-xs font-bold rounded-lg transition inline-flex items-center gap-1"
                          >
                            Edit
                          </Link>
                          <button
                            type="button"
                            onClick={() => handleDelete(b.id, b.booking_code)}
                            className="px-2.5 py-1.5 bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-950/60 dark:text-rose-300 text-xs font-bold rounded-lg transition inline-flex items-center gap-1"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                            Delete
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
          <div className="block md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            {bookingItems.length === 0 ? (
              <div className="p-8 text-center text-gray-400 text-sm">
                No bookings found matching your search.
              </div>
            ) : (
              bookingItems.map((b) => (
                <div key={b.id} className="p-4 space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 px-2 py-0.5 rounded">
                      #{b.booking_code}
                    </span>
                    {getStatusBadge(b.status)}
                  </div>

                  <div>
                    <h3 className="font-extrabold text-base text-gray-900 dark:text-white">{b.customer_name}</h3>
                    <p className="text-xs text-gray-500 dark:text-gray-400">{b.customer_phone}</p>
                    <p className="text-xs font-bold text-gray-800 dark:text-gray-200 mt-1">Vehicle: {b.vehicle?.name} ({b.vehicle?.license_plate})</p>
                  </div>

                  <div className="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700/80 pt-2">
                    <div>
                      <span>Schedule:</span>
                      <p className="font-bold text-gray-800 dark:text-gray-200">{formatDate(b.start_date)} to {formatDate(b.end_date)}</p>
                    </div>
                    <div className="text-right">
                      <span>Total:</span>
                      <p className="font-black text-emerald-600 dark:text-emerald-400 text-sm">₱{Number(b.total_price || 0).toLocaleString()}</p>
                    </div>
                  </div>

                  <div className="pt-2 flex items-center justify-end gap-2">
                    <Link
                      href={`/bookings/${b.id}`}
                      className="flex-1 text-center py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold rounded-xl"
                    >
                      View Details
                    </Link>
                    <Link
                      href={`/bookings/${b.id}/edit`}
                      className="px-3 py-2 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-xs font-bold rounded-xl"
                    >
                      Edit
                    </Link>
                    <button
                      type="button"
                      onClick={() => handleDelete(b.id, b.booking_code)}
                      className="px-3 py-2 bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-xl"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
