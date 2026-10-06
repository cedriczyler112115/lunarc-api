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
import confirmDialog from '@/Utils/confirm';

export default function Show({ booking }) {
  const vehicle = booking.vehicle;
  const [completeModalOpen, setCompleteModalOpen] = React.useState(false);
  const [actualIncome, setActualIncome] = React.useState(
    booking.actual_income !== null && booking.actual_income !== undefined
      ? String(booking.actual_income)
      : String(Math.round(booking.total_price || 0))
  );
  const [isSubmitting, setIsSubmitting] = React.useState(false);

  const handlePrint = () => {
    window.print();
  };

  const handleStatusChange = (newStatus) => {
    if (newStatus === 'completed') {
      setActualIncome(
        booking.actual_income !== null && booking.actual_income !== undefined
          ? String(booking.actual_income)
          : String(Math.round(booking.total_price || 0))
      );
      setCompleteModalOpen(true);
      return;
    }

    const isDestructive = newStatus === 'cancelled';
    confirmDialog({
      title: 'Update Booking Status',
      content: `Are you sure you want to change this booking status to <strong class="uppercase">${newStatus}</strong>?`,
      type: isDestructive ? 'red' : 'blue',
      confirmButtonText: `Update to ${newStatus}`,
      confirmButtonClass: isDestructive ? 'btn-red' : 'btn-blue',
      onConfirm: () => {
        router.patch(`/bookings/${booking.id}`, { status: newStatus });
      },
    });
  };

  const handleCompleteSubmit = (e) => {
    if (e) e.preventDefault();
    const incomeValue = actualIncome !== '' ? parseInt(actualIncome, 10) : 0;
    setIsSubmitting(true);
    router.patch(
      `/bookings/${booking.id}`,
      {
        status: 'completed',
        actual_income: isNaN(incomeValue) ? 0 : incomeValue,
      },
      {
        onFinish: () => {
          setIsSubmitting(false);
          setCompleteModalOpen(false);
        },
      }
    );
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

  return (
    <AppLayout title={`Booking #${booking.booking_code}`}>
      <Head title={`Booking #${booking.booking_code}`} />

      <div className="max-w-2xl mx-auto space-y-6 pb-6">
        <div className="flex items-center justify-between print:hidden">
          <button
            type="button"
            onClick={() => {
              if (window.history.length > 1) {
                window.history.go(-1);
              } else {
                router.visit('/bookings');
              }
            }}
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors cursor-pointer"
          >
            <ArrowLeft className="w-4 h-4" />
            Back
          </button>

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
                className={`px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider ${booking.status === 'confirmed'
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
                <p className="font-bold text-gray-800 dark:text-gray-200">
                  {formatDate(booking.start_date)} {booking.pickup_time ? `at ${formatTime(booking.pickup_time)}` : ''}
                </p>
                <p className="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                  <MapPin className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                  {booking.pickup_location || 'Main Terminal'}
                </p>
              </div>

              <div className="p-4 bg-gray-50 dark:bg-gray-950/40 rounded-2xl border border-gray-200/80 dark:border-gray-800/80 space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400 block">Return Details</span>
                <p className="font-bold text-gray-800 dark:text-gray-200">
                  {formatDate(booking.end_date)} {booking.return_time ? `at ${formatTime(booking.return_time)}` : ''}
                </p>
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

                {booking.actual_income !== null && booking.actual_income !== undefined && (
                  <div className="flex justify-between pt-2 border-t border-emerald-300/60 dark:border-emerald-700/60 text-sm font-black text-emerald-700 dark:text-emerald-300 bg-emerald-100/50 dark:bg-emerald-900/30 p-2.5 rounded-xl">
                    <span>Actual Income:</span>
                    <span className="text-base font-black">₱{Number(booking.actual_income).toLocaleString()}</span>
                  </div>
                )}
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
            {booking.status === 'confirmed' ? (
              <div className="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between print:hidden">
                <span className="text-xs font-bold text-gray-500 dark:text-gray-400">Update Status:</span>

                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => handleStatusChange('completed')}
                    className="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                  >
                    <CheckCircle2 className="w-3.5 h-3.5" />
                    Completed
                  </button>

                  <button
                    type="button"
                    onClick={() => handleStatusChange('cancelled')}
                    className="px-3.5 py-1.5 rounded-xl text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 dark:hover:bg-rose-900/50 shadow-xs transition-all cursor-pointer"
                  >
                    Cancelled
                  </button>
                </div>
              </div>
            ) : (
              <div className="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs print:hidden">
                <span className="font-bold text-gray-500 dark:text-gray-400">Booking Status:</span>
                <span className={`px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider ${booking.status === 'completed'
                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                    : 'bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200 dark:border-rose-800'
                  }`}>
                  {booking.status} (Final)
                </span>
              </div>
            )}
          </div>
        </div>

        {/* Complete Booking Popup Modal with Actual Income Field */}
        {completeModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-5 animate-in zoom-in-95 duration-150">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                  <CheckCircle2 className="w-6 h-6" />
                </div>
                <div>
                  <h3 className="text-base font-extrabold text-gray-900 dark:text-white">Complete Booking</h3>
                  <p className="text-xs text-gray-500 dark:text-gray-400">#{booking.booking_code} &bull; {booking.customer_name}</p>
                </div>
              </div>

              <p className="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                Confirm completion of this booking. Please specify the actual income generated from this rental trip.
              </p>

              <form onSubmit={handleCompleteSubmit} className="space-y-4">
                <div className="space-y-1.5">
                  <label htmlFor="actual_income_input" className="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    Actual Income (₱) <span className="text-rose-500">*</span>
                  </label>
                  <div className="relative">
                    <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">₱</span>
                    <input
                      id="actual_income_input"
                      type="number"
                      step="1"
                      min="0"
                      required
                      placeholder="0"
                      value={actualIncome}
                      onChange={(e) => {
                        const val = e.target.value;
                        if (val === '' || /^\d+$/.test(val)) {
                          setActualIncome(val);
                        }
                      }}
                      className="w-full pl-8 pr-4 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white font-extrabold text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-inner"
                    />
                  </div>
                  <p className="text-[11px] text-gray-500 dark:text-gray-400">
                    Total bill amount: <span className="font-bold text-gray-700 dark:text-gray-300">₱{Number(booking.total_price).toLocaleString()}</span>
                  </p>
                </div>

                <div className="flex items-center justify-end gap-2.5 pt-2">
                  <button
                    type="button"
                    onClick={() => setCompleteModalOpen(false)}
                    disabled={isSubmitting}
                    className="px-4 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-xs font-extrabold text-white shadow-md shadow-emerald-600/30 transition-all flex items-center gap-1.5"
                  >
                    <CheckCircle2 className="w-4 h-4" />
                    {isSubmitting ? 'Saving...' : 'Confirm Completed'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  );
}
