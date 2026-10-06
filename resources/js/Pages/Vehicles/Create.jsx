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
  Sparkles,
  Layers,
  Settings,
  DollarSign,
  Image as ImageIcon,
  X
} from 'lucide-react';

export default function Create({ vehicleTypes = [] }) {
  const { data, setData, post, processing, errors } = useForm({
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
  const [clientErrors, setClientErrors] = useState({});

  const handleImageChange = (e) => {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    const newFiles = [...data.images, ...files];
    setData('images', newFiles);

    const urls = files.map((file) => URL.createObjectURL(file));
    setPreviewUrls((prev) => [...prev, ...urls]);

    if (clientErrors.images) {
      setClientErrors((prev) => {
        const next = { ...prev };
        delete next.images;
        return next;
      });
    }
  };

  const removeImage = (indexToRemove) => {
    const remainingFiles = data.images.filter((_, idx) => idx !== indexToRemove);
    setData('images', remainingFiles);
    setPreviewUrls((prev) => prev.filter((_, idx) => idx !== indexToRemove));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const newErrors = {};

    if (!data.name?.trim()) newErrors.name = 'Vehicle title / display name is required.';
    if (!data.license_plate?.trim()) newErrors.license_plate = 'License plate number is required.';
    if (!data.vehicle_type_id) newErrors.vehicle_type_id = 'Please select a vehicle category.';
    if (!data.status) newErrors.status = 'Initial fleet status is required.';
    if (!data.make?.trim()) newErrors.make = 'Make / manufacturer is required.';
    if (!data.model?.trim()) newErrors.model = 'Model is required.';
    if (!data.year) newErrors.year = 'Manufacturing year is required.';
    if (!data.transmission) newErrors.transmission = 'Transmission is required.';
    if (!data.fuel_type) newErrors.fuel_type = 'Fuel type is required.';
    if (!data.color?.trim()) newErrors.color = 'Exterior color is required.';
    if (!data.seats || data.seats < 1) newErrors.seats = 'Valid seating capacity is required.';
    if (!data.daily_rate || Number(data.daily_rate) <= 0) newErrors.daily_rate = 'Valid base daily rate is required.';
    if (!data.description?.trim()) newErrors.description = 'Vehicle description and features are required.';
    if (!data.images || data.images.length === 0) newErrors.images = 'At least one vehicle photo is required. Please upload photos.';

    if (Object.keys(newErrors).length > 0) {
      setClientErrors(newErrors);
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return;
    }

    setClientErrors({});
    post('/vehicles', {
      forceFormData: true,
    });
  };

  const allErrors = { ...errors, ...clientErrors };
  const hasErrors = Object.keys(allErrors).length > 0;

  return (
    <AppLayout title="Register Vehicle">
      <Head title="Register Vehicle" />

      <div className="max-w-5xl mx-auto space-y-6 pb-12">
        {/* Top Navigation */}
        <div className="flex items-center justify-between">
          <Link
            href="/vehicles"
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to My Fleet
          </Link>
        </div>

        {/* Validation Errors Global Banner */}
        {hasErrors && (
          <div className="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 flex items-start gap-3 text-rose-800 dark:text-rose-200 shadow-xs animate-in fade-in duration-200">
            <AlertCircle className="w-5 h-5 flex-shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
            <div className="space-y-1 text-xs">
              <p className="font-bold">Please correct the following required fields before submitting:</p>
              <ul className="list-disc list-inside space-y-0.5 opacity-90">
                {Object.entries(allErrors).map(([key, msg]) => (
                  <li key={key}>{msg}</li>
                ))}
              </ul>
            </div>
          </div>
        )}

        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl shadow-xs transition-colors duration-300 overflow-hidden">
          {/* Header Banner */}
          <div className="p-6 sm:p-8 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-emerald-500/5 via-teal-500/5 to-transparent">
            <div className="flex items-center gap-3.5">
              <div className="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 shrink-0">
                <Car className="w-6 h-6" />
              </div>
              <div>
                <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                  Register New Vehicle
                </h1>
                <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                  All fields are mandatory. Vehicle will be automatically linked to your authenticated account.
                </p>
              </div>
            </div>
          </div>

          <form onSubmit={handleSubmit} className="p-6 sm:p-8 space-y-8">
            {/* Section 1: Vehicle Identity & Classification */}
            <div className="space-y-4">
              <div className="flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-800">
                <Layers className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                <h2 className="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                  Vehicle Identity & Classification
                </h2>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                {/* Vehicle Title / Display Name */}
                <div className="md:col-span-7">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Vehicle Title / Display Name <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={data.name}
                    onChange={(e) => {
                      setData('name', e.target.value);
                      if (clientErrors.name) setClientErrors((p) => ({ ...p, name: null }));
                    }}
                    placeholder="e.g. Toyota Innova XE Automatic 2024"
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.name ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.name && <p className="text-xs text-rose-500 mt-1">{allErrors.name}</p>}
                </div>

                {/* License Plate Number */}
                <div className="md:col-span-5">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    License Plate Number <span className="text-rose-500">*</span>
                    <span className="text-[10px] text-gray-400 font-normal ml-1">(No spaces, all caps)</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={data.license_plate}
                    onChange={(e) => {
                      const val = e.target.value.toUpperCase().replace(/\s+/g, '');
                      setData('license_plate', val);
                      if (clientErrors.license_plate) setClientErrors((p) => ({ ...p, license_plate: null }));
                    }}
                    onKeyDown={(e) => {
                      if (e.key === ' ') {
                        e.preventDefault();
                      }
                    }}
                    placeholder="e.g. ABC1234"
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white font-mono font-black uppercase tracking-wider focus:outline-none focus:border-emerald-500 ${
                      allErrors.license_plate ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.license_plate ? (
                    <p className="text-xs text-rose-500 font-semibold mt-1 flex items-center gap-1">
                      <AlertCircle className="w-3.5 h-3.5 flex-shrink-0" />
                      {allErrors.license_plate}
                    </p>
                  ) : (
                    <p className="text-[10px] text-gray-400 mt-1">Unique plate identifier across all fleet listings.</p>
                  )}
                </div>

                {/* Category / Type */}
                <div className="md:col-span-6">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Vehicle Category / Body Type <span className="text-rose-500">*</span>
                  </label>
                  <select
                    required
                    value={data.vehicle_type_id}
                    onChange={(e) => {
                      setData('vehicle_type_id', e.target.value);
                      if (clientErrors.vehicle_type_id) setClientErrors((p) => ({ ...p, vehicle_type_id: null }));
                    }}
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.vehicle_type_id ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  >
                    <option value="">Select Category (Sedan, SUV, Van, etc.) *</option>
                    {vehicleTypes.map((t) => (
                      <option key={t.id} value={t.id}>
                        {t.name}
                      </option>
                    ))}
                  </select>
                  {allErrors.vehicle_type_id && <p className="text-xs text-rose-500 mt-1">{allErrors.vehicle_type_id}</p>}
                </div>

                {/* Initial Fleet Status */}
                <div className="md:col-span-6">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Initial Fleet Status <span className="text-rose-500">*</span>
                  </label>
                  <select
                    required
                    value={data.status}
                    onChange={(e) => {
                      setData('status', e.target.value);
                      if (clientErrors.status) setClientErrors((p) => ({ ...p, status: null }));
                    }}
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.status ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  >
                    <option value="available">🟢 Available for Bookings</option>
                    <option value="maintenance">🟡 Under Maintenance</option>
                    <option value="out_of_service">🔴 Out of Service / Off</option>
                  </select>
                  {allErrors.status && <p className="text-xs text-rose-500 mt-1">{allErrors.status}</p>}
                </div>
              </div>
            </div>

            {/* Section 2: Technical Specifications & Details */}
            <div className="space-y-4">
              <div className="flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-800">
                <Settings className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                <h2 className="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                  Vehicle Specifications & Mechanics
                </h2>
              </div>

              {/* Row 1: Make, Model, Year */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Make / Manufacturer <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={data.make}
                    onChange={(e) => {
                      setData('make', e.target.value);
                      if (clientErrors.make) setClientErrors((p) => ({ ...p, make: null }));
                    }}
                    placeholder="e.g. Toyota"
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.make ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.make && <p className="text-xs text-rose-500 mt-1">{allErrors.make}</p>}
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Model <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={data.model}
                    onChange={(e) => {
                      setData('model', e.target.value);
                      if (clientErrors.model) setClientErrors((p) => ({ ...p, model: null }));
                    }}
                    placeholder="e.g. Innova"
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.model ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.model && <p className="text-xs text-rose-500 mt-1">{allErrors.model}</p>}
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Manufacturing Year <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="number"
                    required
                    value={data.year}
                    onChange={(e) => {
                      setData('year', e.target.value);
                      if (clientErrors.year) setClientErrors((p) => ({ ...p, year: null }));
                    }}
                    min="1990"
                    max={new Date().getFullYear() + 1}
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.year ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.year && <p className="text-xs text-rose-500 mt-1">{allErrors.year}</p>}
                </div>
              </div>

              {/* Row 2: Transmission, Fuel, Color, Seats */}
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Transmission <span className="text-rose-500">*</span>
                  </label>
                  <select
                    required
                    value={data.transmission}
                    onChange={(e) => {
                      setData('transmission', e.target.value);
                      if (clientErrors.transmission) setClientErrors((p) => ({ ...p, transmission: null }));
                    }}
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.transmission ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  >
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                  </select>
                  {allErrors.transmission && <p className="text-xs text-rose-500 mt-1">{allErrors.transmission}</p>}
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Fuel Type <span className="text-rose-500">*</span>
                  </label>
                  <select
                    required
                    value={data.fuel_type}
                    onChange={(e) => {
                      setData('fuel_type', e.target.value);
                      if (clientErrors.fuel_type) setClientErrors((p) => ({ ...p, fuel_type: null }));
                    }}
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.fuel_type ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  >
                    <option value="Gasoline">Gasoline</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Electric">Electric</option>
                  </select>
                  {allErrors.fuel_type && <p className="text-xs text-rose-500 mt-1">{allErrors.fuel_type}</p>}
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Exterior Color <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={data.color}
                    onChange={(e) => {
                      setData('color', e.target.value);
                      if (clientErrors.color) setClientErrors((p) => ({ ...p, color: null }));
                    }}
                    placeholder="e.g. Metallic Black"
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.color ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.color && <p className="text-xs text-rose-500 mt-1">{allErrors.color}</p>}
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Seating Capacity <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="number"
                    required
                    value={data.seats}
                    onChange={(e) => {
                      setData('seats', e.target.value);
                      if (clientErrors.seats) setClientErrors((p) => ({ ...p, seats: null }));
                    }}
                    min="1"
                    max="50"
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.seats ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.seats && <p className="text-xs text-rose-500 mt-1">{allErrors.seats}</p>}
                </div>
              </div>
            </div>

            {/* Section 3: Rental Pricing & Description */}
            <div className="space-y-4">
              <div className="flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-800">
                <DollarSign className="w-4 h-4 text-amber-500" />
                <h2 className="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                  Rental Pricing & Overview
                </h2>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div className="md:col-span-4">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Base Daily Rate (₱) <span className="text-rose-500">*</span>
                  </label>
                  <div className="relative">
                    <span className="absolute left-3.5 top-1/2 -translate-y-1/2 font-extrabold text-xs text-gray-400">
                      ₱
                    </span>
                    <input
                      type="number"
                      required
                      step="0.01"
                      value={data.daily_rate}
                      onChange={(e) => {
                        setData('daily_rate', e.target.value);
                        if (clientErrors.daily_rate) setClientErrors((p) => ({ ...p, daily_rate: null }));
                      }}
                      placeholder="2500.00"
                      className={`w-full pl-8 pr-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs font-black text-amber-600 dark:text-amber-400 focus:outline-none focus:border-emerald-500 ${
                        allErrors.daily_rate ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                      }`}
                    />
                  </div>
                  {allErrors.daily_rate && <p className="text-xs text-rose-500 mt-1">{allErrors.daily_rate}</p>}
                  <p className="text-[10px] text-gray-400 mt-1">Default 24-hour self-drive rate.</p>
                </div>

                <div className="md:col-span-8">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Vehicle Description & Features <span className="text-rose-500">*</span>
                  </label>
                  <textarea
                    rows="3"
                    required
                    value={data.description}
                    onChange={(e) => {
                      setData('description', e.target.value);
                      if (clientErrors.description) setClientErrors((p) => ({ ...p, description: null }));
                    }}
                    placeholder="e.g. Bluetooth audio, Dashcam installed, Apple CarPlay / Android Auto, Leather seats, pristine condition."
                    className={`w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500 ${
                      allErrors.description ? 'border-rose-500 ring-1 ring-rose-500' : 'border-gray-200 dark:border-gray-800'
                    }`}
                  />
                  {allErrors.description && <p className="text-xs text-rose-500 mt-1">{allErrors.description}</p>}
                </div>
              </div>
            </div>

            {/* Section 4: Photo Gallery */}
            <div className="space-y-4">
              <div className="flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-800">
                <ImageIcon className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                <h2 className="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                  Vehicle Photos & Gallery <span className="text-rose-500">*</span>
                </h2>
              </div>

              <div className={`relative border-2 border-dashed ${
                allErrors.images ? 'border-rose-500 bg-rose-50/20 dark:bg-rose-950/20' : 'border-gray-200 dark:border-gray-800 hover:border-emerald-500 dark:hover:border-emerald-500 bg-gray-50/50 dark:bg-gray-950/50'
              } rounded-3xl p-6 sm:p-8 text-center transition-colors group`}>
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  onChange={handleImageChange}
                  className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                />
                <div className={`w-12 h-12 rounded-2xl ${
                  allErrors.images ? 'bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400'
                } flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform`}>
                  <Upload className="w-6 h-6" />
                </div>
                <p className="text-xs font-bold text-gray-800 dark:text-gray-200">
                  Click or drag photos here to upload <span className="text-rose-500">*</span>
                </p>
                <p className="text-[10px] text-gray-400 mt-1">
                  Supports JPEG, PNG, WEBP up to 5MB per image. First photo is used as primary cover.
                </p>
              </div>

              {previewUrls.length > 0 && (
                <div className="space-y-2 pt-2">
                  <p className="text-xs font-bold text-gray-700 dark:text-gray-300">
                    Selected Photos ({previewUrls.length})
                  </p>
                  <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    {previewUrls.map((url, index) => (
                      <div
                        key={index}
                        className="relative aspect-video rounded-2xl bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden group shadow-xs"
                      >
                        <img src={url} alt={`Preview ${index + 1}`} className="w-full h-full object-cover" />
                        {index === 0 ? (
                          <span className="absolute top-1.5 left-1.5 bg-emerald-600/90 backdrop-blur-xs text-[9px] font-black text-white px-2 py-0.5 rounded-md shadow-xs">
                            Primary Cover
                          </span>
                        ) : (
                          <span className="absolute top-1.5 left-1.5 bg-black/60 backdrop-blur-xs text-[9px] font-bold text-white px-1.5 py-0.5 rounded-md">
                            #{index + 1}
                          </span>
                        )}
                        <button
                          type="button"
                          onClick={() => removeImage(index)}
                          className="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center opacity-80 hover:opacity-100 shadow-sm transition-opacity"
                          title="Remove photo"
                        >
                          <X className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}
              {allErrors.images && <p className="text-xs text-rose-500 font-semibold mt-1 flex items-center gap-1"><AlertCircle className="w-3.5 h-3.5" /> {allErrors.images}</p>}
            </div>

            {/* Bottom Form Actions */}
            <div className="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
              <Link
                href="/vehicles"
                className="w-full sm:w-auto text-center px-6 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95 transition-all"
              >
                Cancel
              </Link>
              <button
                type="submit"
                disabled={processing}
                className="w-full sm:w-auto px-8 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md shadow-emerald-600/20 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
              >
                {processing ? (
                  <span>Registering Vehicle...</span>
                ) : (
                  <>
                    <Plus className="w-4 h-4" />
                    <span>Register Vehicle</span>
                  </>
                )}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}

