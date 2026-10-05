import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar,
  Car,
  MapPin,
  Clock,
  ArrowLeft,
  Printer,
  CheckCircle2,
  Phone,
  User,
  ShieldCheck,
  Edit,
  DollarSign
} from 'lucide-react';

export default function Show({ booking }) {
  const vehicle = booking.vehicle;

  const handlePrint = () => {
    window.print();
  };

  const handleStatusChange = (newStatus) => {
    if (confirm(`Change booking status to ${newStatus}?`)) {
      router.patch(`/bookings/${booking.id}`, { status: newStatus });
    }
  };

  return (
    <AppLayout title={`Booking #${booking.booking_code}`}>
      <Head title={`Booking #${booking.booking_code}`} />

      <div className="max-w-2xl mx-auto space-y-6 pb-6">
        <div className="flex items-center justify-between print:hidden">
          <Link
            href="/bookings"
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Bookings
          </Link>

          <button
            onClick={handlePrint}
            className="px-4 py-2 rounded-2xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold flex items-center gap-2 border border-gray-200 dark:border-gray-700 shadow-sm"
          >
            <Printer className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Print Ticket / Voucher
          </button>
        </div>

        {/* Digital Mobile Voucher Ticket */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs relative transition-colors duration-300">
          {/* Ticket Header Gradient */}
          <div className="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 p-6 sm:p-8 text-white flex items-center justify-between">
            <div className="space-y-1">
              <span className="text-[10px] font-black uppercase tracking-widest text-emerald-100">Official Rental Voucher</span>
              <h1 className="text-2xl font-black text-white font-mono">#{booking.booking_code}</h1>
              <p className="text-xs text-emerald-100">OneDrive Wheels Fleet Operations</p>
            </div>

            <div className="text-right">
              <span
                className={`px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider ${
                  booking.status === 'confirmed'
                    ? 'bg-white/20 text-white border border-white/30'
                    : 'bg-amber-500/30 text-white border border-amber-300/40'
                }`}
              >
                {booking.status}
              </span>
            </div>
          </div>

          {/* Ticket Body */}
          <div className="p-6 sm:p-8 space-y-6">
            {/* Customer & Vehicle Band */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-950/60 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800">
              <div className="space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Customer Name</span>
                <p className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                  <User className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                  {booking.customer_name}
                </p>
                <p className="text-xs text-gray-500 dark:text-gray-400">{booking.customer_phone}</p>
              </div>

              <div className="space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Assigned Vehicle</span>
                <p className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                  <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                  {vehicle?.name || 'Standard Vehicle'}
                </p>
                <p className="text-xs font-mono text-emerald-600 dark:text-emerald-400 font-bold">{vehicle?.license_plate}</p>
              </div>
            </div>

            {/* Timings & Locations */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div className="p-4 bg-gray-50 dark:bg-gray-950/40 rounded-2xl border border-gray-200/80 dark:border-gray-800/80 space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400 block">Pickup Details</span>
                <p className="font-bold text-gray-800 dark:text-gray-200">{booking.start_date} at {booking.pickup_time || '00:00'}</p>
                <p className="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                  <MapPin className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                  {booking.pickup_location || 'Main Terminal'}
                </p>
              </div>

              <div className="p-4 bg-gray-50 dark:bg-gray-950/40 rounded-2xl border border-gray-200/80 dark:border-gray-800/80 space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400 block">Return Details</span>
                <p className="font-bold text-gray-800 dark:text-gray-200">{booking.end_date} at {booking.return_time || '00:00'}</p>
                <p className="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                  <MapPin className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                  {booking.destination || 'Dropoff Point'}
                </p>
              </div>
            </div>

            {/* Financial Summary */}
            <div className="p-6 bg-emerald-50/60 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 space-y-3">
              <h3 className="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex items-center gap-2">
                <DollarSign className="w-4 h-4 text-amber-500" />
                Payment Breakdown
              </h3>

              <div className="space-y-1.5 text-xs">
                <div className="flex justify-between text-gray-600 dark:text-gray-400">
                  <span>Destination Rate x {booking.total_days} Days:</span>
                  <span className="font-bold text-gray-800 dark:text-gray-200">₱{Number(booking.destination_rate * booking.total_days).toLocaleString()}</span>
                </div>

                {booking.reservation_fee > 0 && (
                  <div className="flex justify-between text-emerald-700 dark:text-emerald-400">
                    <span>Reservation Fee Paid:</span>
                    <span className="font-bold">-₱{Number(booking.reservation_fee).toLocaleString()}</span>
                  </div>
                )}

                <div className="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-800 text-sm font-extrabold text-gray-900 dark:text-white">
                  <span>Total Amount Paid / Due:</span>
                  <span className="text-amber-600 dark:text-amber-400 text-lg">₱{Number(booking.total_price).toLocaleString()}</span>
                </div>
              </div>
            </div>

            {/* License Photo (If Uploaded) */}
            {booking.driver_license_path && (
              <div className="space-y-2 pt-2">
                <span className="text-xs font-bold text-gray-700 dark:text-gray-300 block">Attached Driver's License</span>
                <div className="w-48 h-32 rounded-2xl bg-gray-100 dark:bg-gray-950 overflow-hidden border border-gray-200 dark:border-gray-800">
                  <img src={`/${booking.driver_license_path}`} alt="License" className="w-full h-full object-cover" />
                </div>
              </div>
            )}

            {/* Status Quick Updater Bar (For Hosts / Admins) */}
            <div className="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between print:hidden">
              <span className="text-xs font-bold text-gray-500 dark:text-gray-400">Update Status:</span>

              <div className="flex items-center gap-2">
                {['confirmed', 'completed', 'cancelled'].map((st) => (
                  <button
                    key={st}
                    onClick={() => handleStatusChange(st)}
                    disabled={booking.status === st}
                    className={`px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition-all ${
                      booking.status === st
                        ? 'bg-emerald-600 text-white shadow-xs'
                        : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                    }`}
                  >
                    {st}
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
