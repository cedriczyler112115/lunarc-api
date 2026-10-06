import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  Upload,
  ArrowLeft,
  CheckCircle2,
  AlertCircle,
  Plus,
  Trash2,
  Sparkles
} from 'lucide-react';

export default function Create({ users = [], vehicleTypes = [] }) {
  const { data, setData, post, processing, errors } = useForm({
    user_id: '',
    vehicle_type_id: '',
    name: '',
    make: '',
    model: '',
    year: new Date().getFullYear(),
    license_plate: '',
    color: '',
    transmission: 'Automatic',
    fuel_type: 'Gasoline',
    seats: 5,
    daily_rate: '',
    status: 'available',
    description: '',
    images: [],
  });

  const [previewUrls, setPreviewUrls] = useState([]);

  const handleImageChange = (e) => {
    const files = Array.from(e.target.files);
    setData('images', files);

    const urls = files.map((file) => URL.createObjectURL(file));
    setPreviewUrls(urls);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/vehicles', {
      forceFormData: true,
    });
  };

  const hasErrors = Object.keys(errors).length > 0;

  return (
    <AppLayout title="Register Vehicle">
      <Head title="Register Vehicle" />

      <div className="max-w-4xl mx-auto space-y-6 pb-12">
        <div className="flex items-center justify-between">
          <Link
            href="/vehicles"
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Fleet
          </Link>
        </div>

        {/* Validation Errors Global Banner */}
        {hasErrors && (
          <div className="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 flex items-start gap-3 text-rose-800 dark:text-rose-200">
            <AlertCircle className="w-5 h-5 flex-shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
            <div className="space-y-1 text-xs">
              <p className="font-bold">Please correct the following errors before submitting:</p>
              <ul className="list-disc list-inside space-y-0.5 opacity-90">
                {Object.entries(errors).map(([key, msg]) => (
                  <li key={key}>{msg}</li>
                ))}
              </ul>
            </div>
          </div>
        )}

        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6 transition-colors duration-300">
          <div className="space-y-1 border-b border-gray-100 dark:border-gray-800 pb-4">
            <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
              <Car className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
              Register New Vehicle
            </h1>
            <p className="text-xs text-gray-500 dark:text-gray-400">
              Fill in complete vehicle specifications, rates, and upload photo gallery
            </p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Owner, Category & Status Selection */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
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

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Initial Fleet Status *
                </label>
                <select
                  value={data.status}
                  onChange={(e) => setData('status', e.target.value)}
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.status ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                >
                  <option value="available">Available</option>
                  <option value="maintenance">Under Maintenance</option>
                  <option value="out_of_service">Out of Service</option>
                </select>
                {errors.status && <p className="text-xs text-rose-500 mt-1">{errors.status}</p>}
              </div>
            </div>

            {/* Main Specs: Display Name & Unique Plate Number */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Vehicle Title / Display Name *
                </label>
                <input
                  type="text"
                  value={data.name}
                  onChange={(e) => setData('name', e.target.value)}
                  placeholder="e.g. Toyota Innova XE Automatic 2024"
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
                  <p className="text-[11px] text-gray-400 mt-1">Must be unique across all users and fleet listings.</p>
                )}
              </div>
            </div>

            {/* Make, Model, Year */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                  Model *
                </label>
                <input
                  type="text"
                  value={data.model}
                  onChange={(e) => setData('model', e.target.value)}
                  placeholder="e.g. Innova"
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
            </div>

            {/* Transmission, Fuel, Color, Seats */}
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
            </div>

            {/* Pricing & Description */}
            <div className="grid grid-cols-1 gap-4">
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
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs font-bold text-amber-600 dark:text-amber-400 focus:outline-none focus:border-emerald-500 ${
                    errors.daily_rate ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.daily_rate && <p className="text-xs text-rose-500 mt-1">{errors.daily_rate}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Vehicle Description & Features
                </label>
                <textarea
                  rows="3"
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                  placeholder="e.g. Bluetooth audio, Dashcam installed, Apple CarPlay / Android Auto, Leather seats, pristine condition."
                  className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                    errors.description ? 'border-rose-500' : 'border-gray-200 dark:border-gray-800'
                  }`}
                />
                {errors.description && <p className="text-xs text-rose-500 mt-1">{errors.description}</p>}
              </div>
            </div>

            {/* Photos Uploader */}
            <div className="space-y-3 pt-2">
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Vehicle Photos (Upload Gallery)
              </label>

              <div className="relative border-2 border-dashed border-gray-200 dark:border-gray-800 hover:border-emerald-500 rounded-3xl p-6 text-center bg-gray-50/50 dark:bg-gray-950/50 transition-colors">
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  onChange={handleImageChange}
                  className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />
                <Upload className="w-8 h-8 text-emerald-600 dark:text-emerald-400 mx-auto mb-2" />
                <p className="text-xs font-bold text-gray-700 dark:text-gray-300">
                  Click or drag photos here to upload
                </p>
                <p className="text-[10px] text-gray-400 mt-1">JPEG, PNG, WEBP up to 5MB each</p>
              </div>

              {previewUrls.length > 0 && (
                <div className="grid grid-cols-3 sm:grid-cols-4 gap-3 pt-2">
                  {previewUrls.map((url, index) => (
                    <div
                      key={index}
                      className="relative aspect-video rounded-2xl bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden"
                    >
                      <img src={url} alt={`Preview ${index}`} className="w-full h-full object-cover" />
                      {index === 0 && (
                        <span className="absolute top-1 left-1 bg-emerald-600 text-[9px] font-extrabold text-white px-2 py-0.5 rounded-md">
                          Primary Cover
                        </span>
                      )}
                    </div>
                  ))}
                </div>
              )}
              {errors.images && <p className="text-xs text-rose-500 mt-1">{errors.images}</p>}
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
              <Link
                href="/vehicles"
                className="px-5 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
              >
                Cancel
              </Link>
              <button
                type="submit"
                disabled={processing}
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-95 transition-transform disabled:opacity-50"
              >
                {processing ? 'Registering...' : 'Register Vehicle'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
