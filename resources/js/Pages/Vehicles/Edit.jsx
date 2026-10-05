import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  Upload,
  ArrowLeft,
  Trash2,
  Star,
  CheckCircle2,
  AlertCircle
} from 'lucide-react';

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

  const existingImages = Array.isArray(vehicle.images) ? vehicle.images : [];

  const handleSetPrimaryPhoto = (imagePath) => {
    router.patch(
      `/vehicles/${vehicle.id}/primary-photo`,
      { image_path: imagePath },
      { preserveScroll: true }
    );
  };

  const handleDeletePhoto = (imagePath) => {
    if (confirm('Are you sure you want to delete this photo from the vehicle gallery?')) {
      router.delete(`/vehicles/${vehicle.id}/photo`, {
        data: { image_path: imagePath },
        preserveScroll: true,
      });
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    post(`/vehicles/${vehicle.id}`, {
      forceFormData: true,
    });
  };

  return (
    <AppLayout title={`Edit ${vehicle.name}`}>
      <Head title={`Edit ${vehicle.name}`} />

      <div className="max-w-3xl mx-auto space-y-6 pb-6">
        <div className="flex items-center justify-between">
          <Link
            href="/vehicles"
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Vehicles
          </Link>
        </div>

        {/* Existing Photo Gallery Manager */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4 transition-colors duration-300">
          <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <Star className="w-5 h-5 text-amber-500" />
            Manage Vehicle Photo Gallery
          </h2>

          {existingImages.length === 0 ? (
            <p className="text-xs text-gray-500 dark:text-gray-400">No photos uploaded for this vehicle yet.</p>
          ) : (
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
              {existingImages.map((imgPath, idx) => {
                const isPrimary = vehicle.image_path === imgPath;
                return (
                  <div key={idx} className="relative aspect-video rounded-2xl bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden group">
                    <img src={`/${imgPath}`} alt={`Photo ${idx}`} className="w-full h-full object-cover" />
                    
                    {isPrimary && (
                      <span className="absolute top-2 left-2 bg-emerald-500 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-md">
                        Primary Cover
                      </span>
                    )}

                    <div className="absolute inset-0 bg-gray-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                      {!isPrimary && (
                        <button
                          type="button"
                          onClick={() => handleSetPrimaryPhoto(imgPath)}
                          className="p-2 rounded-xl bg-amber-500 text-white font-bold text-xs hover:bg-amber-400"
                          title="Set as Primary Cover"
                        >
                          <Star className="w-4 h-4 fill-white" />
                        </button>
                      )}
                      <button
                        type="button"
                        onClick={() => handleDeletePhoto(imgPath)}
                        className="p-2 rounded-xl bg-rose-600 text-white font-bold text-xs hover:bg-rose-500"
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

        {/* Edit Form */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6 transition-colors duration-300">
          <div className="space-y-1">
            <h1 className="text-xl font-black text-gray-900 dark:text-white">Edit Vehicle Specs</h1>
            <p className="text-xs text-gray-500 dark:text-gray-400">Update rates, specifications, and state details</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Vehicle Category</label>
                <select
                  value={data.vehicle_type_id}
                  onChange={(e) => setData('vehicle_type_id', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                >
                  <option value="">Select Category</option>
                  {vehicleTypes.map((t) => (
                    <option key={t.id} value={t.id}>
                      {t.name}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Vehicle Status *</label>
                <select
                  value={data.status}
                  onChange={(e) => setData('status', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                >
                  <option value="available">Available</option>
                  <option value="maintenance">Under Maintenance</option>
                  <option value="out_of_service">Out of Service</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Display Name *</label>
                <input
                  type="text"
                  value={data.name}
                  onChange={(e) => setData('name', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">License Plate *</label>
                <input
                  type="text"
                  value={data.license_plate}
                  onChange={(e) => setData('license_plate', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white font-mono focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Daily Rate (₱) *</label>
                <input
                  type="number"
                  step="0.01"
                  value={data.daily_rate}
                  onChange={(e) => setData('daily_rate', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-amber-600 dark:text-amber-400 focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Seating Capacity *</label>
                <input
                  type="number"
                  value={data.seats}
                  onChange={(e) => setData('seats', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>
            </div>

            <div className="space-y-2">
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300">Add More Photos to Gallery</label>
              <input
                type="file"
                multiple
                accept="image/*"
                onChange={(e) => setData('images', Array.from(e.target.files))}
                className="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-2xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200"
              />
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
              <Link
                href="/vehicles"
                className="px-5 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold"
              >
                Cancel
              </Link>
              <button
                type="submit"
                disabled={processing}
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20"
              >
                {processing ? 'Saving...' : 'Update Vehicle'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
