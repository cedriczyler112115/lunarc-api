import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { AlertTriangle, UserPlus, Upload, User, Check } from 'lucide-react';

export default function Register() {
  const { data, setData, post, processing, errors, reset } = useForm({
    role: 'guest',
    first_name: '',
    middle_name: '',
    last_name: '',
    extension_name: '',
    birthday: '',
    address: '',
    contact_number: '',
    email: '',
    password: '',
    password_confirmation: '',
    owner_description: '',
    avatar: null,
  });

  const [avatarPreview, setAvatarPreview] = useState(null);

  const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setData('avatar', file);
      setAvatarPreview(URL.createObjectURL(file));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/register', {
      forceFormData: true,
      onFinish: () => reset('password', 'password_confirmation'),
    });
  };

  return (
    <GuestLayout>
      <Head title="Register Account" />

      <div className="mb-6 text-center">
        <h2 className="text-2xl font-black text-gray-900 dark:text-white">Register Account</h2>
        <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Create your user account for ONEDRIVE WHEELS Car Rental System
        </p>
      </div>

      {/* Admin Approval Notice Banner */}
      <div className="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-900 dark:bg-amber-950/40 dark:text-amber-200 rounded-r-xl text-xs space-y-1 shadow-xs">
        <div className="font-extrabold uppercase tracking-wider flex items-center gap-1.5 text-amber-700 dark:text-amber-400">
          <AlertTriangle className="w-4 h-4 shrink-0" />
          <span>Admin Approval Required</span>
        </div>
        <p className="leading-relaxed">
          All new user registrations must be reviewed and approved by an Administrator before login credentials become active.
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-5">
        {/* Account Role Selection */}
        <div>
          <label className="block text-xs font-bold text-gray-900 dark:text-white mb-2 uppercase tracking-wider">
            Select Account Type
          </label>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div
              onClick={() => setData('role', 'guest')}
              className={`p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between ${
                data.role === 'guest'
                  ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30 ring-2 ring-emerald-500/30'
                  : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'
              }`}
            >
              <div className="flex items-center justify-between">
                <span className="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-1.5">
                  👤 Guest / Renter
                </span>
                <input
                  type="radio"
                  name="role"
                  value="guest"
                  checked={data.role === 'guest'}
                  onChange={() => setData('role', 'guest')}
                  className="text-emerald-600 focus:ring-emerald-500"
                />
              </div>
              <p className="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-snug">
                Can rent available vehicles and book trips
              </p>
            </div>

            <div
              onClick={() => setData('role', 'car_owner')}
              className={`p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between ${
                data.role === 'car_owner'
                  ? 'border-indigo-500 bg-indigo-50/60 dark:bg-indigo-950/30 ring-2 ring-indigo-500/30'
                  : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'
              }`}
            >
              <div className="flex items-center justify-between">
                <span className="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-1.5">
                  🚗 Car Rental Owner
                </span>
                <input
                  type="radio"
                  name="role"
                  value="car_owner"
                  checked={data.role === 'car_owner'}
                  onChange={() => setData('role', 'car_owner')}
                  className="text-indigo-600 focus:ring-indigo-500"
                />
              </div>
              <p className="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-snug">
                Owns vehicles and manages fleet rentals
              </p>
            </div>
          </div>
          {errors.role && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.role}</p>}
        </div>

        {/* Profile Picture Upload */}
        <div className="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-200 dark:border-gray-700 flex items-center gap-4 transition-colors">
          <div className="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0 border-2 border-white dark:border-gray-600 shadow-md flex items-center justify-center">
            {avatarPreview ? (
              <img src={avatarPreview} alt="Preview" className="w-full h-full object-cover" />
            ) : (
              <User className="w-8 h-8 text-gray-400 dark:text-gray-500" />
            )}
          </div>
          <div className="flex-1">
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Profile Picture (Optional)
            </label>
            <input
              type="file"
              accept="image/*"
              onChange={handleAvatarChange}
              className="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950 dark:file:text-indigo-300 transition-colors"
            />
            {errors.avatar && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.avatar}</p>}
          </div>
        </div>

        {/* Personal Info Name Fields */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">First Name *</label>
            <input
              type="text"
              value={data.first_name}
              onChange={(e) => setData('first_name', e.target.value)}
              required
              placeholder="e.g. Maria"
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.first_name && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.first_name}</p>}
          </div>

          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Last Name *</label>
            <input
              type="text"
              value={data.last_name}
              onChange={(e) => setData('last_name', e.target.value)}
              required
              placeholder="e.g. Santos"
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.last_name && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.last_name}</p>}
          </div>

          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Middle Name (Optional)</label>
            <input
              type="text"
              value={data.middle_name}
              onChange={(e) => setData('middle_name', e.target.value)}
              placeholder="e.g. Clara"
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.middle_name && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.middle_name}</p>}
          </div>

          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Extension Name (Optional)</label>
            <input
              type="text"
              value={data.extension_name}
              onChange={(e) => setData('extension_name', e.target.value)}
              placeholder="e.g. Jr., Sr., III"
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.extension_name && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.extension_name}</p>}
          </div>
        </div>

        {/* Contact & Birthday Fields */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Date of Birth *</label>
            <input
              type="date"
              value={data.birthday}
              onChange={(e) => setData('birthday', e.target.value)}
              required
              max={new Date().toISOString().split('T')[0]}
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.birthday && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.birthday}</p>}
          </div>

          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Contact Mobile Number *</label>
            <input
              type="text"
              value={data.contact_number}
              onChange={(e) => setData('contact_number', e.target.value)}
              required
              placeholder="e.g. 09171234567"
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.contact_number && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.contact_number}</p>}
          </div>
        </div>

        {/* Home Address */}
        <div>
          <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Complete Address *</label>
          <textarea
            value={data.address}
            onChange={(e) => setData('address', e.target.value)}
            rows={2}
            required
            placeholder="Street, Barangay, City, Province"
            className="w-full text-sm p-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
          />
          {errors.address && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.address}</p>}
        </div>

        {/* About Car Rental Owner */}
        {data.role === 'car_owner' && (
          <div className="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 space-y-2 transition-colors">
            <div className="flex items-center justify-between">
              <label className="block text-xs font-bold text-indigo-900 dark:text-indigo-300">
                About Car Rental Owner / Business Bio
              </label>
              <span className="text-[10px] font-black text-rose-500 uppercase tracking-wider">* Required for Car Owners</span>
            </div>
            <textarea
              value={data.owner_description}
              onChange={(e) => setData('owner_description', e.target.value)}
              rows={3}
              required={data.role === 'car_owner'}
              placeholder="Provide a brief description about your car rental business, fleet experience, or owner background..."
              className="w-full text-sm p-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.owner_description && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.owner_description}</p>}
          </div>
        )}

        {/* Account Credentials */}
        <div className="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-4">
          <h3 className="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Account Credentials</h3>

          {/* Email Address */}
          <div>
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Email Address *</label>
            <input
              type="email"
              value={data.email}
              onChange={(e) => setData('email', e.target.value)}
              required
              autoComplete="username"
              placeholder="e.g. maria@example.com"
              className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
            />
            {errors.email && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.email}</p>}
          </div>

          {/* Password & Confirmation */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Password *</label>
              <input
                type="password"
                value={data.password}
                onChange={(e) => setData('password', e.target.value)}
                required
                autoComplete="new-password"
                placeholder="••••••••"
                className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
              />
              {errors.password && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.password}</p>}
            </div>

            <div>
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Confirm Password *</label>
              <input
                type="password"
                value={data.password_confirmation}
                onChange={(e) => setData('password_confirmation', e.target.value)}
                required
                autoComplete="new-password"
                placeholder="••••••••"
                className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
              />
            </div>
          </div>
        </div>

        {/* Bottom Actions */}
        <div className="flex items-center justify-between pt-4">
          <Link
            href="/login"
            className="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
          >
            Already registered? Log in here
          </Link>

          <button
            type="submit"
            disabled={processing}
            className="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl shadow-md text-xs font-extrabold uppercase tracking-wider transition duration-150"
          >
            {processing ? 'Submitting...' : 'Submit Registration'}
          </button>
        </div>
      </form>
    </GuestLayout>
  );
}

