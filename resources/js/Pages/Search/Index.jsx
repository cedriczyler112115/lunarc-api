import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import DateInput from '@/Components/DateInput';
import {
  Car,
  Search,
  Calendar,
  Clock,
  User,
  CheckCircle2,
  AlertCircle,
  RotateCcw,
  Sparkles,
  SlidersHorizontal,
  ShieldCheck,
  Eye,
  ArrowRight,
  X,
  ChevronLeft,
  ChevronRight,
  Fuel,
  Users
} from 'lucide-react';

export default function SearchIndex({
  vehicles = [],
  vehicleTypes = [],
  filters = {},
  hasSearched = false,
  totalResults = 0,
}) {
  const vehicleList = Array.isArray(vehicles) ? vehicles : [];
  const typeList = Array.isArray(vehicleTypes) ? vehicleTypes : [];

  const [selectedType, setSelectedType] = useState(filters.vehicle_type_id || '');
  const [startDate, setStartDate] = useState(filters.start_date || '');
  const [pickupTime, setPickupTime] = useState(filters.pickup_time || '');
  const [endDate, setEndDate] = useState(filters.end_date || '');
  const [returnTime, setReturnTime] = useState(filters.return_time || '');

  // Gallery Modal state
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedGallery, setSelectedGallery] = useState({
    name: '',
    licensePlate: '',
    images: [],
    currentIndex: 0,
  });

  const handleSearch = (e) => {
    if (e) e.preventDefault();

    router.get(
      '/search',
      {
        vehicle_type_id: selectedType || undefined,
        start_date: startDate || undefined,
        pickup_time: pickupTime || undefined,
        end_date: endDate || undefined,
        return_time: returnTime || undefined,
      },
      {
        preserveState: true,
        preserveScroll: true,
      }
    );
  };

  const handleReset = () => {
    setSelectedType('');
    setStartDate('');
    setPickupTime('');
    setEndDate('');
    setReturnTime('');

    router.get('/search', {}, { preserveState: false });
  };

  const hasActiveFilters = Boolean(
    selectedType || startDate || pickupTime || endDate || returnTime
  );

  const getVehicleImages = (vehicle) => {
    if (!vehicle) return ['/images/placeholder.jpg'];
    let raw = vehicle.all_images || vehicle.images;
    if (typeof raw === 'string') {
      try {
        const parsed = JSON.parse(raw);
        if (parsed) raw = parsed;
      } catch (e) {
        raw = [raw];
      }
    }
    if (raw && typeof raw === 'object' && !Array.isArray(raw)) {
      raw = Object.values(raw);
    }
    const list = Array.isArray(raw) && raw.length > 0
      ? raw
      : vehicle.image_path
      ? [vehicle.image_path]
      : [];

    const formatted = list
      .filter((img) => img && (typeof img === 'string' || typeof img === 'number'))
      .map((img) => {
        const str = String(img).trim();
        if (str.startsWith('http') || str.startsWith('/')) return str;
        return `/${str}`;
      });

    return formatted.length > 0 ? formatted : ['/images/placeholder.jpg'];
  };

  const openGalleryModal = (vehicle) => {
    const images = getVehicleImages(vehicle);
    setSelectedGallery({
      name: vehicle.name || 'Vehicle Photo',
      licensePlate: vehicle.license_plate || '',
      images,
      currentIndex: 0,
    });
    setModalOpen(true);
  };

  const nextImage = (e) => {
    e?.stopPropagation();
    setSelectedGallery((prev) => ({
      ...prev,
      currentIndex: (prev.currentIndex + 1) % prev.images.length,
    }));
  };

  const prevImage = (e) => {
    e?.stopPropagation();
    setSelectedGallery((prev) => ({
      ...prev,
      currentIndex: (prev.currentIndex - 1 + prev.images.length) % prev.images.length,
    }));
  };

  const buildBookingUrl = (vehicleId) => {
    const params = new URLSearchParams();
    params.set('vehicle_id', vehicleId);
    if (startDate) params.set('start_date', startDate);
    if (endDate) params.set('end_date', endDate);
    if (pickupTime) params.set('pickup_time', pickupTime);
    if (returnTime) params.set('return_time', returnTime);
    return `/bookings/create?${params.toString()}`;
  };

  return (
    <AppLayout title="Search Available Fleet">
      <Head title="Search Available Vehicles" />

      <div className="space-y-6 pb-12 pt-2 md:pt-4">
        {/* Page Header Card */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
          <div>
            <h2 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <Search className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
              Search Available Fleet
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Find and check available rental vehicles by type, travel dates, and pickup schedule.
            </p>
          </div>

          <div className="flex items-center gap-2">
            <Link
              href="/vehicles"
              className="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150"
            >
              <Car className="w-4 h-4 mr-2" />
              View All Fleet
            </Link>
          </div>
        </div>

        {/* Search & Filter Form Card */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-5 sm:p-7 shadow-xs transition-colors">
          <form onSubmit={handleSearch} className="space-y-5">
            <div className="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
              <div className="flex items-center gap-2">
                <SlidersHorizontal className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                <h2 className="text-sm font-extrabold uppercase tracking-wider text-gray-800 dark:text-gray-200">
                  Search Parameters
                </h2>
              </div>

              {hasActiveFilters && (
                <button
                  type="button"
                  onClick={handleReset}
                  className="text-xs font-bold text-gray-500 hover:text-rose-600 dark:text-gray-400 dark:hover:text-rose-400 flex items-center gap-1 transition-colors"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                  Reset Filters
                </button>
              )}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
              {/* Vehicle Type */}
              <div className="space-y-1.5">
                <label
                  htmlFor="search_vehicle_type"
                  className="block text-xs font-bold text-gray-700 dark:text-gray-300"
                >
                  Vehicle Type
                </label>
                <div className="relative">
                  <select
                    id="search_vehicle_type"
                    value={selectedType}
                    onChange={(e) => setSelectedType(e.target.value)}
                    className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/60 text-gray-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                  >
                    <option value="">All Vehicle Types</option>
                    {typeList.map((t) => (
                      <option key={t.id} value={t.id}>
                        {t.name}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              {/* Start Date */}
              <div className="space-y-1.5">
                <label
                  htmlFor="search_start_date"
                  className="block text-xs font-bold text-gray-700 dark:text-gray-300"
                >
                  Start Date
                </label>
                <DateInput
                  id="search_start_date"
                  value={startDate}
                  onChange={(val) => setStartDate(val)}
                  placeholder="mm/dd/yyyy"
                  className="px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/60 text-gray-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>

              {/* Pickup Time */}
              <div className="space-y-1.5">
                <label
                  htmlFor="search_pickup_time"
                  className="block text-xs font-bold text-gray-700 dark:text-gray-300"
                >
                  Pickup Time
                </label>
                <div className="relative">
                  <input
                    type="time"
                    id="search_pickup_time"
                    value={pickupTime}
                    onChange={(e) => setPickupTime(e.target.value)}
                    className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/60 text-gray-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                  />
                </div>
              </div>

              {/* End Date */}
              <div className="space-y-1.5">
                <label
                  htmlFor="search_end_date"
                  className="block text-xs font-bold text-gray-700 dark:text-gray-300"
                >
                  End Date
                </label>
                <DateInput
                  id="search_end_date"
                  value={endDate}
                  onChange={(val) => setEndDate(val)}
                  placeholder="mm/dd/yyyy"
                  className="px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/60 text-gray-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>

              {/* Return Time */}
              <div className="space-y-1.5">
                <label
                  htmlFor="search_return_time"
                  className="block text-xs font-bold text-gray-700 dark:text-gray-300"
                >
                  Return Time
                </label>
                <div className="relative">
                  <input
                    type="time"
                    id="search_return_time"
                    value={returnTime}
                    onChange={(e) => setReturnTime(e.target.value)}
                    className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/60 text-gray-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                  />
                </div>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex items-center justify-end gap-3 pt-2">
              <button
                type="button"
                onClick={handleReset}
                className="px-4 py-2.5 rounded-2xl border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold transition active:scale-95"
              >
                Clear
              </button>
              <button
                type="submit"
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-extrabold shadow-md shadow-emerald-600/25 flex items-center gap-2 transition active:scale-95"
              >
                <Search className="w-4 h-4" />
                <span>Search Available Vehicles</span>
              </button>
            </div>
          </form>
        </div>

        {/* Results Header */}
        {hasSearched && (
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 className="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <Car className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                Available Vehicles
                <span className="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-extrabold">
                  {totalResults}
                </span>
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Showing fleet ready for dispatch matching your criteria
              </p>
            </div>
          </div>
        )}

        {/* Results Grid / States */}
        {!hasSearched ? (
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-10 text-center shadow-xs">
            <div className="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4 border border-emerald-200 dark:border-emerald-800">
              <Search className="w-8 h-8" />
            </div>
            <h3 className="text-base font-extrabold text-gray-900 dark:text-white mb-1">
              Select Your Search Criteria
            </h3>
            <p className="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">
              Choose a vehicle type, travel dates, and pickup/return times above, then click <strong className="text-gray-700 dark:text-gray-200">Search Available Vehicles</strong> to see matching cars.
            </p>
          </div>
        ) : vehicleList.length === 0 ? (
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-12 text-center shadow-xs">
            <div className="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto mb-4 border border-amber-200 dark:border-amber-800">
              <AlertCircle className="w-8 h-8" />
            </div>
            <h3 className="text-base font-extrabold text-gray-900 dark:text-white mb-1">
              No Available Vehicles Found
            </h3>
            <p className="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
              There are no available vehicles matching your chosen vehicle type or schedule window. Try selecting different dates or resetting the search filters.
            </p>
            <button
              onClick={handleReset}
              className="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md shadow-emerald-600/20 active:scale-95"
            >
              <RotateCcw className="w-4 h-4" />
              Reset All Filters
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            {vehicleList.map((vehicle) => {
              const images = getVehicleImages(vehicle);
              const primaryImage = images[0] || '/images/placeholder.jpg';
              const ownerName = vehicle.user?.name || vehicle.owner?.name || 'Authorized Fleet Host';
              const typeName = vehicle.vehicle_type?.name || vehicle.vehicleType?.name || 'Standard';

              return (
                <div
                  key={vehicle.id}
                  className="group bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs hover:shadow-lg transition-all duration-200 flex flex-col justify-between"
                >
                  {/* Vehicle Image Banner */}
                  <div>
                    <div className="relative aspect-video w-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                      <img
                        src={primaryImage}
                        alt={vehicle.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        onError={(e) => {
                          e.target.onerror = null;
                          e.target.src = '/images/placeholder.jpg';
                        }}
                      />

                      {/* Floating Type Badge */}
                      <div className="absolute top-3 left-3 flex items-center gap-1.5">
                        <span className="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase bg-white/90 dark:bg-gray-900/90 text-gray-800 dark:text-gray-200 shadow-sm backdrop-blur-md border border-white/20">
                          {typeName}
                        </span>
                        <span className="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                          Available
                        </span>
                      </div>

                      {/* Photo Gallery Trigger Button */}
                      {images.length > 1 && (
                        <button
                          type="button"
                          onClick={() => openGalleryModal(vehicle)}
                          className="absolute bottom-3 right-3 px-2.5 py-1 rounded-xl bg-black/60 hover:bg-black/80 text-white text-[10px] font-bold backdrop-blur-md flex items-center gap-1 transition"
                        >
                          <Eye className="w-3 h-3" />
                          <span>{images.length} Photos</span>
                        </button>
                      )}
                    </div>

                    {/* Content Section */}
                    <div className="p-5 space-y-3.5">
                      {/* Name & Plate */}
                      <div>
                        <div className="flex items-start justify-between gap-2">
                          <h3 className="text-base font-extrabold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {vehicle.name}
                          </h3>
                        </div>
                        <div className="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                          <span className="font-mono font-bold bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md text-gray-700 dark:text-gray-300">
                            {vehicle.license_plate}
                          </span>
                          {vehicle.year && <span>&bull; {vehicle.year}</span>}
                          {vehicle.color && <span>&bull; {vehicle.color}</span>}
                        </div>
                      </div>

                      {/* Key Specs Pills */}
                      <div className="flex flex-wrap items-center gap-1.5 text-[11px] text-gray-600 dark:text-gray-300">
                        {vehicle.transmission && (
                          <span className="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200/60 dark:border-gray-700/60 font-semibold">
                            {vehicle.transmission}
                          </span>
                        )}
                        {vehicle.fuel_type && (
                          <span className="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200/60 dark:border-gray-700/60 font-semibold flex items-center gap-1">
                            <Fuel className="w-3 h-3 text-gray-400" />
                            {vehicle.fuel_type}
                          </span>
                        )}
                        {vehicle.seats && (
                          <span className="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200/60 dark:border-gray-700/60 font-semibold flex items-center gap-1">
                            <Users className="w-3 h-3 text-gray-400" />
                            {vehicle.seats} Seats
                          </span>
                        )}
                      </div>

                      {/* Owner / Host Name Section */}
                      <div className="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div className="flex items-center gap-2.5 min-w-0">
                          <div className="w-7 h-7 rounded-full bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                            {ownerName.charAt(0).toUpperCase()}
                          </div>
                          <div className="min-w-0">
                            <span className="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 block leading-tight">
                              Owner / Host
                            </span>
                            <span className="text-xs font-extrabold text-gray-800 dark:text-gray-200 truncate block">
                              {ownerName}
                            </span>
                          </div>
                        </div>

                        <div className="flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-bold shrink-0">
                          <ShieldCheck className="w-3.5 h-3.5" />
                          <span>Verified</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Bottom Rate & Book Button */}
                  <div className="p-5 pt-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center justify-between gap-3">
                    <div>
                      <span className="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 block">
                        Daily Rate
                      </span>
                      <span className="text-lg font-black text-emerald-600 dark:text-emerald-400">
                        ₱{Number(vehicle.daily_rate || 0).toLocaleString(undefined, {
                          minimumFractionDigits: 2,
                          maximumFractionDigits: 2,
                        })}
                      </span>
                    </div>

                    <div className="flex items-center gap-2">
                      <Link
                        href={`/vehicles/${vehicle.id}`}
                        className="p-2.5 rounded-2xl bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition active:scale-95"
                        title="View Vehicle Specs"
                      >
                        <Eye className="w-4 h-4" />
                      </Link>

                      <Link
                        href={buildBookingUrl(vehicle.id)}
                        className="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-black shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition active:scale-95"
                      >
                        <span>Book</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </Link>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>

      {/* Lightbox Gallery Modal */}
      {modalOpen && (
        <div
          className="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6 bg-slate-950/90 backdrop-blur-xl animate-in fade-in duration-200"
          onClick={() => setModalOpen(false)}
        >
          <div
            className="relative w-full max-w-4xl bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="p-4 sm:px-6 flex items-center justify-between border-b border-slate-800">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                  <Car className="w-4 h-4" />
                </div>
                <div>
                  <h4 className="text-sm font-extrabold text-white">{selectedGallery.name}</h4>
                  <p className="text-[10px] text-slate-400 font-mono">
                    Plate: {selectedGallery.licensePlate} &bull; Image {selectedGallery.currentIndex + 1} of{' '}
                    {selectedGallery.images.length}
                  </p>
                </div>
              </div>

              <button
                type="button"
                onClick={() => setModalOpen(false)}
                className="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            {/* Modal Image View */}
            <div className="relative flex-1 bg-black flex items-center justify-center min-h-[300px] max-h-[65vh] p-4">
              <img
                src={selectedGallery.images[selectedGallery.currentIndex]}
                alt={selectedGallery.name}
                className="max-h-[60vh] max-w-full object-contain rounded-lg shadow-2xl"
              />

              {selectedGallery.images.length > 1 && (
                <>
                  <button
                    type="button"
                    onClick={prevImage}
                    className="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white flex items-center justify-center transition backdrop-blur-md shadow-lg"
                  >
                    <ChevronLeft className="w-5 h-5" />
                  </button>
                  <button
                    type="button"
                    onClick={nextImage}
                    className="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white flex items-center justify-center transition backdrop-blur-md shadow-lg"
                  >
                    <ChevronRight className="w-5 h-5" />
                  </button>
                </>
              )}
            </div>
          </div>
        </div>
      )}
    </AppLayout>
  );
}
