import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Car,
  CheckCircle2,
  Clock,
  Plus,
  ArrowRight,
  TrendingUp,
  Calendar,
  Eye,
  MapPin,
  Phone,
  BarChart2,
  Layers,
  Sparkles
} from 'lucide-react';

export default function Dashboard({
  totalVehicles,
  availableVehicles,
  activeBookings,
  totalRevenue,
  recentBookings = [],
  vehicles = [],
  myVehicles = [],
  monthlyBookings = []
}) {
  const [loadingToggleId, setLoadingToggleId] = useState(null);
  const [hoveredDataIndex, setHoveredDataIndex] = useState(null);

  const handleToggleAvailability = (vehicleId, currentStatus) => {
    setLoadingToggleId(vehicleId);
    router.patch(
      `/vehicles/${vehicleId}/toggle-availability`,
      {},
      {
        preserveScroll: true,
        onFinish: () => setLoadingToggleId(null),
      }
    );
  };

  const getStatusBadge = (status) => {
    switch (status) {
      case 'confirmed':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Confirmed</span>;
      case 'pending':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Pending</span>;
      case 'completed':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Completed</span>;
      case 'cancelled':
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Cancelled</span>;
      default:
        return <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">{status}</span>;
    }
  };

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr.includes('T') ? dateStr : `${dateStr}T00:00:00`);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  const formatTime = (timeStr) => {
    if (!timeStr) return '';
    if (/am|pm/i.test(timeStr)) return timeStr;
    const parts = timeStr.split(':');
    if (parts.length >= 2) {
      const h = parseInt(parts[0], 10);
      const m = parts[1].padStart(2, '0');
      const ampm = h >= 12 ? 'PM' : 'AM';
      const formattedHour = h % 12 === 0 ? 12 : h % 12;
      return `${formattedHour}:${m} ${ampm}`;
    }
    return timeStr;
  };

  // --- Monthly Bookings Chart Calculations ---
  const chartData = (monthlyBookings && monthlyBookings.length > 0)
    ? monthlyBookings
    : [
      { month: 'Jan', label: 'Jan', count: 0, revenue: 0 },
      { month: 'Feb', label: 'Feb', count: 0, revenue: 0 },
      { month: 'Mar', label: 'Mar', count: 0, revenue: 0 },
      { month: 'Apr', label: 'Apr', count: 0, revenue: 0 },
      { month: 'May', label: 'May', count: 0, revenue: 0 },
      { month: 'Jun', label: 'Jun', count: 0, revenue: 0 },
      { month: 'Jul', label: 'Jul', count: 0, revenue: 0 },
      { month: 'Aug', label: 'Aug', count: 0, revenue: 0 },
      { month: 'Sep', label: 'Sep', count: 0, revenue: 0 },
      { month: 'Oct', label: 'Oct', count: 0, revenue: 0 },
      { month: 'Nov', label: 'Nov', count: 0, revenue: 0 },
      { month: 'Dec', label: 'Dec', count: 0, revenue: 0 },
    ];

  const totalPeriodBookings = chartData.reduce((acc, curr) => acc + (curr.count || 0), 0);
  const totalPeriodRevenue = chartData.reduce((acc, curr) => acc + (curr.revenue || 0), 0);
  const maxBookingCount = Math.max(...chartData.map(d => d.count || 0), 5);

  const svgWidth = 460;
  const svgHeight = 210;
  const padding = { top: 25, bottom: 35, left: 35, right: 20 };
  const graphWidth = svgWidth - padding.left - padding.right;
  const graphHeight = svgHeight - padding.top - padding.bottom;

  const points = chartData.map((d, index) => {
    const x = padding.left + (index / (chartData.length - 1 || 1)) * graphWidth;
    const y = padding.top + graphHeight - ((d.count || 0) / maxBookingCount) * graphHeight;
    return { ...d, x, y, index };
  });

  // Calculate smooth cubic bezier path
  const getSmoothPath = (pts) => {
    if (!pts.length) return '';
    if (pts.length === 1) return `M ${pts[0].x} ${pts[0].y}`;
    let path = `M ${pts[0].x} ${pts[0].y}`;
    for (let i = 0; i < pts.length - 1; i++) {
      const p0 = pts[i === 0 ? 0 : i - 1];
      const p1 = pts[i];
      const p2 = pts[i + 1];
      const p3 = pts[i + 2] || p2;

      const cp1x = p1.x + (p2.x - p0.x) / 6;
      const cp1y = p1.y + (p2.y - p0.y) / 6;
      const cp2x = p2.x - (p3.x - p1.x) / 6;
      const cp2y = p2.y - (p3.y - p1.y) / 6;

      path += ` C ${cp1x.toFixed(1)} ${cp1y.toFixed(1)}, ${cp2x.toFixed(1)} ${cp2y.toFixed(1)}, ${p2.x.toFixed(1)} ${p2.y.toFixed(1)}`;
    }
    return path;
  };

  const linePath = getSmoothPath(points);
  const lastPoint = points[points.length - 1];
  const firstPoint = points[0];
  const areaPath = points.length > 0
    ? `${linePath} L ${lastPoint.x} ${padding.top + graphHeight} L ${firstPoint.x} ${padding.top + graphHeight} Z`
    : '';

  const hoveredPoint = hoveredDataIndex !== null ? points[hoveredDataIndex] : null;

  return (
    <AppLayout title="Dashboard">
      <Head title="Dashboard" />

      <div className="space-y-6 pb-6">
        {/* Welcome Banner */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-6 rounded-3xl shadow-xs transition-colors duration-300">
          <div className="space-y-1">
            <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
              Rental Dashboard
            </h1>
            <p className="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
              Overview of your fleet performance, bookings, and revenue
            </p>
          </div>

          <div className="flex items-center gap-3">
            <Link
              href="/bookings/create"
              className="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-95 transition-transform"
            >
              <Plus className="w-4 h-4" />
              New Booking
            </Link>
            <Link
              href="/vehicles/create"
              className="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-transform"
            >
              <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Add Vehicle
            </Link>
          </div>
        </div>

        {/* Top Metric Cards */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available Fleet</span>
              <div className="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                <CheckCircle2 className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{availableVehicles}</span>
              <span className="text-xs text-gray-500 font-semibold">of {totalVehicles} total</span>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Bookings</span>
              <div className="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200 dark:border-indigo-800">
                <Clock className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">{activeBookings}</span>
              <span className="text-xs text-indigo-600 dark:text-indigo-400 font-semibold">Active Trips</span>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Revenue</span>
              <div className="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-200 dark:border-amber-800">
                <TrendingUp className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">
                ₱{Number(totalRevenue || 0).toLocaleString()}
              </span>
            </div>
          </div>

          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs transition-colors">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Fleet</span>
              <div className="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-950/70 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-200 dark:border-violet-800">
                <Car className="w-5 h-5" />
              </div>
            </div>
            <div className="mt-3 flex items-baseline justify-between">
              <span className="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{totalVehicles}</span>
              <Link href="/vehicles" className="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-bold flex items-center gap-1">
                View fleet <ArrowRight className="w-3 h-3" />
              </Link>
            </div>
          </div>
        </div>

        {/* 3 Columns Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
          
          {/* COLUMN 1: Line Graph of Bookings Per Month */}
          <div className="space-y-4">
            <div className="flex items-center justify-between px-1">
              <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <BarChart2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                Bookings per Month
              </h2>
              <span className="text-[11px] font-bold text-gray-400 dark:text-gray-500">
                12-Month Trend
              </span>
            </div>

            <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs space-y-4 transition-colors">
              {/* Stat Summary Row */}
              <div className="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-gray-950/60 p-3 rounded-2xl border border-gray-100 dark:border-gray-800">
                <div>
                  <span className="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Trips</span>
                  <p className="text-lg font-black text-gray-900 dark:text-white">
                    {totalPeriodBookings} <span className="text-xs font-medium text-emerald-600 dark:text-emerald-400">Bookings</span>
                  </p>
                </div>
                <div className="text-right">
                  <span className="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Period Volume</span>
                  <p className="text-lg font-black text-amber-600 dark:text-amber-400">
                    ₱{totalPeriodRevenue.toLocaleString()}
                  </p>
                </div>
              </div>

              {/* Interactive SVG Line Graph Container */}
              <div className="relative pt-2">
                <svg
                  viewBox={`0 0 ${svgWidth} ${svgHeight}`}
                  className="w-full h-auto overflow-visible select-none"
                >
                  <defs>
                    {/* Gradient for area under line */}
                    <linearGradient id="bookingAreaGradient" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stopColor="#10B981" stopOpacity="0.35" />
                      <stop offset="70%" stopColor="#10B981" stopOpacity="0.08" />
                      <stop offset="100%" stopColor="#10B981" stopOpacity="0.0" />
                    </linearGradient>

                    {/* Gradient for the line stroke */}
                    <linearGradient id="bookingLineGradient" x1="0" y1="0" x2="1" y2="0">
                      <stop offset="0%" stopColor="#059669" />
                      <stop offset="50%" stopColor="#10B981" />
                      <stop offset="100%" stopColor="#34D399" />
                    </linearGradient>
                  </defs>

                  {/* Horizontal Grid lines */}
                  {[0, 0.5, 1].map((ratio, i) => {
                    const y = padding.top + graphHeight * (1 - ratio);
                    const val = Math.round(maxBookingCount * ratio);
                    return (
                      <g key={i} className="text-gray-300 dark:text-gray-700">
                        <line
                          x1={padding.left}
                          y1={y}
                          x2={svgWidth - padding.right}
                          y2={y}
                          stroke="currentColor"
                          strokeDasharray="4 4"
                          strokeWidth="1"
                          strokeOpacity="0.6"
                        />
                        <text
                          x={padding.left - 6}
                          y={y + 3}
                          textAnchor="end"
                          className="text-[9px] fill-gray-400 dark:fill-gray-500 font-mono font-bold"
                        >
                          {val}
                        </text>
                      </g>
                    );
                  })}

                  {/* Area Fill */}
                  {areaPath && (
                    <path
                      d={areaPath}
                      fill="url(#bookingAreaGradient)"
                    />
                  )}

                  {/* Spline Stroke Line */}
                  {linePath && (
                    <path
                      d={linePath}
                      fill="none"
                      stroke="url(#bookingLineGradient)"
                      strokeWidth="3.5"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                  )}

                  {/* Interactive Crosshair & Points */}
                  {points.map((pt, idx) => {
                    const isHovered = hoveredDataIndex === idx;
                    return (
                      <g key={idx}>
                        {/* Hover vertical line */}
                        {isHovered && (
                          <line
                            x1={pt.x}
                            y1={padding.top}
                            x2={pt.x}
                            y2={padding.top + graphHeight}
                            stroke="#10B981"
                            strokeWidth="1.5"
                            strokeDasharray="3 3"
                            strokeOpacity="0.8"
                          />
                        )}

                        {/* Interactive invisible hit target */}
                        <circle
                          cx={pt.x}
                          cy={pt.y}
                          r="14"
                          fill="transparent"
                          className="cursor-pointer"
                          onMouseEnter={() => setHoveredDataIndex(idx)}
                          onMouseLeave={() => setHoveredDataIndex(null)}
                        />

                        {/* Visible Data Dot */}
                        <circle
                          cx={pt.x}
                          cy={pt.y}
                          r={isHovered ? 5.5 : (pt.count > 0 ? 3.5 : 2.5)}
                          fill={isHovered ? '#10B981' : (pt.count > 0 ? '#10B981' : '#9CA3AF')}
                          stroke="#ffffff"
                          strokeWidth={isHovered ? 2.5 : 1.5}
                          className="transition-all duration-150 pointer-events-none"
                        />

                        {/* X-Axis Month Label */}
                        <text
                          x={pt.x}
                          y={padding.top + graphHeight + 16}
                          textAnchor="middle"
                          className={`text-[9px] font-bold select-none ${isHovered
                              ? 'fill-emerald-600 dark:fill-emerald-400 font-extrabold'
                              : 'fill-gray-400 dark:fill-gray-500'
                            }`}
                        >
                          {pt.month}
                        </text>
                      </g>
                    );
                  })}
                </svg>

                {/* Floating Interactive Tooltip */}
                {hoveredPoint && (
                  <div
                    className="absolute z-20 pointer-events-none -translate-x-1/2 bg-gray-900 text-white dark:bg-white dark:text-gray-900 text-[11px] px-3 py-2 rounded-xl shadow-xl border border-gray-700 dark:border-gray-200 transition-all duration-150"
                    style={{
                      left: `${(hoveredPoint.x / svgWidth) * 100}%`,
                      top: `${Math.max(0, (hoveredPoint.y / svgHeight) * 100 - 32)}%`,
                    }}
                  >
                    <p className="font-black border-b border-gray-700/50 dark:border-gray-200/50 pb-0.5 text-emerald-400 dark:text-emerald-600">
                      {hoveredPoint.label || hoveredPoint.month}
                    </p>
                    <p className="mt-1 font-bold">
                      {hoveredPoint.count} {hoveredPoint.count === 1 ? 'booking' : 'bookings'}
                    </p>
                    {hoveredPoint.revenue > 0 && (
                      <p className="font-semibold text-[10px] text-amber-300 dark:text-amber-600">
                        ₱{Number(hoveredPoint.revenue).toLocaleString()}
                      </p>
                    )}
                  </div>
                )}
              </div>

              {/* Chart Legend / Helper */}
              <div className="pt-2 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                <span className="flex items-center gap-1.5">
                  <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block" />
                  Monthly Reservations
                </span>
                <span className="font-medium text-emerald-600 dark:text-emerald-400">
                  Hover points for details
                </span>
              </div>
            </div>
          </div>

          {/* COLUMN 2: My Fleet Availability */}
          <div className="space-y-4">
            <div className="flex items-center justify-between px-1">
              <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Car className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                My Fleet Availability
              </h2>
              <Link href="/vehicles" className="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                Manage
              </Link>
            </div>

            <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 shadow-xs space-y-3 transition-colors">
              {myVehicles.length === 0 ? (
                <div className="p-6 text-center text-xs text-gray-500 dark:text-gray-400 space-y-2">
                  <p>No vehicles added yet.</p>
                  <Link href="/vehicles/create" className="inline-block px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold">
                    Add Vehicle
                  </Link>
                </div>
              ) : (
                myVehicles.slice(0, 6).map((v) => {
                  const isAvailable = v.status === 'available';
                  const isLoading = loadingToggleId === v.id;
                  return (
                    <div
                      key={v.id}
                      className="p-3 bg-gray-50 dark:bg-gray-950/60 rounded-2xl border border-gray-200/80 dark:border-gray-800 flex items-center justify-between gap-3 hover:border-emerald-500/30 transition-colors"
                    >
                      <div className="flex items-center gap-3 min-w-0">
                        <div className="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-800 overflow-hidden shrink-0 border border-gray-300 dark:border-gray-700">
                          {v.image_path ? (
                            <img src={`/${v.image_path}`} alt={v.name} className="w-full h-full object-cover" />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center text-gray-500">
                              <Car className="w-5 h-5" />
                            </div>
                          )}
                        </div>
                        <div className="min-w-0">
                          <p className="text-xs font-bold text-gray-900 dark:text-white truncate">{v.name}</p>
                          <p className="text-[10px] text-gray-500 dark:text-gray-400 font-mono">{v.license_plate}</p>
                        </div>
                      </div>

                      {/* Interactive Toggle Switch */}
                      <button
                        onClick={() => handleToggleAvailability(v.id, v.status)}
                        disabled={isLoading}
                        className={`relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${isAvailable ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-700'
                          }`}
                        title={isAvailable ? 'Status: Available (Click to change)' : 'Status: Out of Service (Click to change)'}
                      >
                        <span
                          className={`pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${isAvailable ? 'translate-x-5' : 'translate-x-0'
                            }`}
                        />
                      </button>
                    </div>
                  );
                })
              )}
            </div>
          </div>

          {/* COLUMN 3: Recent Reservations (Individual Card UI) */}
          <div className="space-y-4">
            <div className="flex items-center justify-between px-1">
              <h2 className="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Calendar className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                Recent Reservations
              </h2>
              <Link href="/bookings" className="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                View all <ArrowRight className="w-3 h-3" />
              </Link>
            </div>

            {recentBookings.length === 0 ? (
              <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-8 text-center space-y-3 shadow-xs">
                <Calendar className="w-10 h-10 text-gray-400 dark:text-gray-600 mx-auto" />
                <p className="text-xs text-gray-500 dark:text-gray-400">No recent bookings recorded yet.</p>
                <Link
                  href="/bookings/create"
                  className="inline-flex px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors"
                >
                  Create First Booking
                </Link>
              </div>
            ) : (
              <div className="space-y-3.5">
                {recentBookings.map((b) => (
                  <div
                    key={b.id}
                    className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-4 sm:p-4.5 shadow-xs hover:shadow-md hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition-all duration-200 space-y-3.5"
                  >
                    {/* Card Header: Code & Plate (Left), Status (Right) */}
                    <div className="flex items-center justify-between gap-2">
                      <div className="flex flex-wrap items-center gap-1.5 min-w-0">
                        <span className="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-300">
                          #{b.booking_code}
                        </span>
                        {b.vehicle?.license_plate && (
                          <span className="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/70 text-amber-900 dark:text-amber-200 font-mono text-[11px] font-black border border-amber-200 dark:border-amber-800">
                            🚘 {b.vehicle.license_plate}
                          </span>
                        )}
                      </div>
                      <div className="shrink-0">
                        {getStatusBadge(b.status)}
                      </div>
                    </div>

                    {/* Customer & Vehicle Info */}
                    <div className="space-y-1 min-w-0">
                      <p className="text-sm font-extrabold text-gray-900 dark:text-white truncate">
                        {b.customer_name}
                      </p>
                      {b.customer_phone && (
                        <p className="text-xs font-bold text-sky-600 dark:text-sky-400 flex items-center gap-1 min-w-0">
                          <Phone className="w-3.5 h-3.5 shrink-0" />
                          <span className="truncate">{b.customer_phone}</span>
                        </p>
                      )}
                      {b.vehicle && (
                        <p className="text-xs font-medium text-gray-700 dark:text-gray-300 truncate flex items-center gap-1.5">
                          <Car className="w-3.5 h-3.5 text-gray-400 shrink-0" />
                          <span className="font-bold">{b.vehicle.name || `${b.vehicle.make} ${b.vehicle.model}`}</span>
                        </p>
                      )}
                      {b.destination && (
                        <p className="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 min-w-0">
                          <MapPin className="w-3.5 h-3.5 text-gray-400 shrink-0" />
                          <span className="truncate">{b.destination}</span>
                        </p>
                      )}
                    </div>

                    {/* Schedule Block */}
                    <div className="bg-gray-50 dark:bg-gray-950/60 rounded-2xl p-2.5 border border-gray-100 dark:border-gray-800 text-[11px] space-y-1">
                      <div className="flex items-center justify-between gap-1">
                        <span className="text-gray-500 dark:text-gray-400 font-medium">Pickup:</span>
                        <span className="font-bold text-gray-900 dark:text-white">
                          {formatDate(b.start_date)}{' '}
                          <span className="text-emerald-600 dark:text-emerald-400 font-bold">
                            ({formatTime(b.pickup_time || '00:00')})
                          </span>
                        </span>
                      </div>
                      <div className="flex items-center justify-between gap-1">
                        <span className="text-gray-500 dark:text-gray-400 font-medium">Return:</span>
                        <span className="font-bold text-gray-900 dark:text-white">
                          {formatDate(b.end_date)}{' '}
                          <span className="text-rose-600 dark:text-rose-400 font-bold">
                            ({formatTime(b.return_time || '00:00')})
                          </span>
                        </span>
                      </div>
                    </div>

                    {/* Card Footer: Duration, Price & Action */}
                    <div className="flex items-center justify-between gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                      <div className="flex items-center gap-2">
                        {b.total_days && (
                          <span className="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-800 dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                            {b.total_days} {b.total_days === 1 ? 'Day' : 'Days'}
                          </span>
                        )}
                        <span className="text-sm sm:text-base font-black text-amber-600 dark:text-amber-400">
                          ₱{Number(b.total_price || 0).toLocaleString()}
                        </span>
                      </div>
                      <Link
                        href={`/bookings/${b.id}`}
                        className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold transition-colors shadow-2xs"
                      >
                        Details <Eye className="w-3.5 h-3.5" />
                      </Link>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

        </div>
      </div>
    </AppLayout>
  );
}
