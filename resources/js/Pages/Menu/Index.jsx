import React from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  LayoutDashboard,
  Car,
  Grid,
  Calendar,
  CalendarDays,
  MapPin,
  Users,
  User,
  LogOut,
  Zap,
  DollarSign
} from 'lucide-react';

import confirmDialog from '@/Utils/confirm';

export default function Menu({ totalVehicles, availableVehicles, activeBookings, pendingApprovalsCount }) {
  const { auth } = usePage().props;
  const user = auth?.user;

  const handleLogout = (e) => {
    e.preventDefault();
    confirmDialog({
      title: 'Confirm Log Out',
      content: 'Are you sure you want to log out of your LunarC account?',
      type: 'red',
      confirmButtonText: 'Log Out',
      confirmButtonClass: 'btn-red',
      onConfirm: () => {
        router.post('/logout');
      },
    });
  };

  const appModules = [
    {
      title: 'Dashboard',
      subtitle: 'Analytics & Overview',
      href: '/dashboard',
      icon: LayoutDashboard,
      gradient: 'from-cyan-500 to-blue-600',
      badge: null,
    },
    {
      title: 'My Fleet',
      subtitle: 'Manage My Vehicles',
      href: '/vehicles',
      icon: Car,
      gradient: 'from-blue-600 to-indigo-600',
      badge: null,
    },
    {
      title: 'All Listing',
      subtitle: 'Browse Rental Cars',
      href: '/vehicles/all-listing',
      icon: Grid,
      gradient: 'from-emerald-500 to-teal-600',
      badge: null,
    },
    {
      title: 'My Bookings',
      subtitle: 'Reservations & Trips',
      href: '/bookings',
      icon: Calendar,
      gradient: 'from-purple-600 to-indigo-600',
      badge: activeBookings > 0 ? activeBookings : null,
      badgeColor: 'bg-emerald-600',
    },
    {
      title: 'My Calendar',
      subtitle: 'Availability Schedule',
      href: '/calendar',
      icon: CalendarDays,
      gradient: 'from-amber-500 to-orange-600',
      badge: null,
    },
    {
      title: 'My Income',
      subtitle: 'Earnings & Revenue',
      href: '/income',
      icon: DollarSign,
      gradient: 'from-emerald-500 to-teal-600',
      badge: null,
    },
    ...(user?.is_admin ? [
      {
        title: 'Rates',
        subtitle: 'Destinations Matrix',
        href: '/destinations',
        icon: MapPin,
        gradient: 'from-red-500 to-rose-600',
        badge: null,
      },
      {
        title: 'Users',
        subtitle: 'User Approvals',
        href: '/admin/users',
        icon: Users,
        gradient: 'from-pink-500 to-rose-600',
        badge: pendingApprovalsCount > 0 ? pendingApprovalsCount : null,
        badgeColor: 'bg-rose-500 animate-pulse',
      },
    ] : []),
    {
      title: 'My Profile',
      subtitle: 'Account Settings',
      href: '/profile',
      icon: User,
      gradient: 'from-indigo-500 to-violet-600',
      badge: null,
    },
  ];

  return (
    <AppLayout title="App Hub">
      <Head title="App Hub" />

      <div className="max-w-4xl mx-auto space-y-6 pb-6">
        {/* Welcome Card */}
        <div className="relative overflow-hidden rounded-3xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-5 sm:p-6 shadow-sm transition-colors duration-300">
          <div className="flex items-center justify-between gap-4">
            <div className="flex items-center gap-3.5">
              <div className="w-12 h-12 rounded-2xl p-0.5 overflow-hidden ring-2 ring-emerald-500/40 bg-emerald-50 dark:bg-gray-800 flex items-center justify-center shadow-md shrink-0">
                <img
                  src={
                    user?.avatar_url ||
                    (user?.avatar_path
                      ? user.avatar_path.startsWith('http')
                        ? user.avatar_path
                        : `/${user.avatar_path}`
                      : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&background=059669&color=ffffff&bold=true`)
                  }
                  alt={user?.name || 'Avatar'}
                  className="w-full h-full object-cover rounded-[14px]"
                />
              </div>

              <div>
                <div className="flex items-center gap-2">
                  <h2 className="font-extrabold text-lg sm:text-xl text-gray-900 dark:text-white tracking-tight">
                    Hello, {user?.name}
                  </h2>
                  <span className="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    {user?.is_admin ? '🚗 Car Owner / Admin' : '👤 Partner Host'}
                  </span>
                </div>
                <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Select an application menu module to continue</p>
              </div>
            </div>

            <Link
              href="/bookings/create"
              className="hidden sm:flex px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold items-center gap-2 shadow-md shadow-emerald-600/20 active:scale-95 transition-all"
            >
              <Zap className="w-4 h-4 fill-white" />
              New Rental
            </Link>
          </div>
        </div>

        {/* Quick Fleet Stats Band */}
        <div className="grid grid-cols-3 gap-3 p-4 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 shadow-sm text-center transition-colors duration-300">
          <div className="space-y-1">
            <span className="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Available</span>
            <p className="text-2xl font-black text-emerald-600 dark:text-emerald-400">{availableVehicles}</p>
          </div>
          <div className="space-y-1 border-x border-gray-100 dark:border-gray-800">
            <span className="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Total Fleet</span>
            <p className="text-2xl font-black text-indigo-600 dark:text-indigo-400">{totalVehicles}</p>
          </div>
          <div className="space-y-1">
            <span className="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Active Trips</span>
            <p className="text-2xl font-black text-amber-600 dark:text-amber-400">{activeBookings}</p>
          </div>
        </div>

        {/* App Launcher Grid */}
        <div>
          <h3 className="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-1 mb-3">App Modules</h3>

          <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
            {appModules.map((module) => {
              const Icon = module.icon;
              return (
                <Link
                  key={module.href}
                  href={module.href}
                  className="group bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/80 rounded-3xl p-4 sm:p-6 border border-gray-200/80 dark:border-gray-800 shadow-xs hover:shadow-md transition-all duration-200 active:scale-[0.97] flex flex-col items-center text-center justify-center min-h-[120px] sm:min-h-[150px]"
                >
                  <div className="relative mb-3">
                    <div className={`w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr ${module.gradient} flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-200`}>
                      <Icon className="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                    </div>
                    {module.badge && (
                      <span className={`absolute -top-1.5 -right-1.5 px-2 py-0.5 text-[10px] font-black text-white rounded-full shadow-sm ${module.badgeColor || 'bg-emerald-600'}`}>
                        {module.badge}
                      </span>
                    )}
                  </div>

                  <span className="font-bold text-sm text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    {module.title}
                  </span>
                  <span className="text-[10px] text-gray-400 dark:text-gray-400 mt-0.5 truncate max-w-full">
                    {module.subtitle}
                  </span>
                </Link>
              );
            })}

            {/* Logout Action Card */}
            <button
              onClick={handleLogout}
              className="group bg-white dark:bg-gray-900 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-3xl p-4 sm:p-6 border border-gray-200/80 dark:border-gray-800 hover:border-rose-200 dark:hover:border-rose-900/50 shadow-xs hover:shadow-md transition-all duration-200 active:scale-[0.97] flex flex-col items-center text-center justify-center min-h-[120px] sm:min-h-[150px]"
            >
              <div className="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-gray-700 to-rose-900 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-200 mb-3">
                <LogOut className="w-6 h-6 text-white" />
              </div>
              <span className="font-bold text-sm text-gray-800 dark:text-gray-100 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">
                Log Out
              </span>
              <span className="text-[10px] text-gray-400 dark:text-gray-400 mt-0.5">
                Sign out of account
              </span>
            </button>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
