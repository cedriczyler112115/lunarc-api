import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { User, Key, Trash2, CheckCircle2, Upload, Shield } from 'lucide-react';

export default function Edit({ user, status }) {
  const profileForm = useForm({
    first_name: user.first_name || '',
    middle_name: user.middle_name || '',
    last_name: user.last_name || '',
    extension_name: user.extension_name || '',
    email: user.email || '',
    contact_number: user.contact_number || '',
    address: user.address || '',
    avatar: null,
  });

  const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  });

  const [avatarPreview, setAvatarPreview] = useState(
    user.avatar_path ? `/${user.avatar_path}` : null
  );

  const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      profileForm.setData('avatar', file);
      setAvatarPreview(URL.createObjectURL(file));
    }
  };

  const handleProfileSubmit = (e) => {
    e.preventDefault();
    profileForm.post('/profile', {
      forceFormData: true,
      preserveScroll: true,
    });
  };

  const handlePasswordSubmit = (e) => {
    e.preventDefault();
    passwordForm.put('/password', {
      preserveScroll: true,
      onSuccess: () => passwordForm.reset(),
    });
  };

  return (
    <AppLayout title="My Profile Settings">
      <Head title="My Profile" />

      <div className="max-w-3xl mx-auto space-y-6 pb-6">
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-6 sm:p-8 rounded-3xl shadow-xs space-y-6 transition-colors duration-300">
          <div className="flex items-center gap-4">
            <div className="relative w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-950 border-2 border-emerald-500 overflow-hidden shrink-0">
              {avatarPreview ? (
                <img src={avatarPreview} alt={user.name} className="w-full h-full object-cover" />
              ) : (
                <div className="w-full h-full flex items-center justify-center font-black text-xl text-emerald-700 dark:text-emerald-300">
                  {user.first_name?.[0] || 'U'}
                </div>
              )}
            </div>

            <div>
              <h1 className="text-xl font-black text-gray-900 dark:text-white">{user.name}</h1>
              <p className="text-xs text-gray-500 dark:text-gray-400">{user.email}</p>
              <span className="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                {user.is_admin ? 'Administrator' : 'Partner Host'}
              </span>
            </div>
          </div>

          {status === 'profile-updated' && (
            <div className="p-3 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-2xl flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Profile updated successfully!
            </div>
          )}

          {/* Profile Form */}
          <form onSubmit={handleProfileSubmit} className="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <h2 className="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
              <User className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Personal Details
            </h2>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">First Name *</label>
                <input
                  type="text"
                  value={profileForm.data.first_name}
                  onChange={(e) => profileForm.setData('first_name', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Last Name *</label>
                <input
                  type="text"
                  value={profileForm.data.last_name}
                  onChange={(e) => profileForm.setData('last_name', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Email Address *</label>
                <input
                  type="email"
                  value={profileForm.data.email}
                  onChange={(e) => profileForm.setData('email', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Contact Phone Number</label>
                <input
                  type="text"
                  value={profileForm.data.contact_number}
                  onChange={(e) => profileForm.setData('contact_number', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Avatar Photo</label>
              <input
                type="file"
                accept="image/*"
                onChange={handleAvatarChange}
                className="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-2xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-200"
              />
            </div>

            <div className="flex justify-end pt-2">
              <button
                type="submit"
                disabled={profileForm.processing}
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-95 transition-transform"
              >
                Save Profile
              </button>
            </div>
          </form>
        </div>

        {/* Update Password Form */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-6 sm:p-8 rounded-3xl shadow-xs space-y-4 transition-colors duration-300">
          <h2 className="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <Key className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Security & Password
          </h2>

          <form onSubmit={handlePasswordSubmit} className="space-y-4">
            <div>
              <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
              <input
                type="password"
                value={passwordForm.data.current_password}
                onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
              />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                <input
                  type="password"
                  value={passwordForm.data.password}
                  onChange={(e) => passwordForm.setData('password', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                <input
                  type="password"
                  value={passwordForm.data.password_confirmation}
                  onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-emerald-500"
                />
              </div>
            </div>

            <div className="flex justify-end">
              <button
                type="submit"
                disabled={passwordForm.processing}
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-95 transition-transform"
              >
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
