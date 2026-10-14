import React from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar,
  Car,
  MapPin,
  Clock,
  ArrowLeft,
  Printer,
  CheckCircle2,
  Phone,
  User,
  UserCheck,
  ShieldCheck,
  Edit,
  DollarSign,
  AlertTriangle
} from 'lucide-react';
import confirmDialog from '@/Utils/confirm';

export default function Show({ booking, users = [] }) {
  const { auth } = usePage().props;
  const isOwner = Boolean(
    auth?.user?.id && (
      String(booking.user_id) === String(auth.user.id) ||
      String(booking.vehicle?.user_id) === String(auth.user.id) ||
      Boolean(auth.user.is_admin)
    )
  );
  const vehicle = booking.vehicle;
  const vehicleOwner = vehicle?.user || vehicle?.owner;
  const getParam = (key, fallback = '') => {
    if (typeof window !== 'undefined') {
      const v = new URLSearchParams(window.location.search).get(key);
      if (v !== null && v !== undefined && v !== '') return v;
    }
    return fallback;
  };

  const isConfirmed = booking.status === 'confirmed';
  const ownerUserId = vehicleOwner?.id || vehicle?.user_id;

  const handleOpenTransfer = () => {
    if (ownerUserId) {
      setSelectedUserId(String(ownerUserId));
      if (booking.vehicle_id) {
        setSelectedVehicleId(String(booking.vehicle_id));
      }
    }
    setTransferModalOpen(true);
  };

  const [completeModalOpen, setCompleteModalOpen] = React.useState(false);
  const [transferModalOpen, setTransferModalOpen] = React.useState(
    () => isConfirmed && getParam('transfer') === '1'
  );
  const [selectedUserId, setSelectedUserId] = React.useState(
    () => getParam('transfer_user_id', ownerUserId ? String(ownerUserId) : (booking.user_id ? String(booking.user_id) : (users[0]?.id ? String(users[0].id) : '')))
  );
  const [selectedVehicleId, setSelectedVehicleId] = React.useState(
    () => getParam('transfer_vehicle_id', booking.vehicle_id ? String(booking.vehicle_id) : '')
  );
  const [isTransferring, setIsTransferring] = React.useState(false);
  const [actualIncome, setActualIncome] = React.useState(
    booking.actual_income !== null && booking.actual_income !== undefined
      ? String(booking.actual_income)
      : String(Math.round(booking.total_price || 0))
  );
  const [isSubmitting, setIsSubmitting] = React.useState(false);

  const selectedUserObj = React.useMemo(() => {
    return users.find((u) => String(u.id) === String(selectedUserId)) || null;
  }, [users, selectedUserId]);

  const userVehicles = React.useMemo(() => {
    return selectedUserObj?.vehicles || [];
  }, [selectedUserObj]);

  const selectedVehicleObj = React.useMemo(() => {
    return userVehicles.find((v) => String(v.id) === String(selectedVehicleId)) || null;
  }, [userVehicles, selectedVehicleId]);

  React.useEffect(() => {
    if (getParam('transfer_user_id')) {
      setSelectedUserId(getParam('transfer_user_id'));
    } else if (ownerUserId) {
      setSelectedUserId(String(ownerUserId));
    } else if (booking.user_id) {
      setSelectedUserId(String(booking.user_id));
    } else if (users.length > 0 && !selectedUserId) {
      setSelectedUserId(String(users[0].id));
    }
  }, [ownerUserId, booking.user_id, users]);

  // Synchronize transfer modal and selection into URL
  React.useEffect(() => {
    if (typeof window === 'undefined') return;
    const params = new URLSearchParams(window.location.search);
    if (transferModalOpen && isConfirmed) params.set('transfer', '1'); else params.delete('transfer');
    if (transferModalOpen && isConfirmed && selectedUserId) params.set('transfer_user_id', selectedUserId); else params.delete('transfer_user_id');
    if (transferModalOpen && isConfirmed && selectedVehicleId) params.set('transfer_vehicle_id', selectedVehicleId); else params.delete('transfer_vehicle_id');
    const newQuery = params.toString() ? `?${params.toString()}` : '';
    const newUrl = `${window.location.pathname}${newQuery}`;
    if (window.location.search !== newQuery) {
      window.history.replaceState(null, '', newUrl);
    }
  }, [transferModalOpen, isConfirmed, selectedUserId, selectedVehicleId]);

  // When selected user changes, automatically select the first available vehicle
  React.useEffect(() => {
    if (!selectedUserObj) {
      setSelectedVehicleId('');
      return;
    }

    const vList = selectedUserObj.vehicles || [];
    if (vList.length === 0) {
      setSelectedVehicleId('');
      return;
    }

    const matchCurrent = vList.find((v) => String(v.id) === String(selectedVehicleId));
    if (!matchCurrent) {
      const firstAvailable = vList.find((v) => v.is_available);
      setSelectedVehicleId(firstAvailable ? String(firstAvailable.id) : String(vList[0].id));
    }
  }, [selectedUserId, selectedUserObj]);

  const handleTransferSubmit = (e) => {
    if (e) e.preventDefault();
    if (!selectedUserId || !selectedVehicleId) return;
    if (selectedVehicleObj && !selectedVehicleObj.is_available) return;

    setIsTransferring(true);
    router.patch(
      `/bookings/${booking.id}/transfer`,
      {
        user_id: selectedUserId,
        vehicle_id: selectedVehicleId,
      },
      {
        preserveScroll: true,
        onSuccess: () => {
          setTransferModalOpen(false);
        },
        onFinish: () => {
          setIsTransferring(false);
        },
      }
    );
  };

  const handlePrint = () => {
    window.print();
  };

  const handleStatusChange = (newStatus) => {
    if (newStatus === 'completed') {
      setActualIncome(
        booking.actual_income !== null && booking.actual_income !== undefined
          ? String(booking.actual_income)
          : String(Math.round(booking.total_price || 0))
      );
      setCompleteModalOpen(true);
      return;
    }

    const isDestructive = newStatus === 'cancelled';
    confirmDialog({
      title: 'Update Booking Status',
      content: `Are you sure you want to change this booking status to <strong class="uppercase">${newStatus}</strong>?`,
      type: isDestructive ? 'red' : 'blue',
      confirmButtonText: `Update to ${newStatus}`,
      confirmButtonClass: isDestructive ? 'btn-red' : 'btn-blue',
      onConfirm: () => {
        router.patch(`/bookings/${booking.id}`, { status: newStatus });
      },
    });
  };

  const handleCompleteSubmit = (e) => {
    if (e) e.preventDefault();
    const incomeValue = actualIncome !== '' ? parseInt(actualIncome, 10) : 0;
    setIsSubmitting(true);
    router.patch(
      `/bookings/${booking.id}`,
      {
        status: 'completed',
        actual_income: isNaN(incomeValue) ? 0 : incomeValue,
      },
      {
        onFinish: () => {
          setIsSubmitting(false);
          setCompleteModalOpen(false);
        },
      }
    );
  };

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const cleanStr = String(dateStr).split('T')[0];
    const [y, m, d] = cleanStr.split('-').map(Number);
    if (!y || !m || !d) return dateStr;
    return new Date(y, m - 1, d).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    });
  };

  const formatTime = (timeStr) => {
    if (!timeStr) return '';
    if (/am|pm/i.test(timeStr)) return timeStr;
    const parts = String(timeStr).split(':');
    if (parts.length < 2) return timeStr;
    let hour = parseInt(parts[0], 10);
    const minute = parts[1].padStart(2, '0');
    if (isNaN(hour)) return timeStr;
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const formattedHour = hour % 12 === 0 ? 12 : hour % 12;
    return `${formattedHour}:${minute} ${ampm}`;
  };

  const handleBack = () => {
    if (typeof window === 'undefined') {
      router.visit('/bookings');
      return;
    }

    const fromParam = getParam('from', '').toLowerCase();
    const referrer = document.referrer ? document.referrer.toLowerCase() : '';
    const storedLastPage = window.sessionStorage?.getItem('last_non_booking_page') || '';
    const storedLastLower = storedLastPage.toLowerCase();

    // 1. Check if previous page or URL was from /calendar
    if (
      fromParam === 'calendar' ||
      fromParam.includes('calendar') ||
      referrer.includes('/calendar') ||
      storedLastLower.includes('/calendar')
    ) {
      if (storedLastPage && storedLastLower.includes('/calendar')) {
        router.visit(storedLastPage);
      } else {
        router.visit('/calendar');
      }
      return;
    }

    // 2. Check if previous page or URL was from /income
    if (
      fromParam === 'income' ||
      fromParam.includes('income') ||
      referrer.includes('/income') ||
      storedLastLower.includes('/income')
    ) {
      if (storedLastPage && storedLastLower.includes('/income')) {
        router.visit(storedLastPage);
      } else {
        router.visit('/income');
      }
      return;
    }

    // Fallback: standard history navigation or /bookings
    if (window.history.length > 1) {
      window.history.go(-1);
    } else {
      router.visit('/bookings');
    }
  };

  return (
    <AppLayout title={`Booking #${booking.booking_code}`}>
      <Head title={`Booking #${booking.booking_code}`} />

      <div className="max-w-2xl mx-auto space-y-6 pb-6">
        <div className="flex items-center justify-between print:hidden">
          <button
            type="button"
            onClick={handleBack}
            className="inline-flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors cursor-pointer"
          >
            <ArrowLeft className="w-4 h-4" />
            Back
          </button>

          <div className="flex items-center gap-2">
            {isConfirmed && booking.status !== 'completed' && (
              <button
                type="button"
                onClick={handleOpenTransfer}
                className="px-3.5 py-2 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-xs font-bold flex items-center gap-1.5 border border-indigo-200 dark:border-indigo-800/80 shadow-2xs transition-all cursor-pointer"
              >
                <UserCheck className="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                Transfer Booking
              </button>
            )}

            <button
              onClick={handlePrint}
              className="px-4 py-2 rounded-2xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold flex items-center gap-2 border border-gray-200 dark:border-gray-700 shadow-sm"
            >
              <Printer className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Print Ticket / Voucher
            </button>
          </div>
        </div>

        {/* Digital Mobile Voucher Ticket */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xs relative transition-colors duration-300">
          {/* Ticket Header Gradient */}
          <div className="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 p-6 sm:p-8 text-white flex items-center justify-between">
            <div className="space-y-1">
              <span className="text-[10px] font-black uppercase tracking-widest text-emerald-100">Official Rental Voucher</span>
              <h1 className="text-2xl font-black text-white font-mono">#{booking.booking_code}</h1>
              <p className="text-xs text-emerald-100">OneDrive Wheels Fleet Operations</p>
            </div>

            <div className="text-right">
              <span
                className={`px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider ${booking.status === 'confirmed'
                    ? 'bg-white/20 text-white border border-white/30'
                    : 'bg-amber-500/30 text-white border border-amber-300/40'
                  }`}
              >
                {booking.status}
              </span>
            </div>
          </div>

          {/* Ticket Body */}
          <div className="p-6 sm:p-8 space-y-6">
            {/* Customer & Vehicle Band */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-950/60 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800">
              <div className="space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Customer Name</span>
                <p className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                  <User className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                  {booking.customer_name}
                </p>
                {isOwner && booking.customer_phone && (
                  <p className="text-xs text-gray-500 dark:text-gray-400">{booking.customer_phone}</p>
                )}
              </div>

              <div className="space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400">Assigned Vehicle</span>
                <p className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                  <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                  {vehicle?.name || 'Standard Vehicle'}
                </p>
                <div className="flex items-center gap-2 flex-wrap">
                  <span className="text-xs font-mono text-emerald-600 dark:text-emerald-400 font-bold">{vehicle?.license_plate}</span>
                  {vehicleOwner && (
                    <span className="text-[11px] text-gray-500 dark:text-gray-400 font-normal">
                      &bull; Owner: <span className="font-semibold text-gray-700 dark:text-gray-300">{vehicleOwner.name}</span>
                    </span>
                  )}
                </div>
              </div>
            </div>

            {/* Booked by & Owner of the Vehicle Band */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {/* Booked by */}
              <div className="flex items-center justify-between gap-3 bg-indigo-50/60 dark:bg-indigo-950/30 p-3.5 sm:p-4 rounded-2xl border border-indigo-100 dark:border-indigo-900/60">
                <div className="flex items-center gap-3 min-w-0">
                  <div className="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <UserCheck className="w-4 h-4" />
                  </div>
                  <div className="min-w-0">
                    <span className="text-[10px] font-black uppercase tracking-wider text-indigo-700 dark:text-indigo-400 block">
                      Booked by
                    </span>
                    <p className="text-xs font-extrabold text-gray-900 dark:text-white truncate">
                      {booking.user ? (
                        booking.user.name
                      ) : (
                        <span className="text-gray-400 italic font-normal">No user assigned</span>
                      )}
                    </p>
                    {booking.user?.email && (
                      <p className="text-[11px] text-gray-500 dark:text-gray-400 font-mono truncate">
                        {booking.user.email}
                      </p>
                    )}
                  </div>
                </div>
              </div>

              {/* Vehicle Owner */}
              <div className="flex items-center justify-between gap-3 bg-amber-50/60 dark:bg-amber-950/30 p-3.5 sm:p-4 rounded-2xl border border-amber-100 dark:border-amber-900/60">
                <div className="flex items-center gap-3 min-w-0">
                  <div className="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <ShieldCheck className="w-4 h-4" />
                  </div>
                  <div className="min-w-0">
                    <span className="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block">
                      Vehicle Owner
                    </span>
                    <p className="text-xs font-extrabold text-gray-900 dark:text-white truncate">
                      {vehicleOwner ? (
                        vehicleOwner.name
                      ) : (
                        <span className="text-gray-400 italic font-normal">Platform Fleet</span>
                      )}
                    </p>
                    {vehicleOwner?.email && (
                      <p className="text-[11px] text-gray-500 dark:text-gray-400 font-mono truncate">
                        {vehicleOwner.email}
                      </p>
                    )}
                  </div>
                </div>

                {isConfirmed && booking.status !== 'completed' && (
                  <button
                    type="button"
                    onClick={handleOpenTransfer}
                    className="self-center px-2.5 py-1.5 rounded-xl text-xs font-bold text-amber-800 dark:text-amber-200 bg-white dark:bg-gray-800 hover:bg-amber-50 dark:hover:bg-amber-900/40 border border-amber-200 dark:border-amber-700/80 shadow-2xs transition-all flex items-center gap-1.5 print:hidden cursor-pointer shrink-0"
                    title="Transfer Booking to Vehicle Owner"
                  >
                    <UserCheck className="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                    <span className="hidden sm:inline">Transfer</span>
                  </button>
                )}
              </div>
            </div>

            {/* Timings & Locations */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div className="p-4 bg-gray-50 dark:bg-gray-950/40 rounded-2xl border border-gray-200/80 dark:border-gray-800/80 space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400 block">Pickup Details</span>
                <p className="font-bold text-gray-800 dark:text-gray-200">
                  {formatDate(booking.start_date)} {booking.pickup_time ? `at ${formatTime(booking.pickup_time)}` : ''}
                </p>
                <p className="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                  <MapPin className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                  {booking.pickup_location || 'Main Terminal'}
                </p>
              </div>

              <div className="p-4 bg-gray-50 dark:bg-gray-950/40 rounded-2xl border border-gray-200/80 dark:border-gray-800/80 space-y-1">
                <span className="text-[10px] font-bold uppercase text-gray-400 block">Return Details</span>
                <p className="font-bold text-gray-800 dark:text-gray-200">
                  {formatDate(booking.end_date)} {booking.return_time ? `at ${formatTime(booking.return_time)}` : ''}
                </p>
                <p className="text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                  <MapPin className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                  {booking.destination || 'Dropoff Point'}
                </p>
              </div>
            </div>

            {/* Financial Summary */}
            <div className="p-6 bg-emerald-50/60 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 space-y-3">
              <h3 className="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex items-center gap-2">
                <DollarSign className="w-4 h-4 text-amber-500" />
                Payment Breakdown
              </h3>

              <div className="space-y-1.5 text-xs">
                <div className="flex justify-between text-gray-600 dark:text-gray-400">
                  <span>Destination Rate x {booking.total_days} Days:</span>
                  <span className="font-bold text-gray-800 dark:text-gray-200">₱{Number(booking.destination_rate * booking.total_days).toLocaleString()}</span>
                </div>

                {booking.reservation_fee > 0 && (
                  <div className="flex justify-between text-emerald-700 dark:text-emerald-400">
                    <span>Reservation Fee Paid:</span>
                    <span className="font-bold">-₱{Number(booking.reservation_fee).toLocaleString()}</span>
                  </div>
                )}

                <div className="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-800 text-sm font-extrabold text-gray-900 dark:text-white">
                  <span>Total Amount Paid / Due:</span>
                  <span className="text-amber-600 dark:text-amber-400 text-lg">₱{Number(booking.total_price).toLocaleString()}</span>
                </div>

                {booking.actual_income !== null && booking.actual_income !== undefined && (
                  <div className="flex justify-between pt-2 border-t border-emerald-300/60 dark:border-emerald-700/60 text-sm font-black text-emerald-700 dark:text-emerald-300 bg-emerald-100/50 dark:bg-emerald-900/30 p-2.5 rounded-xl">
                    <span>Actual Income:</span>
                    <span className="text-base font-black">₱{Number(booking.actual_income).toLocaleString()}</span>
                  </div>
                )}
              </div>
            </div>

            {/* License Photo (If Uploaded) */}
            {booking.driver_license_path && (
              <div className="space-y-2 pt-2">
                <span className="text-xs font-bold text-gray-700 dark:text-gray-300 block">Attached Driver's License</span>
                <div className="w-48 h-32 rounded-2xl bg-gray-100 dark:bg-gray-950 overflow-hidden border border-gray-200 dark:border-gray-800">
                  <img src={`/${booking.driver_license_path}`} alt="License" className="w-full h-full object-cover" />
                </div>
              </div>
            )}

            {/* Status Quick Updater Bar (For Hosts / Admins) */}
            {booking.status === 'confirmed' ? (
              <div className="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between print:hidden">
                <span className="text-xs font-bold text-gray-500 dark:text-gray-400">Update Status:</span>

                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => handleStatusChange('completed')}
                    className="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                  >
                    <CheckCircle2 className="w-3.5 h-3.5" />
                    Completed
                  </button>

                  <button
                    type="button"
                    onClick={() => handleStatusChange('cancelled')}
                    className="px-3.5 py-1.5 rounded-xl text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 dark:hover:bg-rose-900/50 shadow-xs transition-all cursor-pointer"
                  >
                    Cancelled
                  </button>
                </div>
              </div>
            ) : (
              <div className="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs print:hidden">
                <span className="font-bold text-gray-500 dark:text-gray-400">Booking Status:</span>
                <span className={`px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider ${booking.status === 'completed'
                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                    : 'bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200 dark:border-rose-800'
                  }`}>
                  {booking.status} (Final)
                </span>
              </div>
            )}
          </div>
        </div>

        {/* Complete Booking Popup Modal with Actual Income Field */}
        {completeModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-5 animate-in zoom-in-95 duration-150">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                  <CheckCircle2 className="w-6 h-6" />
                </div>
                <div>
                  <h3 className="text-base font-extrabold text-gray-900 dark:text-white">Complete Booking</h3>
                  <p className="text-xs text-gray-500 dark:text-gray-400">#{booking.booking_code} &bull; {booking.customer_name}</p>
                </div>
              </div>

              <p className="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                Confirm completion of this booking. Please specify the actual income generated from this rental trip.
              </p>

              <form onSubmit={handleCompleteSubmit} className="space-y-4">
                <div className="space-y-1.5">
                  <label htmlFor="actual_income_input" className="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    Actual Income (₱) <span className="text-rose-500">*</span>
                  </label>
                  <div className="relative">
                    <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">₱</span>
                    <input
                      id="actual_income_input"
                      type="number"
                      step="1"
                      min="0"
                      required
                      placeholder="0"
                      value={actualIncome}
                      onChange={(e) => {
                        const val = e.target.value;
                        if (val === '' || /^\d+$/.test(val)) {
                          setActualIncome(val);
                        }
                      }}
                      className="w-full pl-8 pr-4 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white font-extrabold text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-inner"
                    />
                  </div>
                  <p className="text-[11px] text-gray-500 dark:text-gray-400">
                    Total bill amount: <span className="font-bold text-gray-700 dark:text-gray-300">₱{Number(booking.total_price).toLocaleString()}</span>
                  </p>
                </div>

                <div className="flex items-center justify-end gap-2.5 pt-2">
                  <button
                    type="button"
                    onClick={() => setCompleteModalOpen(false)}
                    disabled={isSubmitting}
                    className="px-4 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-xs font-extrabold text-white shadow-md shadow-emerald-600/30 transition-all flex items-center gap-1.5"
                  >
                    <CheckCircle2 className="w-4 h-4" />
                    {isSubmitting ? 'Saving...' : 'Confirm Completed'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Transfer Booking Modal */}
        {transferModalOpen && isConfirmed && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-5 animate-in zoom-in-95 duration-150">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-indigo-100 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                  <UserCheck className="w-6 h-6" />
                </div>
                <div>
                  <h3 className="text-base font-extrabold text-gray-900 dark:text-white">Transfer Booking</h3>
                  <p className="text-xs text-gray-500 dark:text-gray-400">
                    #{booking.booking_code} &bull; {booking.customer_name}
                  </p>
                </div>
              </div>

              <div className="p-3 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-200/80 dark:border-gray-700/60 space-y-1 text-xs">
                <span className="text-[10px] font-bold uppercase text-gray-400 block">Currently Booked by</span>
                <p className="font-extrabold text-gray-900 dark:text-white">
                  {booking.user ? (
                    <>
                      {booking.user.name}{' '}
                      <span className="font-mono text-gray-500 dark:text-gray-400 font-normal">
                        ({booking.user.email})
                      </span>
                    </>
                  ) : (
                    <span className="text-gray-400 italic">No user assigned</span>
                  )}
                </p>
              </div>

              <form onSubmit={handleTransferSubmit} className="space-y-4">
                {/* 1. Transferred To User Dropdown */}
                <div className="space-y-1.5">
                  <label
                    htmlFor="transfer_user_select"
                    className="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300"
                  >
                    1. Transfer To Registered User <span className="text-rose-500">*</span>
                  </label>
                  <select
                    id="transfer_user_select"
                    required
                    value={selectedUserId}
                    onChange={(e) => setSelectedUserId(e.target.value)}
                    className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white font-bold text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-inner"
                  >
                    <option value="" disabled>-- Select a registered user --</option>
                    {users.map((u) => {
                      const isCurrent = String(u.id) === String(booking.user_id);
                      const isVehicleOwner = String(u.id) === String(ownerUserId);
                      const vCount = u.vehicles?.length || 0;
                      return (
                        <option key={u.id} value={String(u.id)}>
                          {u.name} ({u.email}) — {vCount} vehicle{vCount === 1 ? '' : 's'} {isVehicleOwner ? '• [Vehicle Owner]' : (isCurrent ? '• [Currently Booked by]' : '')}
                        </option>
                      );
                    })}
                  </select>
                  <p className="text-[11px] text-gray-500 dark:text-gray-400">
                    Select the registered user who will receive this booking.
                  </p>
                </div>

                {/* 2. Vehicle Dropdown (Vehicles of the selected user) */}
                <div className="space-y-1.5">
                  <label
                    htmlFor="transfer_vehicle_select"
                    className="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300"
                  >
                    2. Select Vehicle from User's Fleet <span className="text-rose-500">*</span>
                  </label>

                  {userVehicles.length === 0 ? (
                    <div className="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-xs text-amber-800 dark:text-amber-200 flex items-start gap-2">
                      <AlertTriangle className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                      <div>
                        <span className="font-bold block">No Vehicles in Fleet</span>
                        <span>This user does not have any vehicles registered in their fleet yet. A vehicle is required to complete the transfer.</span>
                      </div>
                    </div>
                  ) : (
                    <>
                      <select
                        id="transfer_vehicle_select"
                        required
                        value={selectedVehicleId}
                        onChange={(e) => setSelectedVehicleId(e.target.value)}
                        className="w-full px-3.5 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white font-bold text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-inner"
                      >
                        <option value="" disabled>-- Select a vehicle --</option>
                        {userVehicles.map((v) => (
                          <option key={v.id} value={String(v.id)}>
                            {v.name} ({v.license_plate}) {v.is_available ? '— [✓ Available]' : '— [✗ Schedule Conflict / Unavailable]'}
                          </option>
                        ))}
                      </select>

                      {/* Availability Check Feedback */}
                      {selectedVehicleObj && (
                        <div className="mt-2">
                          {selectedVehicleObj.is_available ? (
                            <div className="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs text-emerald-800 dark:text-emerald-200 flex items-start gap-2 animate-in fade-in duration-150">
                              <CheckCircle2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                              <div className="space-y-0.5">
                                <span className="font-extrabold block text-emerald-900 dark:text-emerald-100">
                                  ✓ Vehicle is Available for this Booking
                                </span>
                                <p className="text-[11px] text-emerald-700 dark:text-emerald-300">
                                  No conflicts detected between {formatDate(booking.start_date)} ({formatTime(booking.pickup_time || '00:00')}) and {formatDate(booking.end_date)} ({formatTime(booking.return_time || '23:59')}) (+2h carwash buffer).
                                </p>
                              </div>
                            </div>
                          ) : (
                            <div className="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs text-rose-800 dark:text-rose-200 flex items-start gap-2 animate-in fade-in duration-150">
                              <AlertTriangle className="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" />
                              <div className="space-y-0.5">
                                <span className="font-extrabold block text-rose-900 dark:text-rose-100">
                                  ✗ Schedule Conflict Detected
                                </span>
                                {selectedVehicleObj.conflict ? (
                                  <p className="text-[11px] text-rose-700 dark:text-rose-300 leading-tight">
                                    Already reserved by <span className="font-bold">{selectedVehicleObj.conflict.customer_name}</span> (#{selectedVehicleObj.conflict.booking_code}) from {selectedVehicleObj.conflict.start_date} to {selectedVehicleObj.conflict.end_date} (+2h carwash buffer). Please choose another vehicle.
                                  </p>
                                ) : (
                                  <p className="text-[11px] text-rose-700 dark:text-rose-300">
                                    Vehicle is currently marked as <span className="font-bold uppercase">{selectedVehicleObj.status}</span>. Please choose another vehicle.
                                  </p>
                                )}
                              </div>
                            </div>
                          )}
                        </div>
                      )}
                    </>
                  )}
                </div>

                <div className="flex items-center justify-end gap-2.5 pt-2">
                  <button
                    type="button"
                    onClick={() => setTransferModalOpen(false)}
                    disabled={isTransferring}
                    className="px-4 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    disabled={
                      isTransferring ||
                      !selectedUserId ||
                      !selectedVehicleId ||
                      !selectedVehicleObj?.is_available ||
                      (String(selectedUserId) === String(booking.user_id) && String(selectedVehicleId) === String(booking.vehicle_id))
                    }
                    className="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-xs font-extrabold text-white shadow-md shadow-indigo-600/30 transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:pointer-events-none"
                  >
                    <UserCheck className="w-4 h-4" />
                    {isTransferring ? 'Transferring...' : 'Confirm Transfer'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  );
}
