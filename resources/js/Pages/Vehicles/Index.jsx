import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import confirmDialog from '@/Utils/confirm';
import {
  Car,
  Plus,
  Search,
  Edit,
  Trash2,
  Eye,
  Calendar,
  X,
  ChevronLeft,
  ChevronRight,
  Camera,
  RotateCcw
} from 'lucide-react';

export default function Index({ vehicles = [], filters = {} }) {
  const [search, setSearch] = useState(filters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(filters.status || '');
  const isInitialMount = useRef(true);

  // Gallery Modal state
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedGallery, setSelectedGallery] = useState({
    name: '',
    licensePlate: '',
    images: [],
    currentIndex: 0,
  });

  const [loadingToggleId, setLoadingToggleId] = useState(null);

  const handleToggleAvailability = (vehicleId) => {
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

  const applyFilters = (newSearch, newStatus) => {
    const query = {};
    if (newSearch && newSearch.trim()) query.search = newSearch.trim();
    if (newStatus) query.status = newStatus;

    router.get('/vehicles', query, { preserveState: true, replace: true });
  };

  const handleStatusChange = (statusValue) => {
    setSelectedStatus(statusValue);
    applyFilters(search, statusValue);
  };

  // Debounced search for automatic filtering when typing
  useEffect(() => {
    if (isInitialMount.current) {
      isInitialMount.current = false;
      return;
    }

    const timer = setTimeout(() => {
      applyFilters(search, selectedStatus);
    }, 350);

    return () => clearTimeout(timer);
  }, [search]);

  const handleClear = () => {
    setSearch('');
    setSelectedStatus('');
    router.get('/vehicles', {}, { preserveState: true, replace: true });
  };

  const handleDeleteVehicle = (vehicle) => {
    confirmDialog({
      title: 'Delete Vehicle',
      content: `Are you sure you want to delete "${vehicle.name}" (${vehicle.license_plate})? This will remove all photos, details, and fleet records for this vehicle.`,
      type: 'red',
      confirmButtonText: 'Yes, Delete',
      confirmButtonClass: 'btn-red',
      onConfirm: () => {
        router.delete(`/vehicles/${vehicle.id}`, {
          preserveScroll: true,
        });
      },
    });
  };

  const openGallery = (vehicle) => {
    const imagesList = Array.isArray(vehicle.all_images) && vehicle.all_images.length > 0
      ? vehicle.all_images
      : vehicle.images && vehicle.images.length > 0
      ? vehicle.images
      : vehicle.image_path
      ? [vehicle.image_path]
      : [];

    const formattedImages = imagesList.length > 0
      ? imagesList.map((img) => (img.startsWith('http') || img.startsWith('/') ? img : `/${img}`))
      : ['/images/placeholder.jpg'];

    setSelectedGallery({
      name: vehicle.name,
      licensePlate: vehicle.license_plate,
      images: formattedImages,
      currentIndex: 0,
    });
    setModalOpen(true);
  };

  const nextImage = () => {
    setSelectedGallery((prev) => ({
      ...prev,
      currentIndex: (prev.currentIndex + 1) % prev.images.length,
    }));
  };

  const prevImage = () => {
    setSelectedGallery((prev) => ({
      ...prev,
      currentIndex: (prev.currentIndex - 1 + prev.images.length) % prev.images.length,
    }));
  };

  const hasFilters = Boolean(search || selectedStatus);

  return (
    <AppLayout title="My Fleet">
      <Head title="My Fleet" />

      <div className="space-y-6 pb-6">
        {/* Header */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
          <div>
            <h2 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <Car className="w-7 h-7 text-indigo-600 dark:text-indigo-400" />
              My Fleet
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Manage rental car entries, photos, specifications, daily rates, and status.
            </p>
          </div>

          <Link
            href="/vehicles/create"
            className="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150"
          >
            <Plus className="w-5 h-5 mr-2" />
            + Register New Vehicle
          </Link>
        </div>

        {/* Automatic Search and Filter Bar */}
        <div className="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-center">
            {/* Search Input */}
            <div className="md:col-span-7 lg:col-span-8">
              <label className="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                Search Fleet
              </label>
              <div className="relative">
                <Search className="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  type="text"
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  placeholder="Type to filter by make, model, license plate..."
                  className="w-full pl-10 pr-9 py-2.5 text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:text-white rounded-xl focus:outline-none focus:border-indigo-500 transition-colors"
                />
                {search && (
                  <button
                    type="button"
                    onClick={() => {
                      setSearch('');
                      applyFilters('', selectedStatus);
                    }}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    title="Clear search"
                  >
                    <X className="w-3.5 h-3.5" />
                  </button>
                )}
              </div>
            </div>

            {/* Status Filter */}
            <div className="md:col-span-5 lg:col-span-4">
              <label className="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                Status Filter
              </label>
              <div className="flex items-center gap-2">
                <select
                  value={selectedStatus}
                  onChange={(e) => handleStatusChange(e.target.value)}
                  className="w-full text-xs py-2.5 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 dark:text-white rounded-xl focus:outline-none focus:border-indigo-500 font-bold transition-colors"
                >
                  <option value="">All Statuses</option>
                  <option value="available">🟢 Available</option>
                  <option value="rented">🔵 Rented Today</option>
                  <option value="maintenance">🟡 Maintenance</option>
                  <option value="out_of_service">🔴 Out of Service</option>
                </select>

                {hasFilters && (
                  <button
                    type="button"
                    onClick={handleClear}
                    className="px-3.5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition shrink-0 flex items-center gap-1"
                    title="Reset all filters"
                  >
                    <RotateCcw className="w-3.5 h-3.5" />
                    <span>Reset</span>
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Vehicles Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          {vehicles.length === 0 ? (
            <div className="col-span-full bg-white dark:bg-gray-800 p-12 text-center rounded-2xl border border-gray-100 dark:border-gray-700 space-y-3">
              <Car className="w-16 h-16 text-gray-300 mx-auto" />
              <h3 className="text-lg font-bold text-gray-700 dark:text-gray-300">No Vehicles Found</h3>
              <p className="text-gray-500 text-sm">Add your first rental car data entry with photo to start accepting bookings.</p>
              <Link href="/vehicles/create" className="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl text-sm shadow">
                + Add Vehicle Data Entry
              </Link>
            </div>
          ) : (
            vehicles.map((vehicle) => {
              const photoCount = vehicle.all_images?.length || (vehicle.image_path ? 1 : 0);
              return (
                <div key={vehicle.id} className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between">
                  <div>
                    {/* Header / Card Image Banner */}
                    <div
                      onClick={() => openGallery(vehicle)}
                      className="h-40 bg-gradient-to-r from-slate-800 via-indigo-950 to-slate-900 relative overflow-hidden group cursor-pointer"
                      title="Click to open vehicle photo gallery carousel"
                    >
                      {vehicle.image_path && (
                        <img
                          src={vehicle.image_path.startsWith('http') ? vehicle.image_path : `/${vehicle.image_path}`}
                          className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                          alt={vehicle.name}
                        />
                      )}
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30" />
                      <div className="absolute inset-0 p-3.5 flex flex-col justify-between">
                        <div className="flex justify-between items-start">
                          {vehicle.is_rented_today ? (
                            <span
                              className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider shadow-md flex items-center gap-1.5 bg-blue-600 text-white"
                              title="Vehicle is currently on a confirmed rental today"
                            >
                              <span className="w-2 h-2 rounded-full bg-white animate-pulse" />
                              <span>RENTED TODAY</span>
                            </span>
                          ) : (
                            <button
                              type="button"
                              onClick={(e) => {
                                e.stopPropagation();
                                handleToggleAvailability(vehicle.id);
                              }}
                              disabled={loadingToggleId === vehicle.id}
                              className={`px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider shadow-md flex items-center gap-1.5 transition-all active:scale-90 ${
                                vehicle.status === 'available'
                                  ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                                  : vehicle.status === 'maintenance'
                                  ? 'bg-amber-500 hover:bg-amber-600 text-white'
                                  : 'bg-rose-600 hover:bg-rose-700 text-white'
                              }`}
                              title="Click to toggle availability status (ON/OFF)"
                            >
                              <span className={`w-2 h-2 rounded-full ${vehicle.status === 'available' ? 'bg-white animate-pulse' : 'bg-white/60'}`} />
                              <span>{vehicle.status === 'available' ? 'AVAILABLE (ON)' : 'OUT OF SERVICE (OFF)'}</span>
                            </button>
                          )}
                          <div className="flex flex-col items-end gap-1">
                            <span className="text-[10px] font-mono font-bold bg-black/60 backdrop-blur-md text-white px-2 py-0.5 rounded-lg border border-white/20 shadow-xs">
                              {vehicle.license_plate}
                            </span>
                            <span className="text-[9px] font-extrabold bg-black/60 backdrop-blur-md text-white px-2 py-0.5 rounded-lg border border-white/20 shadow-xs flex items-center gap-1 group-hover:bg-indigo-600 transition">
                              📷 {photoCount} {photoCount > 1 ? 'Photos' : 'Photo'}
                            </span>
                          </div>
                        </div>

                        <div>
                          <h3 className="text-base font-extrabold text-white leading-tight drop-shadow-md truncate">{vehicle.name}</h3>
                          <p className="text-[11px] text-indigo-200 font-medium drop-shadow-xs">{vehicle.year} • {vehicle.color || 'Standard Finish'}</p>
                        </div>
                      </div>
                    </div>

                    {/* Specs & Owner Info */}
                    <div className="p-4 space-y-3">
                      <div className="flex items-center justify-between p-2 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <div className="flex items-center space-x-2 truncate">
                          <div className="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-[10px] flex items-center justify-center shadow-xs shrink-0">
                            {vehicle.user?.name?.[0] || 'O'}
                          </div>
                          <div className="truncate">
                            <span className="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block leading-none">Owner</span>
                            <span className="text-xs font-extrabold text-gray-900 dark:text-gray-100 leading-tight block mt-0.5 truncate">{vehicle.user?.name || 'Partner Host'}</span>
                          </div>
                        </div>
                      </div>

                      <div className="grid grid-cols-3 gap-1 py-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                        <div>
                          <span className="block text-[9px] text-gray-400 uppercase font-semibold">Trans.</span>
                          <span className="text-[11px] font-bold text-gray-800 dark:text-gray-200">{vehicle.transmission}</span>
                        </div>
                        <div>
                          <span className="block text-[9px] text-gray-400 uppercase font-semibold">Fuel</span>
                          <span className="text-[11px] font-bold text-gray-800 dark:text-gray-200">{vehicle.fuel_type}</span>
                        </div>
                        <div>
                          <span className="block text-[9px] text-gray-400 uppercase font-semibold">Seats</span>
                          <span className="text-[11px] font-bold text-gray-800 dark:text-gray-200">{vehicle.seats} Seats</span>
                        </div>
                      </div>

                      <p className="text-[11px] text-gray-600 dark:text-gray-400 line-clamp-2">
                        {vehicle.description || 'Standard rental vehicle specification with air conditioning, audio system, and clean interiors.'}
                      </p>

                      <div className="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                        <div>
                          <span className="text-[9px] uppercase font-bold tracking-wider text-indigo-600 dark:text-indigo-400 block">Daily Price</span>
                          <div className="text-base font-black text-indigo-600 dark:text-indigo-400">
                            ₱{Number(vehicle.daily_rate || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            <span className="text-[10px] font-normal text-gray-400">/day</span>
                          </div>
                        </div>
                        <div className="text-right">
                          <span className="text-[10px] text-gray-400 block">Bookings</span>
                          <span className="block text-xs font-bold text-gray-800 dark:text-gray-200">{vehicle.bookings_count || 0} reserved</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Card Action Buttons */}
                  <div className="px-4 pb-4 pt-2 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center gap-1.5">
                    <Link
                      href={`/bookings/create?vehicle_id=${vehicle.id}`}
                      className="flex-1 py-1.5 px-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-xs text-center transition truncate"
                    >
                      Book Car
                    </Link>
                    <Link
                      href={`/calendar?vehicle_id=${vehicle.id}`}
                      title="View Calendar"
                      className="p-2 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 hover:bg-amber-200 rounded-xl transition shrink-0"
                    >
                      <Calendar className="w-4 h-4" />
                    </Link>
                    <Link
                      href={`/vehicles/${vehicle.id}/edit`}
                      title="Edit Vehicle Data"
                      className="p-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-xl transition shrink-0 active:scale-90"
                    >
                      <Edit className="w-4 h-4" />
                    </Link>
                    <button
                      type="button"
                      onClick={() => handleDeleteVehicle(vehicle)}
                      title="Delete Vehicle"
                      className="p-2 bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-900/60 rounded-xl transition shrink-0 active:scale-90"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              );
            })
          )}
        </div>
      </div>

      {/* Interactive Popup Carousel Modal for Vehicle Photos */}
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
            <div className="flex items-center justify-between p-4 sm:p-5 border-b border-slate-800 bg-slate-900/80 backdrop-blur-md z-20">
              <div>
                <h3 className="text-base sm:text-lg font-extrabold text-white flex items-center gap-2">
                  <span>🚗</span>
                  <span>{selectedGallery.name}</span>
                </h3>
                <p className="text-xs text-slate-400 font-mono">
                  License Plate: <span className="text-amber-400 font-bold">{selectedGallery.licensePlate}</span>
                </p>
              </div>

              <div className="flex items-center space-x-3">
                <span className="px-3 py-1 bg-slate-800 text-slate-300 font-mono text-xs font-bold rounded-full border border-slate-700">
                  Photo <span className="text-emerald-400 font-extrabold">{selectedGallery.currentIndex + 1}</span> of {selectedGallery.images.length}
                </span>

                <button
                  type="button"
                  onClick={() => setModalOpen(false)}
                  className="p-2 text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-full transition shadow-md"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>
            </div>

            {/* Main Carousel Stage */}
            <div className="relative flex-1 bg-black flex items-center justify-center min-h-[350px] sm:min-h-[480px] overflow-hidden">
              <img
                src={selectedGallery.images[selectedGallery.currentIndex]}
                className="max-h-full max-w-full object-contain rounded-xl shadow-2xl transition-all duration-300"
                alt={selectedGallery.name}
              />

              {selectedGallery.images.length > 1 && (
                <>
                  <button
                    type="button"
                    onClick={prevImage}
                    className="absolute left-4 z-20 p-3 bg-black/60 hover:bg-black/90 text-white rounded-full backdrop-blur-md transition border border-white/20 shadow-xl cursor-pointer"
                  >
                    <ChevronLeft className="w-6 h-6" />
                  </button>
                  <button
                    type="button"
                    onClick={nextImage}
                    className="absolute right-4 z-20 p-3 bg-black/60 hover:bg-black/90 text-white rounded-full backdrop-blur-md transition border border-white/20 shadow-xl cursor-pointer"
                  >
                    <ChevronRight className="w-6 h-6" />
                  </button>
                </>
              )}
            </div>

            {/* Thumbnail Strip */}
            {selectedGallery.images.length > 1 && (
              <div className="p-3 bg-slate-950 border-t border-slate-800 flex items-center justify-center gap-2 overflow-x-auto z-20">
                {selectedGallery.images.map((img, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => setSelectedGallery((prev) => ({ ...prev, currentIndex: idx }))}
                    className={`w-14 h-10 rounded-lg overflow-hidden border transition flex-shrink-0 cursor-pointer ${
                      selectedGallery.currentIndex === idx ? 'ring-2 ring-emerald-500 opacity-100 border-emerald-500' : 'border-slate-700 opacity-50 hover:opacity-100'
                    }`}
                  >
                    <img src={img} className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>
      )}
    </AppLayout>
  );
}
