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
  ShieldCheck,
  Edit,
  Clock,
  MapPin,
  CheckCircle2
} from 'lucide-react';

export default function Show({ vehicle }) {
  const images = Array.isArray(vehicle.images) && vehicle.images.length > 0
    ? vehicle.images
    : vehicle.image_path ? [vehicle.image_path] : [];

  const [activeImage, setActiveImage] = useState(images[0] || null);

  const bookings = vehicle.bookings || [];
  const isAvailable = vehicle.status === 'available';

  return (
    <AppLayout title={vehicle.name}>
      <Head title={vehicle.name} />

      <div className="max-w-4xl mx-auto space-y-6 pb-6">
        <div className="flex items-center justify-between">
          <Link
            href="/vehicles"
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Fleet
          </Link>

          <Link
            href={`/vehicles/${vehicle.id}/edit`}
            className="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold flex items-center gap-2"
          >
            <Edit className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Edit Vehicle
          </Link>
        </div>

        {/* Gallery & Hero Card */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs space-y-4 transition-colors duration-300">
          <div className="relative aspect-[16/9] sm:aspect-[21/9] bg-gray-100 dark:bg-gray-950 overflow-hidden">
            {activeImage ? (
              <img src={`/${activeImage}`} alt={vehicle.name} className="w-full h-full object-cover" />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-400">
                <Car className="w-16 h-16" />
              </div>
            )}

            <div className="absolute top-4 left-4">
              <span
                className={`px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider backdrop-blur-md shadow-md border ${
                  isAvailable
                    ? 'bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-500/40'
                    : 'bg-rose-500/20 text-rose-800 dark:text-rose-300 border-rose-500/40'
                }`}
              >
                {isAvailable ? 'Available Now' : 'Out of Service'}
              </span>
            </div>

            <div className="absolute bottom-4 right-4 bg-white/90 dark:bg-gray-950/80 backdrop-blur-md px-4 py-2 rounded-2xl border border-gray-200 dark:border-gray-800">
              <span className="text-xl font-black text-amber-600 dark:text-amber-400">₱{Number(vehicle.daily_rate).toLocaleString()}</span>
              <span className="text-xs text-gray-500 dark:text-gray-400 font-normal"> / day</span>
            </div>
          </div>

          {/* Gallery Thumbnails */}
          {images.length > 1 && (
            <div className="px-6 flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
              {images.map((img, i) => (
                <button
                  key={i}
                  onClick={() => setActiveImage(img)}
                  className={`relative w-20 h-14 rounded-xl overflow-hidden border-2 shrink-0 transition-all ${
                    activeImage === img ? 'border-emerald-500 scale-105 shadow-md' : 'border-gray-200 dark:border-gray-800 opacity-60'
                  }`}
                >
                  <img src={`/${img}`} alt={`Thumb ${i}`} className="w-full h-full object-cover" />
                </button>
              ))}
            </div>
          )}

          {/* Details Bar */}
          <div className="p-6 sm:p-8 space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-6">
              <div>
                <span className="text-xs font-extrabold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">
                  {vehicle.vehicle_type?.name || 'Standard Vehicle'}
                </span>
                <h1 className="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-0.5">{vehicle.name}</h1>
                <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {vehicle.make} {vehicle.model} • Year {vehicle.year} • Color: {vehicle.color || 'N/A'}
                </p>
              </div>

              {isAvailable && (
                <Link
                  href={`/bookings/create?vehicle_id=${vehicle.id}`}
                  className="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold text-center shadow-md shadow-emerald-600/20 active:scale-95 transition-transform"
                >
                  Book This Vehicle Now
                </Link>
              )}
            </div>

            {/* Specs Badges */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div className="p-4 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800 text-center space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">License Plate</span>
                <p className="text-sm font-mono font-bold text-gray-800 dark:text-gray-200">{vehicle.license_plate}</p>
              </div>

              <div className="p-4 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800 text-center space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Transmission</span>
                <p className="text-sm font-bold text-gray-800 dark:text-gray-200">{vehicle.transmission}</p>
              </div>

              <div className="p-4 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800 text-center space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Fuel Engine</span>
                <p className="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center justify-center gap-1">
                  <Fuel className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                  {vehicle.fuel_type}
                </p>
              </div>

              <div className="p-4 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800 text-center space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Capacity</span>
                <p className="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center justify-center gap-1">
                  <Users className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                  {vehicle.seats} Passengers
                </p>
              </div>
            </div>

            {/* Description */}
            {vehicle.description && (
              <div className="space-y-2">
                <h3 className="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Host Notes & Description</h3>
                <p className="text-xs text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-950/40 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800">
                  {vehicle.description}
                </p>
              </div>
            )}
          </div>
        </div>

        {/* Booking History Section */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4 transition-colors duration-300">
          <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <Calendar className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            Reservation History for {vehicle.name}
          </h2>

          {bookings.length === 0 ? (
            <p className="text-xs text-gray-500 dark:text-gray-400">No previous or upcoming bookings recorded for this vehicle.</p>
          ) : (
            <div className="divide-y divide-gray-100 dark:divide-gray-800">
              {bookings.map((b) => (
                <div key={b.id} className="py-3.5 flex items-center justify-between text-xs">
                  <div>
                    <span className="font-mono font-bold text-emerald-700 dark:text-emerald-300">#{b.booking_code}</span>
                    <p className="font-bold text-gray-800 dark:text-gray-200 mt-0.5">{b.customer_name}</p>
                    <p className="text-[10px] text-gray-500 dark:text-gray-400">{b.start_date} to {b.end_date}</p>
                  </div>
                  <div className="text-right">
                    <p className="font-black text-amber-600 dark:text-amber-400">₱{Number(b.total_price || 0).toLocaleString()}</p>
                    <span className="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400">{b.status}</span>
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
