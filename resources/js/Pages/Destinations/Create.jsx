import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { MapPin, ArrowLeft, Plus } from 'lucide-react';

export default function Create({ regions = [], regionProvincesMap = {}, vehicleTypes = [] }) {
  const { data, setData, post, processing, errors } = useForm({
    region: '',
    province: '',
    city: '',
    destination_rate: '',
    description: '',
    rates: {},
  });

  const handleRateChange = (vtId, val) => {
    setData('rates', {
      ...data.rates,
      [vtId]: val,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/destinations');
  };

  return (
    <AppLayout title="Add Destination">
      <Head title="Add Destination" />

      <div className="max-w-2xl mx-auto space-y-6 pb-6">
        <Link
          href="/destinations"
          className="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white"
        >
          <ArrowLeft className="w-4 h-4" />
          Back to Destinations
        </Link>

        <div className="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
          <div className="space-y-1">
            <h1 className="text-xl font-black text-white flex items-center gap-2">
              <MapPin className="w-6 h-6 text-rose-400" />
              Add New Destination Location
            </h1>
            <p className="text-xs text-slate-400">Define region, province, city and vehicle type rates</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">Region *</label>
                <input
                  type="text"
                  value={data.region}
                  onChange={(e) => setData('region', e.target.value)}
                  placeholder="e.g. Region III"
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
                />
                {errors.region && <p className="text-xs text-rose-400 mt-1">{errors.region}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">Province *</label>
                <input
                  type="text"
                  value={data.province}
                  onChange={(e) => setData('province', e.target.value)}
                  placeholder="e.g. Pampanga"
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
                />
                {errors.province && <p className="text-xs text-rose-400 mt-1">{errors.province}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">City / Municipality *</label>
                <input
                  type="text"
                  value={data.city}
                  onChange={(e) => setData('city', e.target.value)}
                  placeholder="e.g. Angeles City"
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
                />
                {errors.city && <p className="text-xs text-rose-400 mt-1">{errors.city}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">Base Destination Rate (₱) *</label>
                <input
                  type="number"
                  step="0.01"
                  value={data.destination_rate}
                  onChange={(e) => setData('destination_rate', e.target.value)}
                  placeholder="e.g. 1500"
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs font-bold text-amber-400"
                />
                {errors.destination_rate && <p className="text-xs text-rose-400 mt-1">{errors.destination_rate}</p>}
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-300 mb-1">Description / Notes</label>
              <textarea
                value={data.description}
                onChange={(e) => setData('description', e.target.value)}
                rows="2"
                placeholder="Optional notes regarding toll fees or special route restrictions"
                className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
              />
            </div>

            {/* Custom Vehicle Type Rates */}
            <div className="space-y-2 pt-2 border-t border-slate-800">
              <label className="block text-xs font-bold text-slate-300">Custom Vehicle Type Rates (Optional Override)</label>
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                {vehicleTypes.map((vt) => (
                  <div key={vt.id} className="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 space-y-1">
                    <span className="text-[10px] font-bold text-slate-400 block truncate">{vt.name}</span>
                    <input
                      type="number"
                      step="0.01"
                      value={data.rates[vt.id] || ''}
                      onChange={(e) => handleRateChange(vt.id, e.target.value)}
                      placeholder="Rate (₱)"
                      className="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-lg text-xs font-bold text-amber-400"
                    />
                  </div>
                ))}
              </div>
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
              <Link
                href="/destinations"
                className="px-5 py-2.5 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold"
              >
                Cancel
              </Link>
              <button
                type="submit"
                disabled={processing}
                className="px-6 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg"
              >
                Save Destination
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
