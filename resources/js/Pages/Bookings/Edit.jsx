import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Calendar, ArrowLeft } from 'lucide-react';

export default function Edit({ booking, vehicles = [] }) {
  const { data, setData, put, processing, errors } = useForm({
    status: booking.status || 'confirmed',
    notes: booking.notes || '',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    put(`/bookings/${booking.id}`);
  };

  return (
    <AppLayout title={`Edit Booking #${booking.booking_code}`}>
      <Head title={`Edit Booking #${booking.booking_code}`} />

      <div className="max-w-xl mx-auto space-y-6 pb-6">
        <Link
          href={`/bookings/${booking.id}`}
          className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
        >
          <ArrowLeft className="w-4 h-4" />
          Back to Ticket
        </Link>

        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6 transition-colors duration-300">
          <h1 className="text-xl font-black text-gray-900 dark:text-white">Edit Reservation Status</h1>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <select
                value={data.status}
                onChange={(e) => setData('status', e.target.value)}
                className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white"
              >
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Notes / Internal Comments</label>
              <textarea
                value={data.notes}
                onChange={(e) => setData('notes', e.target.value)}
                rows="3"
                className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white"
              />
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
              <button
                type="submit"
                disabled={processing}
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20"
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
