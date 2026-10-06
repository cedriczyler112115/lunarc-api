import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  Upload,
  ArrowLeft,
  Trash2,
  Star,
  AlertCircle,
  Tag,
  Settings,
  FileText,
  Sparkles,
  ShieldCheck,
  CheckCircle2,
  Image as ImageIcon,
  DollarSign,
  Users,
  Eye
} from 'lucide-react';
import confirmDialog from '@/Utils/confirm';

export default function Edit({ vehicle, users = [], vehicleTypes = [] }) {
  const { data, setData, post, processing, errors } = useForm({
    _method: 'PUT',
    user_id: vehicle.user_id || '',
    vehicle_type_id: vehicle.vehicle_type_id || '',
    name: vehicle.name || '',
    make: vehicle.make || '',
    model: vehicle.model || '',
    year: vehicle.year || new Date().getFullYear(),
    license_plate: vehicle.license_plate || '',
    color: vehicle.color || '',
    transmission: vehicle.transmission || 'Automatic',
    fuel_type: vehicle.fuel_type || 'Gasoline',
    seats: vehicle.seats || 5,
    daily_rate: vehicle.daily_rate || '',
    status: vehicle.status || 'available',
    description: vehicle.description || '',
    images: [],
  });

  const [newImagePreviews, setNewImagePreviews] = useState([]);

  const existingImages = Array.isArray(vehicle.images) ? vehicle.images : [];

  const handleNewImagesChange = (e) => {
    const files = Array.from(e.target.files);
    setData('images', files);
    const urls = files.map((file) => URL.createObjectURL(file));
    setNewImagePreviews(urls);
  };

  const handleSetPrimaryPhoto = (imagePath) => {
    router.patch(
      `/vehicles/${vehicle.id}/primary-photo`,
      { image_path: imagePath },
      { preserveScroll: true }
    );
  };

  const handleDeletePhoto = (imagePath) => {
    confirmDialog({
      title: 'Delete Vehicle Photo',
      content: 'Are you sure you want to delete this photo from the vehicle gallery?',
      type: 'red',
      confirmButtonText: 'Delete Photo',
      confirmButtonClass: 'btn-red',
      onConfirm: () => {
        router.delete(`/vehicles/${vehicle.id}/photo`, {
          data: { image_path: imagePath },
          preserveScroll: true,
        });
      },
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    post(`/vehicles/${vehicle.id}`, {
      forceFormData: true,
    });
  };

  const hasErrors = Object.keys(errors).length > 0;

  return (
    <AppLayout title={`Edit ${vehicle.name}`}>
      <Head title={`Edit ${vehicle.name}`} />

      <div className="max-w-4xl mx-auto space-y-6 pb-12">
        {/* Navigation Top Bar */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <Link
            href="/vehicles"
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to My Fleet
          </Link>

          <div className="flex items-center gap-2">
            <span className="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 font-mono text-xs font-bold rounded-xl border border-emerald-200 dark:border-emerald-800">
              Plate: {vehicle.license_plate}
            </span>
            <Link
              href={`/vehicles/${vehicle.id}`}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            >
              <Eye className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
              View Specs Page
            </Link>
          </div>
        </div>

        {/* Validation Errors Global Banner */}
        {hasErrors && (
          <div className="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 flex items-start gap-3 text-rose-800 dark:text-rose-200">
            <AlertCircle className="w-5 h-5 flex-shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
            <div className="space-y-1 text-xs">
              <p className="font-bold">Please check the form for errors:</p>
              <ul className="list-disc list-inside space-y-0.5 opacity-90">
                {Object.entries(errors).map(([key, msg]) => (
                  <li key={key}>{msg}</li>
                ))}
              </ul>
            </div>
          </div>
        )}

        {/* Section 1: Photo Gallery & Cover Management */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4 transition-colors duration-300">
          <div className="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
            <div>
              <h2 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <ImageIcon className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                1. Vehicle Photo Gallery & Cover Photo
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Manage uploaded photos, select the primary cover photo, or delete unwanted images.
              </p>
            </div>
            <span className="px-2.5 py-1 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs font-mono font-bold">
              {existingImages.length} {existingImages.length === 1 ? 'Photo' : 'Photos'}
            </span>
          </div>

          {existingImages.length === 0 ? (
            <div className="p-6 text-center border border-dashed border-gray-200 dark:border-gray-800 rounded-2xl">
              <ImageIcon className="w-8 h-8 text-gray-400 mx-auto mb-2" />
              <p className="text-xs text-gray-500 dark:text-gray-400">No photos uploaded for this vehicle yet.</p>
            </div>
          ) : (
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
              {existingImages.map((imgPath, idx) => {
                const isPrimary = vehicle.image_path === imgPath;
                return (
                  <div
                    key={idx}
                    className={`relative aspect-video rounded-2xl bg-gray-100 dark:bg-gray-950 border overflow-hidden group transition-all ${
                      isPrimary
                        ? 'border-emerald-500 ring-2 ring-emerald-500/30 shadow-md'
                        : 'border-gray-200 dark:border-gray-800'
                    }`}
                  >
                    <img src={`/${imgPath}`} alt={`Photo ${idx + 1}`} className="w-full h-full object-cover" />

                    {isPrimary && (
                      <span className="absolute top-2 left-2 bg-emerald-600 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-md flex items-center gap-1">
                        ★ Primary Cover
                      </span>
                    )}

                    <div className="absolute inset-0 bg-gray-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                      {!isPrimary && (
                        <button
                          type="button"
                          onClick={() => handleSetPrimaryPhoto(imgPath)}
                          className="p-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-xs shadow-lg transition-transform active:scale-95"
                          title="Set as Primary Cover Photo"
                        >
                          <Star className="w-4 h-4 fill-white" />
                        </button>
                      )}
                      <button
                        type="button"
                        onClick={() => handleDeletePhoto(imgPath)}
                        className="p-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg transition-transform active:scale-95"
                        title="Delete Photo"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>

        {/* Main Edit Form */}
        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Section 2: Identification & Basic Info */}
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5 transition-colors duration-300">
            <div className="border-b border-gray-100 dark:border-gray-800 pb-3">
              <h2 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                2. Vehicle Identification & Basic Details
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Core identity information and license plate registration
              </p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Vehicle Title / Display Name *
                </label>
                <input
                  type="text"
                  value={data.name}
                  onChange={(e) => setData('name', e.target.value)}
                  placeholder="e.g. Toyota Fortuner 2.8 V 4x2"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.name ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.name && <p className="text-xs text-rose-500 mt-1">{errors.name}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  License Plate Number *
                  <span className="text-[10px] text-gray-400 font-normal ml-1.5">(Unique per vehicle)</span>
                </label>
                <input
                  type="text"
                  value={data.license_plate}
                  onChange={(e) => setData('license_plate', e.target.value.toUpperCase())}
                  placeholder="e.g. ABC 1234"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white font-mono uppercase focus:outline-none focus:border-emerald-500 ${
                    errors.license_plate ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.license_plate ? (
                  <p className="text-xs text-rose-500 font-semibold mt-1 flex items-center gap-1">
                    <AlertCircle className="w-3.5 h-3.5 flex-shrink-0" />
                    {errors.license_plate}
                  </p>
                ) : (
                  <p className="text-[11px] text-gray-400 mt-1">Must be unique across all registered vehicles and users.</p>
                )}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Make / Manufacturer *
                </label>
                <input
                  type="text"
                  value={data.make}
                  onChange={(e) => setData('make', e.target.value)}
                  placeholder="e.g. Toyota"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.make ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.make && <p className="text-xs text-rose-500 mt-1">{errors.make}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Model Name *
                </label>
                <input
                  type="text"
                  value={data.model}
                  onChange={(e) => setData('model', e.target.value)}
                  placeholder="e.g. Fortuner"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.model ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.model && <p className="text-xs text-rose-500 mt-1">{errors.model}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Manufacturing Year *
                </label>
                <input
                  type="number"
                  value={data.year}
                  onChange={(e) => setData('year', e.target.value)}
                  min="1990"
                  max={new Date().getFullYear() + 1}
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.year ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.year && <p className="text-xs text-rose-500 mt-1">{errors.year}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Exterior Color
                </label>
                <input
                  type="text"
                  value={data.color}
                  onChange={(e) => setData('color', e.target.value)}
                  placeholder="e.g. Metallic Black"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.color ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.color && <p className="text-xs text-rose-500 mt-1">{errors.color}</p>}
              </div>
            </div>
          </div>

          {/* Section 3: Classification & Ownership */}
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5 transition-colors duration-300">
            <div className="border-b border-gray-100 dark:border-gray-800 pb-3">
              <h2 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <Tag className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                3. Classification, Ownership & Fleet Status
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Assign host ownership, category classification, and operational availability
              </p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Vehicle Category / Type
                </label>
                <select
                  value={data.vehicle_type_id}
                  onChange={(e) => setData('vehicle_type_id', e.target.value)}
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.vehicle_type_id ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                >
                  <option value="">Select Category</option>
                  {vehicleTypes.map((t) => (
                    <option key={t.id} value={t.id}>
                      {t.name}
                    </option>
                  ))}
                </select>
                {errors.vehicle_type_id && <p className="text-xs text-rose-500 mt-1">{errors.vehicle_type_id}</p>}
              </div>

              {users.length > 0 && (
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Car Host / Owner
                  </label>
                  <select
                    value={data.user_id}
                    onChange={(e) => setData('user_id', e.target.value)}
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      errors.user_id ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  >
                    <option value="">Current Authenticated User</option>
                    {users.map((u) => (
                      <option key={u.id} value={u.id}>
                        {u.name} ({u.email})
                      </option>
                    ))}
                  </select>
                  {errors.user_id && <p className="text-xs text-rose-500 mt-1">{errors.user_id}</p>}
                </div>
              )}

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Fleet Status *
                </label>
                <select
                  value={data.status}
                  onChange={(e) => setData('status', e.target.value)}
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.status ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                >
                  <option value="available">Available (Active in listings)</option>
                  <option value="maintenance">Under Maintenance</option>
                  <option value="out_of_service">Out of Service</option>
                </select>
                {errors.status && <p className="text-xs text-rose-500 mt-1">{errors.status}</p>}
              </div>
            </div>
          </div>

          {/* Section 4: Specifications & Pricing */}
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5 transition-colors duration-300">
            <div className="border-b border-gray-100 dark:border-gray-800 pb-3">
              <h2 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <Settings className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                4. Mechanical Specifications & Rental Rate
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Drivetrain details, seating capacity, and daily pricing
              </p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Transmission *
                </label>
                <select
                  value={data.transmission}
                  onChange={(e) => setData('transmission', e.target.value)}
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.transmission ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                >
                  <option value="Automatic">Automatic</option>
                  <option value="Manual">Manual</option>
                </select>
                {errors.transmission && <p className="text-xs text-rose-500 mt-1">{errors.transmission}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Fuel Type *
                </label>
                <select
                  value={data.fuel_type}
                  onChange={(e) => setData('fuel_type', e.target.value)}
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.fuel_type ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                >
                  <option value="Gasoline">Gasoline</option>
                  <option value="Diesel">Diesel</option>
                  <option value="Hybrid">Hybrid</option>
                  <option value="Electric">Electric</option>
                </select>
                {errors.fuel_type && <p className="text-xs text-rose-500 mt-1">{errors.fuel_type}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Seating Capacity *
                </label>
                <input
                  type="number"
                  value={data.seats}
                  onChange={(e) => setData('seats', e.target.value)}
                  min="1"
                  max="50"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.seats ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.seats && <p className="text-xs text-rose-500 mt-1">{errors.seats}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Base Daily Rate (₱) *
                </label>
                <input
                  type="number"
                  step="0.01"
                  value={data.daily_rate}
                  onChange={(e) => setData('daily_rate', e.target.value)}
                  placeholder="e.g. 2500"
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs font-bold text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-emerald-500 ${
                    errors.daily_rate ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.daily_rate && <p className="text-xs text-rose-500 mt-1">{errors.daily_rate}</p>}
              </div>
            </div>
          </div>

          {/* Section 5: Description & Notes */}
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5 transition-colors duration-300">
            <div className="border-b border-gray-100 dark:border-gray-800 pb-3">
              <h2 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <FileText className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                5. Vehicle Description & Features
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Highlights, amenities, inclusions, or special instructions for renters
              </p>
            </div>

            <div>
              <textarea
                rows="4"
                value={data.description}
                onChange={(e) => setData('description', e.target.value)}
                placeholder="e.g. Equipped with Apple CarPlay / Android Auto, dashcam, Bluetooth audio, leather interior, pristine condition."
                className={`w-full px-4 py-3 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                  errors.description ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                }`}
              />
              {errors.description && <p className="text-xs text-rose-500 mt-1">{errors.description}</p>}
            </div>
          </div>

          {/* Section 6: Upload Additional Photos */}
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4 transition-colors duration-300">
            <div className="border-b border-gray-100 dark:border-gray-800 pb-3">
              <h2 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <Upload className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                6. Add More Photos to Gallery
              </h2>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Upload new photos to append to the vehicle gallery (JPEG, PNG, WEBP up to 5MB each)
              </p>
            </div>

            <div className="relative border-2 border-dashed border-gray-200 dark:border-gray-800 hover:border-emerald-500 rounded-3xl p-6 text-center bg-gray-50/50 dark:bg-gray-950/50 transition-colors">
              <input
                type="file"
                multiple
                accept="image/*"
                onChange={handleNewImagesChange}
                className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              />
              <Upload className="w-8 h-8 text-emerald-600 dark:text-emerald-400 mx-auto mb-2" />
              <p className="text-xs font-bold text-gray-700 dark:text-gray-300">
                Click or drag new photos here to append
              </p>
              <p className="text-[10px] text-gray-400 mt-1">Select one or multiple photos to upload</p>
            </div>

            {newImagePreviews.length > 0 && (
              <div className="space-y-2 pt-2">
                <p className="text-xs font-bold text-gray-600 dark:text-gray-400">
                  New photos selected ({newImagePreviews.length}):
                </p>
                <div className="grid grid-cols-3 sm:grid-cols-4 gap-3">
                  {newImagePreviews.map((url, index) => (
                    <div
                      key={index}
                      className="relative aspect-video rounded-2xl bg-gray-100 dark:bg-gray-950 border border-emerald-500/50 overflow-hidden shadow-sm"
                    >
                      <img src={url} alt={`New upload preview ${index + 1}`} className="w-full h-full object-cover" />
                      <span className="absolute bottom-1 right-1 bg-emerald-600/90 text-[8px] font-black text-white px-1.5 py-0.5 rounded">
                        + New
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            )}
            {errors.images && <p className="text-xs text-rose-500 mt-1">{errors.images}</p>}
          </div>

          {/* Form Action Buttons */}
          <div className="flex items-center justify-end gap-3 pt-2">
            <Link
              href="/vehicles"
              className="px-5 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            >
              Cancel
            </Link>
            <button
              type="submit"
              disabled={processing}
              className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-95 transition-all disabled:opacity-50"
            >
              {processing ? 'Saving Changes...' : 'Update Vehicle Details'}
            </button>
          </div>
        </form>
      </div>
    </AppLayout>
  );
}
