import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  Users,
  Fuel,
  ArrowLeft,
  Calendar,
  DollarSign,
  Edit,
  Clock,
  MapPin,
  Phone,
  Mail,
  User,
  Eye,
  FileText
} from 'lucide-react';

export default function Show({ vehicle }) {
  const images = Array.isArray(vehicle.images) && vehicle.images.length > 0
    ? vehicle.images
    : vehicle.image_path ? [vehicle.image_path] : [];

  const [activeImage, setActiveImage] = useState(images[0] || null);

  const bookings = vehicle.bookings || [];
  const isAvailable = vehicle.status === 'available';

  const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const cleanStr = String(dateStr).slice(0, 10);
    const parts = cleanStr.split('-');
    if (parts.length === 3) {
      const year = parseInt(parts[0], 10);
      const month = parseInt(parts[1], 10) - 1;
      const day = parseInt(parts[2], 10);
      return new Date(year, month, day).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      });
    }
    const d = new Date(dateStr);
    return isNaN(d.getTime())
      ? dateStr
      : d.toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric',
        });
  };

  const formatTime = (timeStr) => {
    if (!timeStr) return '';
    const [h, m] = timeStr.split(':');
    if (h === undefined || m === undefined) return timeStr;
    const hour = parseInt(h, 10);
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${m} ${period}`;
  };

  const getStatusBadge = (status) => {
    switch (status) {
      case 'confirmed':
        return (
          <span className="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
            Confirmed
          </span>
        );
      case 'pending':
        return (
          <span className="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
            Pending
          </span>
        );
      case 'completed':
        return (
          <span className="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
            Completed
          </span>
        );
      case 'cancelled':
        return (
          <span className="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
            Cancelled
          </span>
        );
      default:
        return (
          <span className="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
            {status || 'Unknown'}
          </span>
        );
    }
  };

  return (
    <AppLayout title={vehicle.name}>
      <Head title={vehicle.name} />

      <div className="max-w-6xl mx-auto space-y-6 pb-8">
        {/* Top Header Card */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700">
          <div>
            <div className="flex items-center gap-3 flex-wrap">
              <h2 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                {vehicle.name}
              </h2>
              <span
                className={`px-3 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider ${
                  vehicle.status === 'available'
                    ? 'bg-emerald-500 text-white'
                    : vehicle.status === 'maintenance'
                    ? 'bg-amber-500 text-white'
                    : 'bg-rose-600 text-white'
                }`}
              >
                {vehicle.status?.replace('_', ' ')}
              </span>
            </div>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              License Plate: <span className="font-mono font-bold text-gray-800 dark:text-gray-200">{vehicle.license_plate}</span>
            </p>
          </div>

          <div className="flex items-center space-x-3">
            <Link
              href="/vehicles/all-listing"
              className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 text-sm font-semibold rounded-xl transition"
            >
              <ArrowLeft className="w-4 h-4" />
              Back to All Listing
            </Link>
            <Link
              href={`/vehicles/${vehicle.id}/edit`}
              className="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 text-sm font-semibold rounded-xl transition flex items-center gap-1.5"
            >
              <Edit className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Edit Specs & Photo
            </Link>
            {isAvailable && (
              <Link
                href={`/bookings/create?vehicle_id=${vehicle.id}`}
                className="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow transition"
              >
                Book This Car
              </Link>
            )}
          </div>
        </div>

        {/* Main Vehicle Hero 2-Column Framework Card */}
        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 overflow-hidden grid grid-cols-1 md:grid-cols-3">
          {/* Left Column: Photo / Gallery */}
          <div className="md:col-span-1 bg-slate-900 relative min-h-[260px] flex flex-col items-center justify-center overflow-hidden p-2">
            {activeImage ? (
              <img
                src={activeImage.startsWith('http') ? activeImage : `/${activeImage}`}
                alt={vehicle.name}
                className="w-full h-full object-cover rounded-xl"
              />
            ) : (
              <div className="text-center p-6 text-gray-400">
                <Car className="w-16 h-16 mx-auto mb-2 text-gray-600" />
                <span className="text-xs font-bold block">No photo uploaded</span>
                <Link href={`/vehicles/${vehicle.id}/edit`} className="mt-2 inline-block text-xs text-indigo-400 hover:underline">
                  + Upload Photo
                </Link>
              </div>
            )}

            {/* Multiple image thumbnails */}
            {images.length > 1 && (
              <div className="w-full flex items-center justify-center gap-2 mt-2 px-2 overflow-x-auto">
                {images.map((img, i) => (
                  <button
                    key={i}
                    onClick={() => setActiveImage(img)}
                    className={`relative w-12 h-10 rounded-lg overflow-hidden border-2 shrink-0 transition-all ${
                      activeImage === img ? 'border-emerald-500 scale-105 shadow-md' : 'border-slate-700 opacity-60'
                    }`}
                  >
                    <img src={img.startsWith('http') ? img : `/${img}`} alt={`Thumb ${i}`} className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Right Column: Vehicle Details & Specs */}
          <div className="md:col-span-2 p-8 space-y-6 flex flex-col justify-between">
            <div className="space-y-4">
              <div className="flex justify-between items-start">
                <div>
                  <span className="text-xs font-bold text-gray-400 uppercase tracking-wider">Manufacturer / Model</span>
                  <h3 className="text-2xl font-black text-gray-900 dark:text-white">{vehicle.make} {vehicle.model}</h3>
                  <p className="text-xs text-indigo-500 font-semibold">{vehicle.year} Model • {vehicle.color || 'Standard Finish'}</p>
                </div>
                <div className="text-right">
                  <span className="text-xs text-gray-400 uppercase font-semibold">Daily Rate</span>
                  <div className="text-3xl font-black text-indigo-600 dark:text-indigo-400">
                    ₱{Number(vehicle.daily_rate).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>

              {/* Owner Pill if present */}
              {vehicle.user && (
                <div className="flex items-center justify-between p-3.5 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50">
                  <div className="flex items-center space-x-3">
                    <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-sm flex items-center justify-center shadow border-2 border-white">
                      {vehicle.user.name ? vehicle.user.name.charAt(0).toUpperCase() : 'U'}
                    </div>
                    <div>
                      <span className="text-[10px] font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block leading-none">
                        Vehicle Registered Owner
                      </span>
                      <span className="text-sm font-black text-gray-900 dark:text-white block mt-1">
                        {vehicle.user.name}
                      </span>
                    </div>
                  </div>
                  <div className="text-right flex-shrink-0">
                    <span className="text-xs font-medium text-gray-500 dark:text-gray-400 block">{vehicle.user.email}</span>
                  </div>
                </div>
              )}

              {/* Specs Pills (Transmission, Fuel Type, Seats) */}
              <div className="grid grid-cols-3 gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                <div>
                  <span className="block text-[10px] text-gray-400 uppercase font-bold">Transmission</span>
                  <span className="text-sm font-extrabold text-gray-800 dark:text-gray-200">{vehicle.transmission}</span>
                </div>
                <div>
                  <span className="block text-[10px] text-gray-400 uppercase font-bold">Fuel Type</span>
                  <span className="text-sm font-extrabold text-gray-800 dark:text-gray-200">{vehicle.fuel_type}</span>
                </div>
                <div>
                  <span className="block text-[10px] text-gray-400 uppercase font-bold">Seats</span>
                  <span className="text-sm font-extrabold text-gray-800 dark:text-gray-200">{vehicle.seats} Passengers</span>
                </div>
              </div>

              {/* Description */}
              <div>
                <span className="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Features & Notes</span>
                <p className="text-sm text-gray-700 dark:text-gray-300">
                  {vehicle.description || 'Standard rental vehicle specs with full air conditioning, power steering, and clean interior.'}
                </p>
              </div>
            </div>

            <div className="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
              <Link
                href={`/calendar?vehicle_id=${vehicle.id}`}
                className="text-xs font-bold text-amber-600 hover:underline flex items-center"
              >
                <Calendar className="w-4 h-4 mr-1" />
                View Booking Calendar for {vehicle.name}
              </Link>
              <Link href="/vehicles/all-listing" className="text-xs text-gray-500 hover:underline">
                &larr; Back to All Listing
              </Link>
            </div>
          </div>
        </div>

        {/* Reservation History Section in Single Row Card */}
        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 space-y-4">
          <div className="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
            <h3 className="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <Calendar className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
              Reservation History for {vehicle.name}
            </h3>
            <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
              {bookings.filter((b) => b.status === 'confirmed').length}{' '}
              {bookings.filter((b) => b.status === 'confirmed').length === 1 ? 'Confirmed Reservation' : 'Confirmed Reservations'}
            </span>
          </div>

          {bookings.filter((b) => b.status === 'confirmed').length === 0 ? (
            <p className="text-xs text-gray-400 text-center py-6">No confirmed reservations for this vehicle yet.</p>
          ) : (
            <div className="space-y-3">
              {bookings
                .filter((b) => b.status === 'confirmed')
                .map((b) => (
                  <div
                    key={b.id}
                    className="p-4 sm:p-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xs hover:border-emerald-400 dark:hover:border-emerald-600 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4"
                  >
                    {/* 1. Destination Route */}
                    <div className="flex items-center gap-3 min-w-[200px] flex-1">
                      <div className="p-2.5 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl text-emerald-600 dark:text-emerald-400 shrink-0">
                        <MapPin className="w-4 h-4" />
                      </div>
                      <div className="min-w-0">
                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">
                          Destination Route
                        </span>
                        <span className="font-bold text-sm text-gray-900 dark:text-white block truncate">
                          {b.destination || (b.destination_model ? `${b.destination_model.region} — ${b.destination_model.city}` : 'Standard Mindanao Route')}
                        </span>
                      </div>
                    </div>

                    {/* 2. Pickup */}
                    <div className="min-w-[180px]">
                      <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">
                        Pickup
                      </span>
                      <div className="font-bold text-xs text-gray-800 dark:text-gray-200 mt-0.5">
                        {formatDate(b.start_date)}{' '}
                        <span className="text-emerald-600 dark:text-emerald-400 font-semibold">
                          ({formatTime(b.pickup_time || '08:00')})
                        </span>
                      </div>
                    </div>

                    {/* 3. Return */}
                    <div className="min-w-[180px]">
                      <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">
                        Return
                      </span>
                      <div className="font-bold text-xs text-gray-800 dark:text-gray-200 mt-0.5">
                        {formatDate(b.end_date)}{' '}
                        <span className="text-rose-600 dark:text-rose-400 font-semibold">
                          ({formatTime(b.return_time || '18:00')})
                        </span>
                      </div>
                    </div>

                    {/* 4. Total Price */}
                    <div className="text-left md:text-right min-w-[120px] pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-800">
                      <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">
                        Total Price
                      </span>
                      <div className="text-lg font-black text-amber-600 dark:text-amber-400 mt-0.5">
                        ₱{Number(b.total_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                      </div>
                    </div>
                  </div>
                ))}
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}


