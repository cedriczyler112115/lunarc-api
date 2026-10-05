import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { MapPin, ArrowLeft } from 'lucide-react';

export default function Edit({ destination, vehicleTypes = [] }) {
  const existingRates = {};
  (destination.vehicle_rates || []).forEach((r) => {
    existingRates[r.vehicle_type_id] = r.destination_rate;
  });

  const { data, setData, put, processing, errors } = useForm({
    region: destination.region || '',
    province: destination.province || '',
    city: destination.city || '',
    destination_rate: destination.destination_rate || '',
    description: destination.description || '',
    rates: existingRates,
  });

  const handleRateChange = (vtId, val) => {
    setData('rates', {
      ...data.rates,
      [vtId]: val,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    put(`/destinations/${destination.id}`);
  };

  return (
    <AppLayout title={`Edit Destination ${destination.city}`}>
      <Head title={`Edit Destination ${destination.city}`} />

      <div className="max-w-2xl mx-auto space-y-6 pb-6">
        <Link
          href="/destinations"
          className="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white"
        >
          <ArrowLeft className="w-4 h-4" />
          Back to Destinations
        </Link>

        <div className="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
          <h1 className="text-xl font-black text-white">Edit Destination Details</h1>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">Region *</label>
                <input
                  type="text"
                  value={data.region}
                  onChange={(e) => setData('region', e.target.value)}
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">Province *</label>
                <input
                  type="text"
                  value={data.province}
                  onChange={(e) => setData('province', e.target.value)}
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">City / Municipality *</label>
                <input
                  type="text"
                  value={data.city}
                  onChange={(e) => setData('city', e.target.value)}
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">Base Destination Rate (₱) *</label>
                <input
                  type="number"
                  step="0.01"
                  value={data.destination_rate}
                  onChange={(e) => setData('destination_rate', e.target.value)}
                  className="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs font-bold text-amber-400"
                />
              </div>
            </div>

            <div className="space-y-2 pt-2 border-t border-slate-800">
              <label className="block text-xs font-bold text-slate-300">Custom Vehicle Category Rates (₱)</label>
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                {vehicleTypes.map((vt) => (
                  <div key={vt.id} className="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 space-y-1">
                    <span className="text-[10px] font-bold text-slate-400 block truncate">{vt.name}</span>
                    <input
                      type="number"
                      step="0.01"
                      value={data.rates[vt.id] !== undefined ? data.rates[vt.id] : ''}
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
                Update Destination
              </button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
