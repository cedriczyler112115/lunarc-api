import React, { useState } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  Grid,
  Search,
  SlidersHorizontal,
  Users,
  Fuel,
  CheckCircle2,
  Calendar,
  Sparkles,
  ArrowRight,
  ShieldCheck,
  Plus,
  User,
  Eye,
  X,
  ChevronLeft,
  ChevronRight,
  ChevronDown
} from 'lucide-react';

export default function AllListing({
  vehicles = [],
  vehicleTypes = [],
  totalFleetCount = 0,
  availableFleetCount = 0,
  filters = {}
}) {
  const { auth } = usePage().props || {};

  // Safe array extractions
  const vehicleList = Array.isArray(vehicles)
    ? vehicles
    : (vehicles && typeof vehicles === 'object' && Array.isArray(vehicles.data) ? vehicles.data : []);

  const typeList = Array.isArray(vehicleTypes)
    ? vehicleTypes
    : (vehicleTypes && typeof vehicleTypes === 'object' && Array.isArray(vehicleTypes.data) ? vehicleTypes.data : []);

  const activeFilters = (filters && typeof filters === 'object' && !Array.isArray(filters)) ? filters : {};

  const [search, setSearch] = useState(activeFilters.search || '');
  const [selectedType, setSelectedType] = useState(activeFilters.vehicle_type_id || '');
  const [selectedStatus, setSelectedStatus] = useState(activeFilters.status || '');
  const [selectedSort, setSelectedSort] = useState(activeFilters.sort || 'latest');
  const [selectedTransmission, setSelectedTransmission] = useState(activeFilters.transmission || '');
  const [selectedFuelType, setSelectedFuelType] = useState(activeFilters.fuel_type || '');
  const [selectedSeats, setSelectedSeats] = useState(activeFilters.seats || '');
  const [minPrice, setMinPrice] = useState(activeFilters.min_price || '');
  const [maxPrice, setMaxPrice] = useState(activeFilters.max_price || '');

  const [mobileFilterOpen, setMobileFilterOpen] = useState(false);

  // Gallery Modal state
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedGallery, setSelectedGallery] = useState({
    name: '',
    licensePlate: '',
    images: [],
    currentIndex: 0,
  });

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

  const handleFilterSubmit = (e) => {
    if (e) e.preventDefault();
    const query = {};
    if (search) query.search = search;
    if (selectedType) query.vehicle_type_id = selectedType;
    if (selectedStatus) query.status = selectedStatus;
    if (selectedSort && selectedSort !== 'latest') query.sort = selectedSort;
    if (selectedTransmission) query.transmission = selectedTransmission;
    if (selectedFuelType) query.fuel_type = selectedFuelType;
    if (selectedSeats) query.seats = selectedSeats;
    if (minPrice) query.min_price = minPrice;
    if (maxPrice) query.max_price = maxPrice;

    router.get('/vehicles/all-listing', query, { preserveState: true, replace: true });
  };

  const handleClearFilters = () => {
    setSearch('');
    setSelectedType('');
    setSelectedStatus('');
    setSelectedSort('latest');
    setSelectedTransmission('');
    setSelectedFuelType('');
    setSelectedSeats('');
    setMinPrice('');
    setMaxPrice('');
    router.get('/vehicles/all-listing', {}, { preserveState: true, replace: true });
  };

  const openGallery = (vehicle) => {
    const formattedImages = getVehicleImages(vehicle);
    setSelectedGallery({
      name: vehicle?.name || 'Vehicle',
      licensePlate: vehicle?.license_plate || '',
      images: formattedImages,
      currentIndex: 0,
    });
    setModalOpen(true);
  };

  const nextImage = () => {
    setSelectedGallery((prev) => ({
      ...prev,
      currentIndex: (prev.currentIndex + 1) % (prev.images.length || 1),
    }));
  };

  const prevImage = () => {
    setSelectedGallery((prev) => ({
      ...prev,
      currentIndex: (prev.currentIndex - 1 + (prev.images.length || 1)) % (prev.images.length || 1),
    }));
  };

  const hasActiveFilters = Boolean(
    search || selectedType || selectedStatus || (selectedSort && selectedSort !== 'latest') ||
    selectedTransmission || selectedFuelType || selectedSeats || minPrice || maxPrice
  );

  return (
    <AppLayout title="All Listing">
      <Head title="All Listing" />

      <div className="space-y-6 pb-6">
        {/* Header Slot */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 transition-colors">
          <div>
            <h2 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <Grid className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
              All Listing
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Browse, filter, and inspect all car listings across Mindanao hosts and fleets.
            </p>
          </div>
          <div className="flex items-center gap-2.5">
            <Link
              href="/vehicles"
              className="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl shadow-xs transition duration-150 border border-gray-200 dark:border-gray-700"
            >
              <User className="w-4 h-4 mr-1.5 opacity-70" />
              View My Fleet ({auth?.user?.name || 'User'})
            </Link>
            <Link
              href="/vehicles/create"
              className="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-md transition duration-150"
            >
              <Plus className="w-4 h-4 mr-1.5" />
              + Register Vehicle
            </Link>
          </div>
        </div>

        {/* Comprehensive Vehicle Information Filter Form */}
        <div className="bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 shadow-xs border border-gray-100 dark:border-gray-700 space-y-4">
          <div className="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
            <div className="flex items-center gap-2">
              <SlidersHorizontal className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
              <h3 className="font-extrabold text-sm sm:text-base text-gray-900 dark:text-white">Filter Vehicle Fleet</h3>
              {hasActiveFilters && (
                <span className="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                  Active Filters
                </span>
              )}
            </div>
            <button
              type="button"
              onClick={() => setMobileFilterOpen(!mobileFilterOpen)}
              className="sm:hidden text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1"
            >
              <span>{mobileFilterOpen ? 'Hide Filters' : 'Show All Filters'}</span>
              <ChevronDown className={`w-4 h-4 transition-transform ${mobileFilterOpen ? 'rotate-180' : ''}`} />
            </button>
          </div>

          <form onSubmit={handleFilterSubmit} className={`space-y-4 ${mobileFilterOpen ? 'block' : 'hidden sm:block'}`}>
            {/* Row 1: Search, Vehicle Type, Status, Sort */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
              {/* Search Keyword */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Search Keyword</label>
                <div className="relative">
                  <input
                    type="text"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder="Make, model, color, host..."
                    className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 pl-9"
                  />
                  <Search className="w-4 h-4 text-gray-400 absolute left-3 top-3" />
                </div>
              </div>

              {/* Vehicle Type */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Vehicle Type</label>
                <select
                  value={selectedType}
                  onChange={(e) => setSelectedType(e.target.value)}
                  className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">All Vehicle Types</option>
                  {typeList.map((type) => (
                    <option key={type.id} value={type.id}>
                      {type.name}
                    </option>
                  ))}
                </select>
              </div>

              {/* Status */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Availability Status</label>
                <select
                  value={selectedStatus}
                  onChange={(e) => setSelectedStatus(e.target.value)}
                  className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">All Statuses</option>
                  <option value="available">🟢 Available</option>
                  <option value="rented">🔵 Rented</option>
                  <option value="maintenance">🟡 In Maintenance</option>
                  <option value="out_of_service">🔴 Out of Service</option>
                </select>
              </div>

              {/* Sort By */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Sort By</label>
                <select
                  value={selectedSort}
                  onChange={(e) => setSelectedSort(e.target.value)}
                  className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="latest">Newest Registered</option>
                  <option value="price_asc">Price: Low to High</option>
                  <option value="price_desc">Price: High to Low</option>
                  <option value="name_asc">Vehicle Name (A-Z)</option>
                  <option value="seats_desc">Seats: Most to Least</option>
                </select>
              </div>
            </div>

            {/* Row 2: Transmission, Fuel Type, Min Seats, Price Min/Max */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
              {/* Transmission */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Transmission</label>
                <select
                  value={selectedTransmission}
                  onChange={(e) => setSelectedTransmission(e.target.value)}
                  className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">All Transmissions</option>
                  <option value="Automatic">Automatic</option>
                  <option value="Manual">Manual</option>
                </select>
              </div>

              {/* Fuel Type */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Fuel Type</label>
                <select
                  value={selectedFuelType}
                  onChange={(e) => setSelectedFuelType(e.target.value)}
                  className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">All Fuel Types</option>
                  <option value="Gasoline">Gasoline</option>
                  <option value="Diesel">Diesel</option>
                  <option value="Hybrid">Hybrid</option>
                  <option value="Electric">Electric</option>
                </select>
              </div>

              {/* Min Seats */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Minimum Seats</label>
                <select
                  value={selectedSeats}
                  onChange={(e) => setSelectedSeats(e.target.value)}
                  className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">Any Capacity</option>
                  <option value="4">4+ Seats</option>
                  <option value="5">5+ Seats</option>
                  <option value="7">7+ Seats</option>
                  <option value="10">10+ Seats (Vans)</option>
                  <option value="14">14+ Seats (Commuter)</option>
                </select>
              </div>

              {/* Daily Rate Range */}
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Daily Rate (₱)</label>
                <div className="grid grid-cols-2 gap-2">
                  <input
                    type="number"
                    value={minPrice}
                    onChange={(e) => setMinPrice(e.target.value)}
                    placeholder="Min"
                    className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                  />
                  <input
                    type="number"
                    value={maxPrice}
                    onChange={(e) => setMaxPrice(e.target.value)}
                    placeholder="Max"
                    className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                  />
                </div>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex items-center justify-end gap-2 pt-2">
              {hasActiveFilters && (
                <button
                  type="button"
                  onClick={handleClearFilters}
                  className="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-bold rounded-xl transition"
                >
                  Clear Filters
                </button>
              )}
              <button
                type="submit"
                className="py-2.5 px-6 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center gap-1.5"
              >
                <Search className="w-4 h-4" />
                Apply Filters
              </button>
            </div>
          </form>
        </div>

        {/* Vehicle Listings Grid */}
        {vehicleList.length === 0 ? (
          <div className="p-12 text-center bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 space-y-4">
            <div className="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
              <Car className="w-8 h-8" />
            </div>
            <div>
              <h4 className="font-extrabold text-lg text-gray-900 dark:text-white">No Vehicles Match Your Filters</h4>
              <p className="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto mt-1">
                Try broadening your search keywords or resetting specific filters to see more listings.
              </p>
            </div>
            <div className="pt-2">
              <button
                onClick={handleClearFilters}
                className="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition"
              >
                Reset All Filters
              </button>
            </div>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {vehicleList.map((vehicle) => {
              const imgList = getVehicleImages(vehicle);
              const imgCount = imgList.length;
              const typeName = vehicle?.vehicle_type?.name || vehicle?.vehicleType?.name || null;
              const ownerName = vehicle?.user?.name || vehicle?.user?.first_name || 'Verified Host';

              return (
                <div
                  key={vehicle.id}
                  className="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden"
                >
                  {/* Image Cover */}
                  <div
                    onClick={() => openGallery(vehicle)}
                    className="relative aspect-[16/10] bg-gray-100 dark:bg-gray-900 overflow-hidden cursor-pointer"
                  >
                    {vehicle?.image_path ? (
                      <img
                        src={vehicle.image_path.startsWith('http') ? vehicle.image_path : `/${vehicle.image_path}`}
                        alt={vehicle.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                    ) : (
                      <div className="w-full h-full flex flex-col items-center justify-center text-gray-400">
                        <Car className="w-12 h-12 mb-1 opacity-50" />
                        <span className="text-xs font-semibold">No Image Uploaded</span>
                      </div>
                    )}

                    {/* Status Badge Overlay */}
                    <div className="absolute top-3 left-3 flex items-center gap-1.5">
                      {vehicle.is_rented_today ? (
                        <span className="px-2.5 py-1 rounded-full text-xs font-black bg-blue-600 text-white shadow-md shadow-blue-600/30 flex items-center gap-1">
                          <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                          Rented
                        </span>
                      ) : vehicle.status === 'available' ? (
                        <span className="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-md shadow-emerald-500/30 flex items-center gap-1">
                          <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                          Available
                        </span>
                      ) : vehicle.status === 'maintenance' ? (
                        <span className="px-2.5 py-1 rounded-full text-xs font-black bg-amber-500 text-white shadow-md shadow-amber-500/30">
                          In Maintenance
                        </span>
                      ) : (
                        <span className="px-2.5 py-1 rounded-full text-xs font-black bg-rose-500 text-white shadow-md shadow-rose-500/30">
                          Out of Service
                        </span>
                      )}

                      {typeName && (
                        <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-900/80 text-white backdrop-blur-xs">
                          {typeName}
                        </span>
                      )}
                    </div>

                    {/* Gallery Indicator */}
                    {imgCount > 1 && (
                      <div className="absolute bottom-3 right-3 px-2 py-0.5 rounded-lg bg-black/70 text-white text-[11px] font-bold flex items-center gap-1 backdrop-blur-xs">
                        📷 {imgCount} Photos
                      </div>
                    )}
                  </div>

                  {/* Card Body */}
                  <div className="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                      {/* Title & Year */}
                      <div className="flex items-start justify-between gap-2">
                        <div>
                          <h4 className="font-extrabold text-lg text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                            {vehicle.name}
                          </h4>
                          <p className="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">
                            {vehicle.make} {vehicle.model} ({vehicle.year}) &bull;{' '}
                            <span className="font-mono text-gray-700 dark:text-gray-300 font-bold">{vehicle.license_plate}</span>
                          </p>
                        </div>
                      </div>

                      {/* Key Specs Pills */}
                      <div className="grid grid-cols-3 gap-2 mt-4 text-center">
                        <div className="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
                          <span className="text-[10px] uppercase font-bold text-gray-400 block">Gearbox</span>
                          <span className="text-xs font-black text-gray-800 dark:text-gray-200 mt-0.5 block truncate">{vehicle.transmission}</span>
                        </div>
                        <div className="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
                          <span className="text-[10px] uppercase font-bold text-gray-400 block">Fuel</span>
                          <span className="text-xs font-black text-gray-800 dark:text-gray-200 mt-0.5 block truncate">{vehicle.fuel_type}</span>
                        </div>
                        <div className="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
                          <span className="text-[10px] uppercase font-bold text-gray-400 block">Capacity</span>
                          <span className="text-xs font-black text-gray-800 dark:text-gray-200 mt-0.5 block truncate">{vehicle.seats} Seats</span>
                        </div>
                      </div>

                      {/* Host / Owner & Color */}
                      <div className="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <div className="flex items-center gap-2">
                          <div className="w-5 h-5 rounded-full bg-emerald-500 text-white font-bold text-[9px] flex items-center justify-center shrink-0">
                            {ownerName[0] || 'V'}
                          </div>
                          <span className="font-medium text-gray-700 dark:text-gray-300 truncate max-w-[130px]">
                            {ownerName}
                          </span>
                        </div>
                        {vehicle.color && (
                          <span className="text-[11px] font-medium text-gray-400">
                            Color: <span className="font-semibold text-gray-600 dark:text-gray-300">{vehicle.color}</span>
                          </span>
                        )}
                      </div>
                    </div>

                    {/* Bottom Price & Actions */}
                    <div className="pt-3 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-between gap-2">
                      <div>
                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Daily Rate</span>
                        <span className="text-xl font-black text-emerald-600 dark:text-emerald-400">
                          ₱{Number(vehicle.daily_rate || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </span>
                      </div>
                      <div className="flex items-center gap-1.5">
                        <Link
                          href={`/vehicles/${vehicle.id}`}
                          className="p-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-xs transition"
                          title="View Specs & Schedule"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        {vehicle.status === 'available' && !vehicle.is_rented_today ? (
                          <Link
                            href={`/bookings/create?vehicle_id=${vehicle.id}`}
                            className="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1"
                          >
                            Book Now &rarr;
                          </Link>
                        ) : (
                          <span className="py-2.5 px-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 font-bold text-xs cursor-not-allowed">
                            {vehicle.is_rented_today ? 'Rented' : 'Unavailable'}
                          </span>
                        )}
                      </div>
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
