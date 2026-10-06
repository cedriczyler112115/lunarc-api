import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Calendar,
  Car,
  MapPin,
  Clock,
  ArrowLeft,
  CheckCircle2,
  AlertCircle,
  AlertTriangle,
  Upload,
  DollarSign,
  User,
  Phone,
  Info,
  ShieldCheck,
  FileText,
  Lock,
  ChevronLeft,
  ChevronRight
} from 'lucide-react';

export default function Edit({
  booking,
  vehicles = [],
  destinations = [],
  destinationsHierarchy = {},
  canEditAll = false
}) {
  // Find initial region and province for the booking's destination
  let initialRegion = '';
  let initialProvince = '';
  if (destinationsHierarchy && booking.destination_id) {
    for (const [r, provs] of Object.entries(destinationsHierarchy)) {
      for (const [p, cities] of Object.entries(provs)) {
        if (cities.some((c) => String(c.id) === String(booking.destination_id))) {
          initialRegion = r;
          initialProvince = p;
          break;
        }
      }
      if (initialRegion) break;
    }
  }

  const availableRegions = Object.keys(destinationsHierarchy || {});
  const [selectedRegion, setSelectedRegion] = useState(initialRegion || availableRegions[0] || '');
  const [selectedProvince, setSelectedProvince] = useState(initialProvince || '');
  const [availableProvinces, setAvailableProvinces] = useState([]);
  const [availableCities, setAvailableCities] = useState([]);

  const { data, setData, processing, errors } = useForm({
    vehicle_id: String(booking.vehicle_id || ''),
    destination_id: String(booking.destination_id || ''),
    customer_name: booking.customer_name || '',
    customer_phone: booking.customer_phone || '',
    driver_license: null,
    reservation_fee: String(booking.reservation_fee || '0'),
    pickup_location: booking.pickup_location || '',
    pickup_time: booking.pickup_time || '08:00',
    return_time: booking.return_time || '18:00',
    start_date: booking.start_date || '',
    end_date: booking.end_date || '',
    status: booking.status || 'pending',
    notes: booking.notes || '',
  });

  const [licensePreview, setLicensePreview] = useState(
    booking.driver_license_path ? `/${booking.driver_license_path}` : null
  );

  const activeVehicle = vehicles.find((v) => String(v.id) === String(data.vehicle_id)) || booking.vehicle || null;
  const activeDestination = destinations.find((d) => String(d.id) === String(data.destination_id)) || booking.destination_model || null;

  // Calendar State
  const initialYear = booking.start_date ? new Date(booking.start_date).getFullYear() : new Date().getFullYear();
  const initialMonth = booking.start_date ? new Date(booking.start_date).getMonth() : new Date().getMonth();
  const [calendarYear, setCalendarYear] = useState(initialYear);
  const [calendarMonth, setCalendarMonth] = useState(initialMonth);
  const [calendarDays, setCalendarDays] = useState([]);
  const [hoveredDay, setHoveredDay] = useState(null);

  // Pricing & Buffer Conflict State
  const [totalDays, setTotalDays] = useState(booking.total_days || 1);
  const [excessHours, setExcessHours] = useState(0);
  const [totalPrice, setTotalPrice] = useState(Number(booking.total_price || 0));
  const [hasConflict, setHasConflict] = useState(false);
  const [conflictMessage, setConflictMessage] = useState('');

  // Destination cascading state
  useEffect(() => {
    if (selectedRegion && destinationsHierarchy[selectedRegion]) {
      const provs = Object.keys(destinationsHierarchy[selectedRegion]);
      setAvailableProvinces(provs);
      if (!selectedProvince || !provs.includes(selectedProvince)) {
        setSelectedProvince(provs[0] || '');
      }
    } else {
      setAvailableProvinces([]);
      setSelectedProvince('');
    }
  }, [selectedRegion]);

  useEffect(() => {
    if (selectedRegion && selectedProvince && destinationsHierarchy[selectedRegion]?.[selectedProvince]) {
      const cities = destinationsHierarchy[selectedRegion][selectedProvince];
      setAvailableCities(cities);
      if (cities.length > 0 && (!data.destination_id || !cities.some((c) => String(c.id) === String(data.destination_id)))) {
        setData('destination_id', String(cities[0].id));
      }
    } else {
      setAvailableCities([]);
    }
  }, [selectedProvince]);

  const getCityRate = (cityObj) => {
    if (!cityObj) return 0;
    if (activeVehicle && activeVehicle.vehicle_type_id) {
      if (cityObj.type_rates && cityObj.type_rates[activeVehicle.vehicle_type_id] !== undefined) {
        return Number(cityObj.type_rates[activeVehicle.vehicle_type_id]);
      }
      if (cityObj.vehicle_rates) {
        const vRate = cityObj.vehicle_rates.find((r) => String(r.vehicle_type_id) === String(activeVehicle.vehicle_type_id));
        if (vRate) return Number(vRate.destination_rate);
      }
    }
    return Number(cityObj.destination_rate ?? cityObj.base_rate ?? 0);
  };

  const currentDestinationRate = activeDestination ? getCityRate(activeDestination) : (Number(booking.destination_rate) || 0);

  // Live conflict checking for the selected vehicle with 2-Hour Carwash Buffer (excluding this booking)
  useEffect(() => {
    setHasConflict(false);
    setConflictMessage('');

    if (!activeVehicle || !data.start_date || !data.end_date) return;

    const pTime = data.pickup_time || '00:00';
    const rTime = data.return_time || '23:59';

    const reqStart = new Date(`${data.start_date}T${pTime}`);
    const reqEnd = new Date(`${data.end_date}T${rTime}`);

    if (isNaN(reqStart.getTime()) || isNaN(reqEnd.getTime())) return;

    const reqEndWithBuffer = new Date(reqEnd.getTime() + (2 * 60 * 60 * 1000));
    const otherBookings = (activeVehicle.bookings || []).filter((b) => String(b.id) !== String(booking.id));

    for (let bk of otherBookings) {
      const bkPTime = bk.pickup_time || '00:00';
      const bkRTime = bk.return_time || '23:59';

      const bStart = new Date(`${bk.start_date}T${bkPTime}`);
      const bEnd = new Date(`${bk.end_date}T${bkRTime}`);

      if (isNaN(bStart.getTime()) || isNaN(bEnd.getTime())) continue;

      const bEndWithBuffer = new Date(bEnd.getTime() + (2 * 60 * 60 * 1000));

      // Conflict formula with 2h carwash buffer: reqStart < bEndWithBuffer AND reqEndWithBuffer > bStart
      if (reqStart < bEndWithBuffer && reqEndWithBuffer > bStart) {
        setHasConflict(true);
        const customerName = bk.customer_name || 'Another Customer';
        const availHours = bEndWithBuffer.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        setConflictMessage(
          `Vehicle is reserved by ${customerName} until ${bk.end_date} ${bkRTime} (+2h Carwash Buffer). Available from ${availHours} onwards.`
        );
        break;
      }
    }
  }, [data.vehicle_id, data.start_date, data.end_date, data.pickup_time, data.return_time, activeVehicle]);

  // Recalculate duration & pricing based on 24-hr schedule & excess hours
  useEffect(() => {
    if (!data.start_date || !data.end_date) return;

    const pTime = data.pickup_time || '00:00';
    const rTime = data.return_time || '00:00';

    const start = new Date(`${data.start_date}T${pTime}`);
    const end = new Date(`${data.end_date}T${rTime}`);

    const diffMs = end.getTime() - start.getTime();
    let days = 1;
    let excess = 0;

    if (!isNaN(diffMs) && diffMs > 0) {
      const totalMinutes = Math.max(0, Math.ceil(diffMs / (1000 * 60)));
      const totalHours = Math.ceil(totalMinutes / 60);

      if (totalHours <= 24) {
        days = 1;
        excess = 0;
      } else {
        const fullDays = Math.floor(totalHours / 24);
        const remHours = totalHours % 24;
        if (remHours > 5) {
          days = fullDays + 1;
          excess = 0;
        } else {
          days = fullDays;
          excess = remHours;
        }
      }
    }

    setTotalDays(days);
    setExcessHours(excess);

    const excessFee = excess * 200;
    const subtotal = (days * currentDestinationRate) + excessFee;
    const resFee = Number(data.reservation_fee || 0);
    setTotalPrice(Math.max(0, subtotal - resFee));
  }, [data.vehicle_id, data.destination_id, data.start_date, data.end_date, data.pickup_time, data.return_time, data.reservation_fee, currentDestinationRate]);

  // Build Calendar Days for Availability Grid
  const formatDateStr = (d) => {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
  };

  const buildDayObject = (dObj, dateStr, isCurrentMonth) => {
    let isBooked = false;
    let isPartial = false;
    let bookingInfo = null;

    if (activeVehicle && activeVehicle.bookings) {
      const otherBookings = activeVehicle.bookings.filter((b) => String(b.id) !== String(booking.id));
      for (let bk of otherBookings) {
        const start = bk.start_date;
        const end = bk.end_date;
        if (dateStr > start && dateStr < end) {
          isBooked = true;
          bookingInfo = bk;
          break;
        } else if (dateStr === start || dateStr === end) {
          isPartial = true;
          bookingInfo = bk;
        }
      }
    }

    return {
      dateStr,
      dayNum: dObj.getDate(),
      isCurrentMonth,
      isBooked,
      isPartial,
      bookingInfo,
    };
  };

  useEffect(() => {
    const firstDay = new Date(calendarYear, calendarMonth, 1);
    const lastDay = new Date(calendarYear, calendarMonth + 1, 0);
    const startingDay = firstDay.getDay();
    const totalDaysInMonth = lastDay.getDate();

    const days = [];
    const prevMonthLastDay = new Date(calendarYear, calendarMonth, 0).getDate();

    // Trailing previous month days
    for (let i = startingDay - 1; i >= 0; i--) {
      const d = new Date(calendarYear, calendarMonth - 1, prevMonthLastDay - i);
      const dateStr = formatDateStr(d);
      days.push(buildDayObject(d, dateStr, false));
    }

    // Current month days
    for (let i = 1; i <= totalDaysInMonth; i++) {
      const d = new Date(calendarYear, calendarMonth, i);
      const dateStr = formatDateStr(d);
      days.push(buildDayObject(d, dateStr, true));
    }

    setCalendarDays(days);
  }, [calendarYear, calendarMonth, activeVehicle]);

  const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  const calendarMonthLabel = `${monthNames[calendarMonth]} ${calendarYear}`;

  const prevCalendarMonth = () => {
    if (calendarMonth === 0) {
      setCalendarMonth(11);
      setCalendarYear((y) => y - 1);
    } else {
      setCalendarMonth((m) => m - 1);
    }
  };

  const nextCalendarMonth = () => {
    if (calendarMonth === 11) {
      setCalendarMonth(0);
      setCalendarYear((y) => y + 1);
    } else {
      setCalendarMonth((m) => m + 1);
    }
  };

  const selectCalendarDate = (dateStr) => {
    if (!data.start_date || (data.start_date && data.end_date && data.start_date !== data.end_date)) {
      setData((prev) => ({ ...prev, start_date: dateStr, end_date: dateStr }));
    } else if (dateStr >= data.start_date) {
      setData('end_date', dateStr);
    } else {
      setData((prev) => ({ ...prev, start_date: dateStr, end_date: dateStr }));
    }
  };

  const isSelectedDate = (dateStr) => {
    if (!data.start_date) return false;
    if (!data.end_date) return dateStr === data.start_date;
    return dateStr >= data.start_date && dateStr <= data.end_date;
  };

  const handleLicenseChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setData('driver_license', file);
      setLicensePreview(URL.createObjectURL(file));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    router.post(`/bookings/${booking.id}`, {
      _method: 'PUT',
      ...data,
    }, {
      forceFormData: true,
    });
  };

  return (
    <AppLayout title={`Edit Booking #${booking.booking_code}`}>
      <Head title={`Edit Booking #${booking.booking_code}`} />

      <div className="max-w-5xl mx-auto space-y-6 pb-12">
        {/* Header Bar */}
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700">
          <div>
            <div className="flex items-center gap-2">
              <Link
                href="/bookings"
                className="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition"
                title="Back to Bookings"
              >
                <ArrowLeft className="w-4 h-4" />
              </Link>
              <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">
                Edit Booking #{booking.booking_code}
              </h1>
            </div>
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1 pl-10">
              Update reservation dates, vehicle assignment, Mindanao destination routes, and 2-hour buffer conflict management.
            </p>
          </div>

          <div className="flex items-center gap-2 pl-10 sm:pl-0">
            <span
              className={`px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider ${
                booking.status === 'pending'
                  ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300 dark:border-amber-800'
                  : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'
              }`}
            >
              Current Status: {booking.status}
            </span>
          </div>
        </div>

        {/* Status Notification Banner */}
        {!canEditAll ? (
          <div className="p-5 rounded-3xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 flex items-start gap-3 shadow-xs">
            <Lock className="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
            <div className="space-y-1 text-xs">
              <h3 className="font-extrabold text-sm text-amber-900 dark:text-amber-200">
                Full Details Locked ({booking.status.toUpperCase()})
              </h3>
              <p className="text-amber-700 dark:text-amber-300 leading-relaxed">
                Only reservations with a <strong>Pending</strong> status can have their vehicle, schedule, customer, and destination details modified. Because this reservation is already <strong>{booking.status}</strong>, you may only update the status or internal notes.
              </p>
            </div>
          </div>
        ) : (
          <div className="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-center justify-between gap-3 shadow-xs">
            <div className="flex items-center gap-2.5">
              <CheckCircle2 className="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
              <p className="text-xs font-bold">
                Status is <strong>Pending</strong> &mdash; Full edit mode enabled. Vehicle availability includes 2-hour buffer validation.
              </p>
            </div>
          </div>
        )}

        {/* Validation Errors Alert */}
        {Object.keys(errors).length > 0 && (
          <div className="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-950/40 dark:text-rose-200 rounded-r-2xl shadow-xs space-y-1 text-xs">
            <h4 className="font-extrabold text-sm mb-1 flex items-center gap-1.5 text-rose-700 dark:text-rose-400">
              <AlertCircle className="w-4 h-4" />
              Validation / Schedule Conflict Error
            </h4>
            <ul className="list-disc list-inside space-y-1 font-semibold">
              {Object.values(errors).map((err, i) => (
                <li key={i}>{err}</li>
              ))}
            </ul>
          </div>
        )}

        {/* Main Edit Form */}
        <form onSubmit={handleSubmit} className="space-y-6">
          {canEditAll ? (
            <>
              {/* Step 1: Vehicle Selection */}
              <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-5">
                <h3 className="text-base font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <Car className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    1. Select Assigned Fleet Vehicle
                  </span>
                  <span className="text-xs font-semibold text-gray-400">Step 1 of 5</span>
                </h3>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                    Available Car Fleet *
                  </label>
                  <select
                    value={data.vehicle_id}
                    onChange={(e) => setData('vehicle_id', e.target.value)}
                    className="w-full text-sm font-extrabold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 p-3"
                    required
                  >
                    <option value="">-- Choose a Car from Fleet --</option>
                    {vehicles.map((v) => (
                      <option key={v.id} value={String(v.id)}>
                        {v.name} ({v.license_plate}) — ₱{Number(v.daily_rate).toLocaleString(undefined, { minimumFractionDigits: 2 })}/day [{v.transmission}, {v.seats} Seats]
                      </option>
                    ))}
                  </select>
                </div>

                {/* Selected Vehicle Banner Card */}
                {activeVehicle && (
                  <div className="p-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 shadow-md border border-slate-700">
                    <div className="flex items-center space-x-4 w-full md:w-auto">
                      {activeVehicle.image_path ? (
                        <img
                          src={activeVehicle.image_path.startsWith('http') ? activeVehicle.image_path : `/${activeVehicle.image_path}`}
                          alt={activeVehicle.name}
                          className="w-24 h-16 rounded-xl object-cover border border-white/20 shadow-xs shrink-0"
                        />
                      ) : (
                        <div className="w-16 h-16 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400 shrink-0">
                          <Car className="w-7 h-7 opacity-50" />
                        </div>
                      )}

                      <div className="space-y-1">
                        <span className="text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500 text-white px-2 py-0.5 rounded shadow-xs">
                          Assigned Car
                        </span>
                        <h4 className="text-base font-black text-white leading-tight">{activeVehicle.name}</h4>
                        <p className="text-xs text-indigo-200 font-medium">
                          Plate: <span className="font-mono font-bold text-amber-400">{activeVehicle.license_plate}</span> &bull; Transmission:{' '}
                          <span className="font-bold">{activeVehicle.transmission}</span> &bull; Capacity: <span className="font-bold">{activeVehicle.seats} Seats</span>
                        </p>
                      </div>
                    </div>

                    <div className="text-right w-full md:w-auto border-t md:border-t-0 pt-2 md:pt-0 border-slate-800">
                      <span className="text-xs text-indigo-200 uppercase font-bold tracking-wider block">Daily Base Rate</span>
                      <div className="text-xl font-black text-emerald-400">
                        ₱{Number(activeVehicle.daily_rate || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Step 2: Destination Selection */}
              <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-5">
                <h3 className="text-base font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <MapPin className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    2. Select Mindanao Destination Route
                  </span>
                  <span className="text-xs font-semibold text-gray-400">Step 2 of 5</span>
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {/* Region */}
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">1. Region</label>
                    <select
                      value={selectedRegion}
                      onChange={(e) => setSelectedRegion(e.target.value)}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 p-3"
                    >
                      {availableRegions.map((reg) => (
                        <option key={reg} value={reg}>
                          {reg}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Province */}
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">2. Province</label>
                    <select
                      value={selectedProvince}
                      onChange={(e) => setSelectedProvince(e.target.value)}
                      disabled={!selectedRegion}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 p-3 disabled:opacity-50"
                    >
                      {availableProvinces.map((prov) => (
                        <option key={prov} value={prov}>
                          {prov}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* City */}
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">3. City / Destination *</label>
                    <select
                      value={data.destination_id}
                      onChange={(e) => setData('destination_id', e.target.value)}
                      disabled={!selectedProvince}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 p-3 disabled:opacity-50"
                      required
                    >
                      <option value="">-- Choose City / Municipality --</option>
                      {availableCities.map((item) => {
                        const rate = getCityRate(item);
                        return (
                          <option key={item.id} value={String(item.id)}>
                            {item.city} — ₱{Number(rate).toLocaleString(undefined, { minimumFractionDigits: 2 })} / day
                          </option>
                        );
                      })}
                    </select>
                  </div>
                </div>

                {/* Confirmed Route Summary */}
                {activeDestination && (
                  <div className="p-4 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-2xl flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                      <div className="p-2.5 bg-sky-500 text-white rounded-xl shrink-0">
                        <MapPin className="w-5 h-5" />
                      </div>
                      <div>
                        <div className="text-[10px] text-sky-700 dark:text-sky-300 font-extrabold uppercase tracking-wider">
                          Selected Route
                        </div>
                        <div className="text-sm font-extrabold text-gray-900 dark:text-white">
                          {activeDestination.region} &mdash; {activeDestination.province} &mdash; {activeDestination.city}
                        </div>
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-xs font-semibold text-gray-500 dark:text-gray-400">Destination Fee / Day</div>
                      <div className="text-base font-black text-sky-600 dark:text-sky-400">
                        ₱{Number(currentDestinationRate).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Step 3: Interactive Availability Calendar & 2-Hour Carwash Buffer */}
              <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-5">
                <h3 className="text-base font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <Calendar className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    3. Schedule &amp; Availability Calendar (2-Hour Carwash Buffer)
                  </span>
                  <span className="text-xs font-semibold text-gray-400">Step 3 of 5</span>
                </h3>

                {/* Live 2-Hour Carwash Buffer Conflict Banner */}
                {hasConflict && (
                  <div className="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs flex items-start gap-3 shadow-xs">
                    <AlertTriangle className="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                    <div className="space-y-1">
                      <p className="font-extrabold text-sm text-rose-800 dark:text-rose-300">
                        Schedule Conflict Detected (+2-Hour Carwash Buffer Rule)
                      </p>
                      <p className="text-rose-700 dark:text-rose-300 leading-relaxed font-semibold">
                        {conflictMessage}
                      </p>
                      <p className="text-[11px] text-rose-600/80 dark:text-rose-400 italic">
                        Please adjust your pickup or return schedule to allow at least 2 hours of buffer for car cleaning and turnaround.
                      </p>
                    </div>
                  </div>
                )}

                {/* Interactive Month Picker Calendar */}
                {activeVehicle && (
                  <div className="p-6 bg-gray-50 dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                          <span>📅 Vehicle Availability Calendar</span>
                          <span className="text-xs font-semibold text-gray-500">(Click to select dates; hover red/amber days to inspect buffer)</span>
                        </h4>
                      </div>
                      <div className="flex items-center space-x-2">
                        <button
                          type="button"
                          onClick={prevCalendarMonth}
                          className="px-2.5 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold hover:bg-gray-100 transition"
                        >
                          &larr; Prev
                        </button>
                        <span className="text-xs font-extrabold text-gray-800 dark:text-gray-200 min-w-[120px] text-center">
                          {calendarMonthLabel}
                        </span>
                        <button
                          type="button"
                          onClick={nextCalendarMonth}
                          className="px-2.5 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold hover:bg-gray-100 transition"
                        >
                          Next &rarr;
                        </button>
                      </div>
                    </div>

                    {/* Calendar Grid */}
                    <div className="grid grid-cols-7 gap-1 text-center text-xs">
                      <div className="font-bold text-rose-500 py-1">Sun</div>
                      <div className="font-bold text-gray-500 py-1">Mon</div>
                      <div className="font-bold text-gray-500 py-1">Tue</div>
                      <div className="font-bold text-gray-500 py-1">Wed</div>
                      <div className="font-bold text-gray-500 py-1">Thu</div>
                      <div className="font-bold text-gray-500 py-1">Fri</div>
                      <div className="font-bold text-gray-500 py-1">Sat</div>

                      {calendarDays.map((day, idx) => {
                        const selected = isSelectedDate(day.dateStr);

                        if (day.isBooked) {
                          return (
                            <div
                              key={idx}
                              onMouseEnter={() => setHoveredDay(day.dateStr)}
                              onMouseLeave={() => setHoveredDay(null)}
                              className="relative h-14 p-1 rounded-xl bg-rose-100 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 cursor-not-allowed flex flex-col justify-between items-center opacity-90 transition"
                            >
                              <span className="text-xs font-black">{day.dayNum}</span>
                              <span className="text-[9px] font-black uppercase tracking-wider bg-rose-600 text-white px-1 py-0.2 rounded shadow-xs">
                                Booked
                              </span>

                              {hoveredDay === day.dateStr && day.bookingInfo && (
                                <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white rounded-xl shadow-2xl z-50 text-left pointer-events-none border border-slate-700 space-y-1 animate-in fade-in duration-150">
                                  <div className="text-[10px] font-bold text-rose-400 uppercase tracking-wider">
                                    🔒 Fully Reserved Date
                                  </div>
                                  <p className="text-xs font-bold text-white">
                                    Renter: <span className="text-emerald-400 font-extrabold">{day.bookingInfo.customer_name}</span>
                                  </p>
                                  <p className="text-xs text-slate-300">
                                    Route: <span className="italic text-sky-300 font-medium">{day.bookingInfo.destination || 'Standard Rental'}</span>
                                  </p>
                                  <p className="text-[10px] text-slate-400 border-t border-slate-800 pt-1 mt-1">
                                    {day.bookingInfo.start_date} to {day.bookingInfo.end_date}
                                  </p>
                                </div>
                              )}
                            </div>
                          );
                        }

                        if (!day.isBooked && day.isPartial && day.isCurrentMonth) {
                          return (
                            <div key={idx} className="relative">
                              <button
                                type="button"
                                onClick={() => selectCalendarDate(day.dateStr)}
                                onMouseEnter={() => setHoveredDay(day.dateStr)}
                                onMouseLeave={() => setHoveredDay(null)}
                                className={`w-full h-14 p-1 rounded-xl border flex flex-col justify-between items-center transition-colors font-semibold ${
                                  selected
                                    ? 'bg-emerald-600 text-white border-emerald-600'
                                    : 'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border-amber-300 dark:border-amber-700 hover:bg-amber-100'
                                }`}
                              >
                                <span className="text-xs font-black">{day.dayNum}</span>
                                <span className={`text-[9px] font-bold px-1 rounded ${selected ? 'bg-emerald-700 text-white' : 'bg-amber-500 text-white'}`}>
                                  {selected ? 'Selected' : 'Has Booking'}
                                </span>
                              </button>

                              {hoveredDay === day.dateStr && day.bookingInfo && (
                                <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white rounded-xl shadow-2xl z-50 text-left pointer-events-none border border-slate-700 space-y-1 animate-in fade-in duration-150">
                                  <div className="text-[10px] font-bold text-amber-400 uppercase tracking-wider">
                                    ⏰ Partial Booking / Return Date
                                  </div>
                                  <p className="text-xs font-bold text-white">
                                    Renter: <span className="text-emerald-400 font-extrabold">{day.bookingInfo.customer_name}</span>
                                  </p>
                                  <p className="text-xs text-slate-300">
                                    Reserved: <span className="font-bold text-amber-300">{day.bookingInfo.pickup_time || '00:00'}</span> to{' '}
                                    <span className="font-bold text-amber-300">{day.bookingInfo.return_time || '23:59'}</span>
                                  </p>
                                  <p className="text-[10px] text-sky-300 italic border-t border-slate-800 pt-1 mt-1">
                                    + 2h Carwash Buffer after return. Available outside window!
                                  </p>
                                </div>
                              )}
                            </div>
                          );
                        }

                        if (day.isCurrentMonth) {
                          return (
                            <button
                              key={idx}
                              type="button"
                              onClick={() => selectCalendarDate(day.dateStr)}
                              className={`w-full h-14 p-1 rounded-xl border flex flex-col justify-between items-center transition-colors font-semibold ${
                                selected
                                  ? 'bg-emerald-600 text-white border-emerald-600'
                                  : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 hover:border-emerald-300'
                              }`}
                            >
                              <span className="text-xs font-black">{day.dayNum}</span>
                              <span className="text-[9px] font-bold">{selected ? 'Selected' : 'Available'}</span>
                            </button>
                          );
                        }

                        return (
                          <div key={idx} className="h-14 p-1 rounded-xl bg-gray-100/50 dark:bg-gray-800/30 text-gray-400 border border-transparent flex items-center justify-center">
                            <span className="text-xs">{day.dayNum}</span>
                          </div>
                        );
                      })}
                    </div>
                  </div>
                )}

                {/* Schedule Inputs */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Pickup / Start Date *
                    </label>
                    <input
                      type="date"
                      value={data.start_date}
                      onChange={(e) => setData('start_date', e.target.value)}
                      required
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                    {errors.start_date && <p className="text-rose-500 text-[11px] mt-1">{errors.start_date}</p>}
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Return / End Date *
                    </label>
                    <input
                      type="date"
                      value={data.end_date}
                      min={data.start_date}
                      onChange={(e) => setData('end_date', e.target.value)}
                      required
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                    {errors.end_date && <p className="text-rose-500 text-[11px] mt-1">{errors.end_date}</p>}
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Pickup Time *
                    </label>
                    <input
                      type="time"
                      value={data.pickup_time}
                      onChange={(e) => setData('pickup_time', e.target.value)}
                      required
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Return Time *
                    </label>
                    <input
                      type="time"
                      value={data.return_time}
                      onChange={(e) => setData('return_time', e.target.value)}
                      required
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                  </div>

                  <div className="sm:col-span-2">
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Pickup / Dropoff Terminal Location *
                    </label>
                    <input
                      type="text"
                      value={data.pickup_location}
                      onChange={(e) => setData('pickup_location', e.target.value)}
                      required
                      placeholder="e.g. Bancasi Airport / Robinsons Place Butuan"
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-semibold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                    {errors.pickup_location && <p className="text-rose-500 text-[11px] mt-1">{errors.pickup_location}</p>}
                  </div>
                </div>
              </div>

              {/* Step 4: Live Pricing Calculation (24h standard & excess hour calculation) */}
              <div className="bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <h3 className="text-base font-extrabold text-gray-900 dark:text-white flex items-center justify-between border-b pb-2 border-emerald-200/60 dark:border-emerald-800/60">
                  <span className="flex items-center gap-2">
                    <DollarSign className="w-5 h-5 text-amber-500" />
                    4. Live Rental Pricing Summary
                  </span>
                  <span className="text-xs font-semibold text-gray-400">Step 4 of 5</span>
                </h3>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                  <div className="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800">
                    <span className="text-gray-500 dark:text-gray-400 block font-semibold">Total Duration</span>
                    <span className="text-base font-black text-gray-900 dark:text-white mt-1 block">
                      {totalDays} {totalDays === 1 ? 'Day' : 'Days'}
                      {excessHours > 0 && <span className="text-amber-600 font-bold ml-1 text-xs">(+{excessHours}h @ ₱200/hr)</span>}
                    </span>
                    <p className="text-[10px] text-gray-400 mt-1">24-hour cycle standard with 5h grace buffer</p>
                  </div>

                  <div className="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800">
                    <span className="text-gray-500 dark:text-gray-400 block font-semibold">Destination Rate / Day</span>
                    <span className="text-base font-black text-emerald-600 dark:text-emerald-400 mt-1 block">
                      ₱{Number(currentDestinationRate).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                    </span>
                    <p className="text-[10px] text-gray-400 mt-1">Applied for vehicle category</p>
                  </div>

                  <div className="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-800">
                    <label className="text-gray-500 dark:text-gray-400 block font-semibold">Reservation Fee Deducted</label>
                    <input
                      type="number"
                      value={data.reservation_fee}
                      onChange={(e) => setData('reservation_fee', e.target.value)}
                      min="0"
                      className="mt-1 w-full px-2 py-1 text-xs font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg"
                    />
                    <p className="text-[10px] text-gray-400 mt-1">Deducted from final total</p>
                  </div>
                </div>

                <div className="flex items-center justify-between pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60">
                  <span className="text-sm font-extrabold text-gray-800 dark:text-gray-200">Total Price Due:</span>
                  <span className="text-2xl font-black text-amber-600 dark:text-amber-400">
                    ₱{Number(totalPrice).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </span>
                </div>
              </div>

              {/* Step 5: Customer Details */}
              <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-5">
                <h3 className="text-base font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <User className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    5. Customer &amp; Driver Details
                  </span>
                  <span className="text-xs font-semibold text-gray-400">Step 5 of 5</span>
                </h3>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Customer Full Name *
                    </label>
                    <input
                      type="text"
                      value={data.customer_name}
                      onChange={(e) => setData('customer_name', e.target.value)}
                      required
                      placeholder="e.g. Maria Clara"
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-semibold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                    {errors.customer_name && <p className="text-rose-500 text-[11px] mt-1">{errors.customer_name}</p>}
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                      Contact Phone Number *
                    </label>
                    <input
                      type="text"
                      value={data.customer_phone}
                      onChange={(e) => setData('customer_phone', e.target.value)}
                      required
                      placeholder="e.g. 09171234567"
                      className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-semibold text-gray-900 dark:text-white focus:ring-emerald-500"
                    />
                    {errors.customer_phone && <p className="text-rose-500 text-[11px] mt-1">{errors.customer_phone}</p>}
                  </div>
                </div>

                {/* Driver's License Replacement */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Driver's License Photo (Optional replacement)
                  </label>
                  <div className="flex flex-col sm:flex-row items-start gap-4">
                    <input
                      type="file"
                      accept="image/*"
                      onChange={handleLicenseChange}
                      className="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/60 dark:file:text-emerald-300"
                    />
                    {licensePreview && (
                      <div className="w-32 h-20 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 shrink-0">
                        <img src={licensePreview} alt="Driver's License Preview" className="w-full h-full object-cover" />
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </>
          ) : (
            /* Read-Only Summary for Non-Pending Bookings */
            <div className="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
              <h2 className="text-xs font-black uppercase tracking-wider text-gray-400">
                Reservation Details Summary (Read-Only)
              </h2>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div className="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl">
                  <span className="text-gray-400 block font-semibold">Customer:</span>
                  <p className="font-extrabold text-gray-900 dark:text-white mt-0.5">{booking.customer_name} ({booking.customer_phone})</p>
                </div>
                <div className="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl">
                  <span className="text-gray-400 block font-semibold">Vehicle:</span>
                  <p className="font-extrabold text-gray-900 dark:text-white mt-0.5">{booking.vehicle?.name} ({booking.vehicle?.license_plate})</p>
                </div>
                <div className="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl">
                  <span className="text-gray-400 block font-semibold">Schedule:</span>
                  <p className="font-extrabold text-gray-900 dark:text-white mt-0.5">{booking.start_date} ({booking.pickup_time || '00:00'}) to {booking.end_date} ({booking.return_time || '00:00'})</p>
                </div>
                <div className="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl">
                  <span className="text-gray-400 block font-semibold">Destination & Total:</span>
                  <p className="font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">📍 {booking.destination} • ₱{Number(booking.total_price || 0).toLocaleString()}</p>
                </div>
              </div>
            </div>
          )}

          {/* Status & Notes Management (Always Available) */}
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-4">
            <h3 className="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2 border-b pb-2 border-gray-100 dark:border-gray-700">
              <ShieldCheck className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
              Reservation Status &amp; Notes
            </h3>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Booking Status *
                </label>
                <select
                  value={data.status}
                  onChange={(e) => setData('status', e.target.value)}
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs font-bold text-gray-900 dark:text-white focus:ring-emerald-500"
                >
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>

              <div className="sm:col-span-2">
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Internal Comments / Special Notes
                </label>
                <textarea
                  value={data.notes}
                  onChange={(e) => setData('notes', e.target.value)}
                  rows="3"
                  placeholder="Special instructions, pickup reminders, or payment notes..."
                  className="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white focus:ring-emerald-500"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
              <Link
                href={`/bookings/${booking.id}`}
                className="px-5 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold hover:bg-gray-200 transition"
              >
                Cancel
              </Link>

              <button
                type="submit"
                disabled={processing || (canEditAll && hasConflict)}
                className="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-95 transition"
              >
                {processing ? 'Saving...' : hasConflict ? 'Resolve Conflict to Save' : 'Save Changes'}
              </button>
            </div>
          </div>
        </form>
      </div>
    </AppLayout>
  );
}
