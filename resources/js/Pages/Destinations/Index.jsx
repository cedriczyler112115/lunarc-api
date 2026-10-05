import React, { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  MapPin,
  Plus,
  Search,
  Edit,
  Trash2,
  Save,
  CheckCircle2,
  Car,
  X,
  Sparkles,
  Loader2
} from 'lucide-react';

export default function Index({
  destinations,
  regions = [],
  regionProvincesMap = {},
  vehicleTypes = [],
  filters = {}
}) {
  const [search, setSearch] = useState(filters.search || '');
  const [selectedRegion, setSelectedRegion] = useState(filters.region || '');
  const [selectedProvince, setSelectedProvince] = useState(filters.province || '');

  const [rateInputs, setRateInputs] = useState({});
  const [savingDestId, setSavingDestId] = useState(null);
  const [showAddTypeModal, setShowAddTypeModal] = useState(false);
  const [toastMessage, setToastMessage] = useState(null);

  const showToast = (msg) => {
    setToastMessage(msg);
    setTimeout(() => {
      setToastMessage(null);
    }, 3000);
  };

  const { data: newTypeData, setData: setNewTypeData, post: postNewType, reset: resetNewType, processing: addingType } = useForm({
    name: '',
    description: '',
  });

  const destinationItems = destinations?.data || [];
  const availableProvinces = selectedRegion && regionProvincesMap[selectedRegion]
    ? regionProvincesMap[selectedRegion]
    : Array.from(new Set(Object.values(regionProvincesMap).flat())).sort();

  const handleFilter = (updates = {}) => {
    const nextFilters = {
      search,
      region: selectedRegion,
      province: selectedProvince,
      ...updates,
    };

    const query = {};
    if (nextFilters.search) query.search = nextFilters.search;
    if (nextFilters.region) query.region = nextFilters.region;
    if (nextFilters.province) query.province = nextFilters.province;

    router.get('/destinations', query, { preserveState: true, replace: true });
  };

  const handleRateInputChange = (destId, vehicleTypeId, val) => {
    setRateInputs((prev) => ({
      ...prev,
      [`${destId}_${vehicleTypeId}`]: val,
    }));
  };

  const handleSaveInlineRates = (dest) => {
    setSavingDestId(dest.id);

    const ratesMap = {};
    (dest.vehicle_rates || []).forEach((r) => {
      ratesMap[r.vehicle_type_id] = r.destination_rate;
    });

    const ratesPayload = {};
    vehicleTypes.forEach((vt) => {
      const key = `${dest.id}_${vt.id}`;
      if (key in rateInputs) {
        ratesPayload[vt.id] = rateInputs[key];
      } else if (ratesMap[vt.id] !== undefined) {
        ratesPayload[vt.id] = ratesMap[vt.id];
      }
    });

    router.patch(
      `/destinations/${dest.id}/rates`,
      {
        rates: ratesPayload,
      },
      {
        preserveScroll: true,
        onSuccess: () => {
          showToast(`Rental rates for ${dest.city} updated successfully!`);
        },
        onFinish: () => setSavingDestId(null),
      }
    );
  };

  const handleDelete = (id, name) => {
    if (confirm(`Are you sure you want to delete destination ${name}?`)) {
      router.delete(`/destinations/${id}`);
    }
  };

  const handleDeleteVehicleType = (id, name) => {
    if (confirm(`Delete vehicle type ${name}?`)) {
      router.delete(`/vehicle-types/${id}`, { preserveScroll: true });
    }
  };

  const handleAddVehicleType = (e) => {
    e.preventDefault();
    postNewType('/vehicle-types', {
      preserveScroll: true,
      onSuccess: () => {
        resetNewType();
        setShowAddTypeModal(false);
      },
    });
  };

  return (
    <AppLayout title="Destinations & Vehicle Type Rental Pricing">
      <Head title="Destinations & Vehicle Type Pricing" />

      {/* Floating Toast Notification */}
      {toastMessage && (
        <div className="fixed top-5 right-5 z-[300] flex items-center gap-2.5 bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-xl border border-emerald-500 font-bold text-sm transition-all animate-bounce">
          <CheckCircle2 className="w-5 h-5 text-white" />
          <span>{toastMessage}</span>
        </div>
      )}

      <div className="space-y-6 pb-6">
        {/* Header */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-700 transition-colors">
          <div>
            <h2 className="font-extrabold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
              <MapPin className="w-7 h-7 text-sky-600 dark:text-sky-400" />
              Destinations & Vehicle Type Rental Pricing
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Configure destination rates per vehicle type (Sedan, SUV, MPV, Van, Pickup, and custom vehicle types).
            </p>
          </div>

          <div>
            <Link
              href="/destinations/create"
              className="inline-flex items-center px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl shadow-md transition duration-150"
            >
              <Plus className="w-5 h-5 mr-1.5" />
              + Add Destination
            </Link>
          </div>
        </div>

        {/* Configured Vehicle Types Banner */}
        <div className="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 space-y-3">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
              <Car className="w-4 h-4 text-sky-600" />
              Configured Vehicle Types (Dynamic Rates Matrix)
            </h3>
            <span className="text-xs text-gray-400 font-semibold">Total: {vehicleTypes.length} Vehicle Types</span>
          </div>

          <div className="flex flex-wrap items-center gap-2">
            {vehicleTypes.map((vt) => {
              const isStandard = ['Sedan', 'SUV', 'MPV', 'Van', 'Pickup'].includes(vt.name);
              return (
                <div
                  key={vt.id}
                  className="inline-flex items-center space-x-2 px-3 py-1.5 bg-sky-50 dark:bg-sky-950/50 border border-sky-200 dark:border-sky-800 rounded-xl text-xs font-bold text-sky-900 dark:text-sky-200"
                >
                  <span>🚗 {vt.name}</span>
                  {!isStandard && (
                    <button
                      type="button"
                      onClick={() => handleDeleteVehicleType(vt.id, vt.name)}
                      className="text-rose-500 hover:text-rose-700 ml-1 font-extrabold"
                      title="Remove custom type"
                    >
                      &times;
                    </button>
                  )}
                </div>
              );
            })}
            <button
              type="button"
              onClick={() => setShowAddTypeModal(true)}
              className="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition text-indigo-600 dark:text-indigo-400"
            >
              + Add Custom Vehicle Type
            </button>
          </div>
        </div>

        {/* Search & Cascading Filter Bar */}
        <div className="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700">
          <form
            onSubmit={(e) => {
              e.preventDefault();
              handleFilter();
            }}
            className="grid grid-cols-1 md:grid-cols-4 gap-4"
          >
            <div>
              <label className="block text-xs font-semibold text-gray-500 uppercase mb-1">Search City / Province / Region</label>
              <input
                type="text"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="City, Province or Region..."
                className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Region</label>
              <select
                value={selectedRegion}
                onChange={(e) => {
                  setSelectedRegion(e.target.value);
                  setSelectedProvince('');
                }}
                className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold"
              >
                <option value="">All Regions</option>
                {regions.map((reg) => (
                  <option key={reg} value={reg}>
                    {reg}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Province</label>
              <select
                value={selectedProvince}
                onChange={(e) => setSelectedProvince(e.target.value)}
                className="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold"
              >
                <option value="">All Provinces</option>
                {availableProvinces.map((prov) => (
                  <option key={prov} value={prov}>
                    {prov}
                  </option>
                ))}
              </select>
            </div>

            <div className="flex items-end space-x-2">
              <button
                type="submit"
                className="w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl shadow-xs transition"
              >
                Apply Filters
              </button>
              {(search || selectedRegion || selectedProvince) && (
                <button
                  type="button"
                  onClick={() => {
                    setSearch('');
                    setSelectedRegion('');
                    setSelectedProvince('');
                    router.get('/destinations', {}, { preserveState: true, replace: true });
                  }}
                  className="py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-bold rounded-xl transition"
                >
                  Clear
                </button>
              )}
            </div>
          </form>
        </div>

        {/* Dynamic Destinations Matrix Table */}
        <div className="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm text-gray-600 dark:text-gray-300 border-collapse">
              <thead className="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                <tr>
                  <th className="px-4 py-4">Region</th>
                  <th className="px-4 py-4">Province</th>
                  <th className="px-4 py-4">City / Municipality</th>

                  {vehicleTypes.map((vt) => (
                    <th
                      key={vt.id}
                      className="px-3 py-4 text-center bg-sky-50/60 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-r border-sky-100 dark:border-sky-900/50 min-w-[120px]"
                    >
                      {vt.name}
                      <span className="block text-[10px] font-semibold text-gray-400 lowercase">(rate ₱)</span>
                    </th>
                  ))}

                  <th className="px-4 py-4 text-right min-w-[80px]">Save</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                {destinationItems.length === 0 ? (
                  <tr>
                    <td colSpan={4 + vehicleTypes.length} className="px-6 py-12 text-center text-gray-400">
                      No destinations found matching your filters.
                    </td>
                  </tr>
                ) : (
                  destinationItems.map((dest) => {
                    const ratesMap = {};
                    (dest.vehicle_rates || []).forEach((r) => {
                      ratesMap[r.vehicle_type_id] = r.destination_rate;
                    });

                    const isSaving = savingDestId === dest.id;

                    return (
                      <tr key={dest.id} className="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                        <td className="px-4 py-3 font-bold text-gray-800 dark:text-gray-200 text-xs">
                          {dest.region}
                        </td>
                        <td className="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-xs">
                          {dest.province}
                        </td>
                        <td className="px-4 py-3 font-extrabold text-gray-900 dark:text-white text-xs">
                          📍 {dest.city}
                        </td>

                        {vehicleTypes.map((vt) => {
                          const existingVal = ratesMap[vt.id] !== undefined ? ratesMap[vt.id] : dest.destination_rate;
                          const rawInputVal = rateInputs[`${dest.id}_${vt.id}`] !== undefined
                            ? rateInputs[`${dest.id}_${vt.id}`]
                            : existingVal;

                          const currentInputVal = rawInputVal !== '' && rawInputVal !== null && rawInputVal !== undefined
                            ? Math.round(Number(rawInputVal))
                            : '';

                          return (
                            <td key={vt.id} className="px-2 py-2 text-center border-l border-r border-gray-100 dark:border-gray-700 bg-slate-50/30 dark:bg-slate-900/20">
                              <div className="relative w-20 mx-auto">
                                <span className="absolute inset-y-0 left-0 pl-1.5 flex items-center pointer-events-none text-[11px] font-black text-gray-400">₱</span>
                                <input
                                  type="number"
                                  step="1"
                                  min="0"
                                  max="9999"
                                  value={currentInputVal}
                                  onChange={(e) => handleRateInputChange(dest.id, vt.id, e.target.value)}
                                  className="pl-4 pr-1 py-1 w-20 text-xs font-black text-sky-600 dark:text-sky-400 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-sky-500 focus:border-sky-500 shadow-2xs text-center"
                                />
                              </div>
                            </td>
                          );
                        })}

                        <td className="px-4 py-3 text-right whitespace-nowrap">
                          <button
                            type="button"
                            onClick={() => handleSaveInlineRates(dest)}
                            disabled={isSaving}
                            className="p-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-2xs transition inline-flex items-center justify-center disabled:opacity-50"
                            title="Save rates for destination"
                          >
                            {isSaving ? (
                              <Loader2 className="w-4 h-4 animate-spin text-white" />
                            ) : (
                              <Save className="w-4 h-4" />
                            )}
                          </button>
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Dynamic Modal for Adding Vehicle Type */}
        {showAddTypeModal && (
          <div className="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-md w-full p-6 space-y-4">
              <div className="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                <h3 className="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                  🚗 Add New Vehicle Type
                </h3>
                <button
                  type="button"
                  onClick={() => setShowAddTypeModal(false)}
                  className="text-gray-400 hover:text-gray-600 text-lg font-bold"
                >
                  &times;
                </button>
              </div>

              <form onSubmit={handleAddVehicleType} className="space-y-4">
                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Vehicle Type Name (e.g. Crossover, Luxury, Minibus)
                  </label>
                  <input
                    type="text"
                    value={newTypeData.name}
                    onChange={(e) => setNewTypeData('name', e.target.value)}
                    required
                    placeholder="e.g. Crossover, Truck, Minibus"
                    className="w-full text-sm font-bold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl p-2.5"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Description (Optional)
                  </label>
                  <textarea
                    value={newTypeData.description}
                    onChange={(e) => setNewTypeData('description', e.target.value)}
                    rows={2}
                    placeholder="e.g. Commercial heavy-duty or specialty vehicles"
                    className="w-full text-xs border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl p-2.5"
                  />
                </div>

                <div className="flex justify-end space-x-3 pt-2">
                  <button
                    type="button"
                    onClick={() => setShowAddTypeModal(false)}
                    className="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    disabled={addingType}
                    className="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs"
                  >
                    {addingType ? 'Adding...' : 'Add Dynamic Vehicle Type'}
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
