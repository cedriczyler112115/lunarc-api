import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
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
  Sparkles,
  Info,
  ChevronLeft,
  ChevronRight
} from 'lucide-react';

export default function Create({
  vehicles = [],
  destinations = [],
  destinationsHierarchy = {},
  selectedVehicle = null,
  selectedVehicleId = '',
  startDate = '',
  endDate = ''
}) {
  const initialVehicleId = selectedVehicleId || (vehicles[0]?.id ? String(vehicles[0].id) : '');

  const { data, setData, post, processing, errors } = useForm({
    vehicle_id: initialVehicleId,
    destination_id: '',
    customer_name: '',
    customer_phone: '',
    driver_license: null,
    reservation_fee: '0',
    pickup_location: '',
    pickup_time: '08:00',
    return_time: '18:00',
    start_date: startDate || new Date().toISOString().split('T')[0],
    end_date: endDate || new Date(Date.now() + 86400000).toISOString().split('T')[0],
    notes: '',
  });

  // Regions & Cascading Location State
  const availableRegions = Object.keys(destinationsHierarchy || {});
  const [selectedRegion, setSelectedRegion] = useState(availableRegions[0] || '');
  const [selectedProvince, setSelectedProvince] = useState('');
  const [availableProvinces, setAvailableProvinces] = useState([]);
  const [availableCities, setAvailableCities] = useState([]);

  // License image preview
  const [licensePreview, setLicensePreview] = useState(null);

  // Active Vehicle & Active Destination details
  const activeVehicle = vehicles.find((v) => String(v.id) === String(data.vehicle_id)) || null;
  const activeDestination = destinations.find((d) => String(d.id) === String(data.destination_id)) || null;

  // Calendar State
  const [calendarYear, setCalendarYear] = useState(new Date().getFullYear());
  const [calendarMonth, setCalendarMonth] = useState(new Date().getMonth());
  const [calendarDays, setCalendarDays] = useState([]);
  const [hoveredDay, setHoveredDay] = useState(null);

  // Cost & Conflict State
  const [totalDays, setTotalDays] = useState(1);
  const [excessHours, setExcessHours] = useState(0);
  const [totalPrice, setTotalPrice] = useState(0);
  const [hasConflict, setHasConflict] = useState(false);
  const [conflictMessage, setConflictMessage] = useState('');

  // Mindanao Region & Province Cascading Logic
  useEffect(() => {
    if (availableRegions.length > 0 && !selectedRegion) {
      if (availableRegions.includes('Region XIII (Caraga)')) {
        setSelectedRegion('Region XIII (Caraga)');
      } else {
        setSelectedRegion(availableRegions[0]);
      }
    }
  }, [destinationsHierarchy]);

  useEffect(() => {
    if (selectedRegion && destinationsHierarchy[selectedRegion]) {
      const provs = Object.keys(destinationsHierarchy[selectedRegion]);
      setAvailableProvinces(provs);
      if (selectedRegion === 'Region XIII (Caraga)' && provs.includes('Agusan del Norte')) {
        setSelectedProvince('Agusan del Norte');
      } else {
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
      if (cities.length > 0) {
        if (!data.destination_id || !cities.some(c => String(c.id) === String(data.destination_id))) {
          const defaultCity = cities.find(c => c.city === 'Butuan City') || cities[0];
          setData('destination_id', String(defaultCity.id));
        }
      }
    } else {
      setAvailableCities([]);
    }
  }, [selectedProvince]);

  // Calculate destination rate for vehicle type
  const getCityRate = (cityObj) => {
    if (!cityObj) return 0;
    if (activeVehicle && activeVehicle.vehicle_type_id && cityObj.vehicle_rates) {
      const vRate = cityObj.vehicle_rates.find(r => String(r.vehicle_type_id) === String(activeVehicle.vehicle_type_id));
      if (vRate) return Number(vRate.destination_rate);
    }
    return Number(cityObj.destination_rate || cityObj.base_rate || 0);
  };

  const currentDestinationRate = activeDestination ? getCityRate(activeDestination) : 0;

  // Conflict Checking Algorithm with 2-Hour Carwash Buffer
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
    const vehicleBookings = activeVehicle.bookings || [];

    for (let bk of vehicleBookings) {
      const bkPTime = bk.pickup_time || '00:00';
      const bkRTime = bk.return_time || '23:59';

      const bStart = new Date(`${bk.start_date}T${bkPTime}`);
      const bEnd = new Date(`${bk.end_date}T${bkRTime}`);

      if (isNaN(bStart.getTime()) || isNaN(bEnd.getTime())) continue;

      const bEndWithBuffer = new Date(bEnd.getTime() + (2 * 60 * 60 * 1000));

      // Schedule Conflict Formula: reqStart < bEndWithBuffer AND reqEndWithBuffer > bStart
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



  // Recalculate Days & Total Price
  useEffect(() => {
    if (!data.start_date || !data.end_date) {
      setTotalDays(0);
      setExcessHours(0);
      setTotalPrice(0);
      return;
    }

    const pTime = data.pickup_time || '00:00';
    const rTime = data.return_time || '00:00';

    const start = new Date(`${data.start_date}T${pTime}`);
    const end = new Date(`${data.end_date}T${rTime}`);

    const diffMs = end.getTime() - start.getTime();
    let days = 1;
    let excess = 0;

    if (isNaN(diffMs) || diffMs <= 0) {
      days = 1;
      excess = 0;
    } else {
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

  // Build Calendar Days
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
      for (let bk of activeVehicle.bookings) {
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
    post('/bookings', {
      forceFormData: true,
    });
  };

  return (
    <AppLayout title="Create Car Rental Booking">
      <Head title="Create Car Rental Booking" />

      <div className="space-y-6 pb-6">
        {/* Header Slot */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 transition-colors">
          <div>
            <h2 className="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <Calendar className="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
              Create Car Rental Booking
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Book a specific vehicle, set destination across Mindanao (Region &rarr; Province &rarr; City) with custom rental pricing.
            </p>
          </div>
          <Link
            href="/calendar"
            className="inline-flex items-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl shadow-md transition"
          >
            <Calendar className="w-4 h-4 mr-1.5" />
            View Booking Calendar
          </Link>
        </div>

        {/* Validation Errors Alert */}
        {Object.keys(errors).length > 0 && (
          <div className="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-950/40 dark:text-rose-200 rounded-r-2xl shadow-xs space-y-1 text-xs">
            <h4 className="font-extrabold text-sm mb-1 flex items-center gap-1.5 text-rose-700 dark:text-rose-400">
              <AlertCircle className="w-4 h-4" />
              Booking Validation / Conflict Error
            </h4>
            <ul className="list-disc list-inside space-y-1 font-semibold">
              {Object.values(errors).map((err, i) => (
                <li key={i}>{err}</li>
              ))}
            </ul>
          </div>
        )}

        {/* Main 4-Step Form Card */}
        <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
          <form onSubmit={handleSubmit} className="space-y-8">
            {/* Step 1: Select Specific Vehicle */}
            <div>
              <h3 className="text-lg font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                <span>1. Select Specific Vehicle</span>
                <span className="text-xs font-semibold text-gray-400">Step 1 of 4</span>
              </h3>

              <div>
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                  Available Car Fleet
                </label>
                <select
                  value={data.vehicle_id}
                  onChange={(e) => setData('vehicle_id', e.target.value)}
                  className="w-full text-base font-extrabold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3"
                  required
                >
                  <option value="">-- Choose a Car from Fleet --</option>
                  {vehicles.map((v) => (
                    <option key={v.id} value={v.id}>
                      {v.name} ({v.license_plate}) — ₱{Number(v.daily_rate).toLocaleString(undefined, { minimumFractionDigits: 2 })}/day [{v.transmission}, {v.seats} Seats]
                    </option>
                  ))}
                </select>
              </div>
            </div>

            {/* Selected Vehicle Summary Banner */}
            {activeVehicle && (
              <div className="p-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 shadow-md border border-slate-700">
                <div className="flex items-center space-x-4 w-full md:w-auto">
                  {activeVehicle.image_path ? (
                    <img
                      src={activeVehicle.image_path.startsWith('http') ? activeVehicle.image_path : `/${activeVehicle.image_path}`}
                      alt={activeVehicle.name}
                      className="w-28 h-20 rounded-xl object-cover border border-white/20 shadow-xs shrink-0"
                    />
                  ) : (
                    <div className="w-20 h-20 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400 shrink-0">
                      <Car className="w-8 h-8 opacity-50" />
                    </div>
                  )}

                  <div className="space-y-1">
                    <span className="text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500 text-white px-2 py-0.5 rounded shadow-xs">
                      Selected Car
                    </span>
                    <h4 className="text-lg font-black text-white leading-tight">{activeVehicle.name}</h4>
                    <p className="text-xs text-indigo-200 font-medium">
                      Plate: <span className="font-mono font-bold text-amber-400">{activeVehicle.license_plate}</span> &bull; Transmission:{' '}
                      <span className="font-bold">{activeVehicle.transmission}</span> &bull; Capacity: <span className="font-bold">{activeVehicle.seats} Seats</span>
                    </p>
                  </div>
                </div>

                <div className="text-right w-full md:w-auto border-t md:border-t-0 pt-2 md:pt-0 border-slate-800">
                  <span className="text-xs text-indigo-200 uppercase font-bold tracking-wider block">Starting Daily Price</span>
                  <div className="text-2xl font-black text-emerald-400">
                    ₱{Number(activeVehicle.daily_rate || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            )}

            {/* Step 2: Cascading Mindanao Destination */}
            <div>
              <h3 className="text-lg font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                <span>2. Select Mindanao Destination (Region &rarr; Province &rarr; City/Municipality)</span>
                <span className="text-xs font-semibold text-gray-400">Step 2 of 4</span>
              </h3>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Region */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">1. Select Region</label>
                  <select
                    value={selectedRegion}
                    onChange={(e) => setSelectedRegion(e.target.value)}
                    className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3"
                  >
                    <option value="">-- Choose Region --</option>
                    {availableRegions.map((reg) => (
                      <option key={reg} value={reg}>
                        {reg}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Province */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">2. Select Province</label>
                  <select
                    value={selectedProvince}
                    onChange={(e) => setSelectedProvince(e.target.value)}
                    disabled={!selectedRegion}
                    className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3 disabled:opacity-50"
                  >
                    <option value="">-- Choose Province --</option>
                    {availableProvinces.map((prov) => (
                      <option key={prov} value={prov}>
                        {prov}
                      </option>
                    ))}
                  </select>
                </div>

                {/* City */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">3. Select City & Rental Fee</label>
                  <select
                    value={data.destination_id}
                    onChange={(e) => setData('destination_id', e.target.value)}
                    disabled={!selectedProvince}
                    className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3 disabled:opacity-50"
                    required
                  >
                    <option value="">-- Choose City / Municipality --</option>
                    {availableCities.map((item) => {
                      const rate = getCityRate(item);
                      return (
                        <option key={item.id} value={item.id}>
                          {item.city} — {rate > 0 ? `+₱${Number(rate).toLocaleString(undefined, { minimumFractionDigits: 2 })} rental fee` : '₱0.00 (Base Area)'}
                        </option>
                      );
                    })}
                  </select>
                </div>
              </div>

              {/* Active Destination Summary Card */}
              {activeDestination && (
                <div className="mt-4 p-4 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-2xl flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="p-2.5 bg-sky-500 text-white rounded-xl shrink-0">
                      <MapPin className="w-5 h-5" />
                    </div>
                    <div>
                      <div className="text-[10px] text-sky-700 dark:text-sky-300 font-extrabold uppercase tracking-wider">
                        Confirmed Mindanao Route
                      </div>
                      <div className="text-base font-extrabold text-gray-900 dark:text-white">
                        {activeDestination.region} &mdash; {activeDestination.province} &mdash; {activeDestination.city}
                      </div>
                      <div className="text-xs text-gray-500 dark:text-gray-400">
                        {activeDestination.description || 'Standard destination route in Mindanao'}
                      </div>
                    </div>
                  </div>
                  <div className="text-right">
                    <div className="text-xs font-semibold text-gray-500 dark:text-gray-400">Destination Fee</div>
                    <div className="text-lg font-black text-sky-600 dark:text-sky-400">
                      ₱{Number(currentDestinationRate).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* Step 3: Calendar & Days */}
            <div>
              <h3 className="text-lg font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                <span>3. Book for Specific Days</span>
                <span className="text-xs font-semibold text-gray-400">Step 3 of 4</span>
              </h3>

              {/* Interactive Month Picker Calendar */}
              {activeVehicle && (
                <div className="p-6 bg-gray-50 dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-4 mb-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <h4 className="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>📅 Calendar</span>
                        <span className="text-xs font-semibold text-gray-500">(Hover red dates to see customer details & destination)</span>
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

                  {/* Grid of Days */}
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
                                  🔒 Fully Reserved Vehicle Date
                                </div>
                                <p className="text-xs font-bold text-white">
                                  Customer: <span className="text-emerald-400 font-extrabold">{day.bookingInfo.customer_name}</span>
                                </p>
                                <p className="text-xs text-slate-300">
                                  Destination: <span className="italic text-sky-300 font-medium">{day.bookingInfo.destination || 'Standard Car Rental'}</span>
                                </p>
                                <p className="text-[10px] text-slate-400 border-t border-slate-800 pt-1 mt-1">
                                  Dates: {day.bookingInfo.start_date} to {day.bookingInfo.end_date}
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
                              className={`w-full h-14 p-1 rounded-xl border flex flex-col justify-between items-center transition font-semibold ${
                                selected
                                  ? 'bg-emerald-600 text-white border-emerald-700 shadow-md scale-105'
                                  : 'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border-amber-300 dark:border-amber-700 hover:bg-amber-100'
                              }`}
                            >
                              <span className="text-xs font-black">{day.dayNum}</span>
                              <span className="text-[9px] font-bold px-1 rounded bg-amber-500 text-white">
                                {selected ? 'Selected' : 'Has Booking'}
                              </span>
                            </button>

                            {hoveredDay === day.dateStr && day.bookingInfo && (
                              <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white rounded-xl shadow-2xl z-50 text-left pointer-events-none border border-slate-700 space-y-1 animate-in fade-in duration-150">
                                <div className="text-[10px] font-bold text-amber-400 uppercase tracking-wider">
                                  ⏰ Partial Booking / Return Date
                                </div>
                                <p className="text-xs font-bold text-white">
                                  Customer: <span className="text-emerald-400 font-extrabold">{day.bookingInfo.customer_name}</span>
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
                            className={`w-full h-14 p-1 rounded-xl border flex flex-col justify-between items-center transition font-semibold ${
                              selected
                                ? 'bg-emerald-600 text-white border-emerald-700 shadow-md scale-105'
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

              {/* Rental Dates & Pickup / Return Timings Grid */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                      Pickup / Start Date *
                    </label>
                    <input
                      type="date"
                      value={data.start_date}
                      onChange={(e) => setData('start_date', e.target.value)}
                      min={new Date().toISOString().split('T')[0]}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                      Pickup Time *
                    </label>
                    <input
                      type="time"
                      value={data.pickup_time}
                      onChange={(e) => setData('pickup_time', e.target.value)}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                      required
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                      Return / End Date *
                    </label>
                    <input
                      type="date"
                      value={data.end_date}
                      onChange={(e) => setData('end_date', e.target.value)}
                      min={data.start_date}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                      Return Time *
                    </label>
                    <input
                      type="time"
                      value={data.return_time}
                      onChange={(e) => setData('return_time', e.target.value)}
                      className="w-full text-xs font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                      required
                    />
                  </div>
                </div>
              </div>

              {/* Conflict Warning Box */}
              {hasConflict && (
                <div className="mt-4 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 rounded-xl flex items-center space-x-3 text-rose-800 dark:text-rose-200 text-xs font-bold">
                  <AlertTriangle className="w-5 h-5 text-rose-600 flex-shrink-0" />
                  <div>
                    <span className="block uppercase font-black tracking-wider text-[10px] text-rose-600 dark:text-rose-400">
                      ⚠️ Schedule Conflict Detected (2-Hour Carwash Buffer)
                    </span>
                    <span>{conflictMessage}</span>
                  </div>
                </div>
              )}
            </div>

            {/* Live Cost Breakdown Card */}
            <div className="p-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl">
              <h4 className="text-xs font-extrabold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider mb-3">
                Live Cost Breakdown
              </h4>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div className="space-y-1">
                  <p className="text-xs font-semibold text-gray-500 uppercase">Destination Fee Subtotal</p>
                  <p className="text-xs font-bold text-gray-800 dark:text-gray-200">
                    {totalDays} Day(s) &times; ₱{Number(currentDestinationRate).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </p>
                  {excessHours > 0 && (
                    <p className="text-xs font-bold text-amber-600 dark:text-amber-400">
                      + {excessHours} Excess Hr(s) &times; ₱200.00 = +₱{Number(excessHours * 200).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                    </p>
                  )}
                  <p className="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    = ₱{Number((totalDays * currentDestinationRate) + (excessHours * 200)).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </p>
                  <p className="text-[10px] text-gray-400 truncate">
                    {activeDestination ? `${activeDestination.region} — ${activeDestination.province} — ${activeDestination.city}` : 'No Destination Selected'}
                  </p>
                </div>

                <div className="space-y-1 border-t md:border-t-0 md:border-l pt-2 md:pt-0 md:pl-4 border-emerald-200 dark:border-emerald-800">
                  <p className="text-xs text-gray-500 uppercase font-semibold">Less: Reservation Fee</p>
                  <p className="text-sm font-extrabold text-rose-600 dark:text-rose-400">
                    - ₱{Number(data.reservation_fee || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </p>
                  <p className="text-[10px] text-gray-400">Deducted from Total</p>
                </div>

                <div className="text-right border-t md:border-t-0 md:border-l pt-2 md:pt-0 md:pl-4 border-emerald-200 dark:border-emerald-800">
                  <span className="text-xs text-gray-500 uppercase font-semibold">Total Rental Price</span>
                  <div className="text-3xl font-black text-emerald-600 dark:text-emerald-400">
                    ₱{Number(totalPrice).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            </div>

            {/* Step 4: Customer Details & Requirements */}
            <div>
              <h3 className="text-lg font-extrabold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                <span>4. Customer Details & Rental Requirements</span>
                <span className="text-xs font-semibold text-gray-400">Step 4 of 4</span>
              </h3>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Full Name */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Full Name *</label>
                  <input
                    type="text"
                    value={data.customer_name}
                    onChange={(e) => setData('customer_name', e.target.value)}
                    required
                    placeholder="e.g. Juan Dela Cruz"
                    className="w-full text-sm font-semibold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                  />
                </div>

                {/* Mobile Number */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Mobile / Contact Number *</label>
                  <input
                    type="text"
                    value={data.customer_phone}
                    onChange={(e) => setData('customer_phone', e.target.value)}
                    required
                    placeholder="e.g. +63 917 123 4567"
                    className="w-full text-sm font-semibold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                  />
                </div>

                {/* Pickup Location */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Pickup Location *</label>
                  <input
                    type="text"
                    value={data.pickup_location}
                    onChange={(e) => setData('pickup_location', e.target.value)}
                    required
                    placeholder="e.g. Bancasi Airport (BXU) / Hotel Lobby / City Center"
                    className="w-full text-sm font-semibold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
                  />
                </div>

                {/* Reservation Fee */}
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Reservation Fee (₱)</label>
                  <div className="relative">
                    <span className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-black text-gray-400">₱</span>
                    <input
                      type="number"
                      step="0.01"
                      min="0"
                      value={data.reservation_fee}
                      onChange={(e) => setData('reservation_fee', e.target.value)}
                      placeholder="0.00"
                      className="w-full text-sm font-extrabold text-emerald-600 dark:text-emerald-400 border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 pl-7 p-2.5"
                    />
                  </div>
                </div>

                {/* Driver's License Upload */}
                <div className="md:col-span-2">
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                    Driver's License Picture of Renter
                  </label>
                  <input
                    type="file"
                    accept="image/*"
                    onChange={handleLicenseChange}
                    className="block w-full text-xs text-gray-900 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl cursor-pointer p-2 focus:outline-none"
                  />
                  {licensePreview && (
                    <div className="mt-2 w-44 h-28 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                      <img src={licensePreview} alt="License Preview" className="w-full h-full object-cover" />
                    </div>
                  )}
                </div>
              </div>

              {/* Special Instructions / Notes */}
              <div className="mt-4">
                <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                  Special Instructions / Pickup Location Details
                </label>
                <textarea
                  value={data.notes}
                  onChange={(e) => setData('notes', e.target.value)}
                  rows={2}
                  placeholder="e.g. Pickup at Bancasi Airport (BXU) at 9:00 AM, child seat requested..."
                  className="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3"
                />
              </div>
            </div>

            {/* Form Actions */}
            <div className="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
              <Link
                href="/bookings"
                className="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm rounded-xl transition"
              >
                Cancel
              </Link>
              <button
                type="submit"
                disabled={processing || !data.vehicle_id || !data.destination_id || totalDays <= 0 || hasConflict}
                className="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-50 text-white font-extrabold text-sm rounded-xl shadow-lg transition"
              >
                {processing ? 'Processing...' : 'Confirm & Reserve Booking'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}

