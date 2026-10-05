import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
  Users,
  ShieldCheck,
  CheckCircle2,
  XCircle,
  Search,
  UserCheck,
  UserX,
  Shield,
  Trash2,
  Mail,
  Phone
} from 'lucide-react';

export default function Index({ users, pendingCount = 0, approvedCount = 0, filters = {} }) {
  const [search, setSearch] = useState(filters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(filters.status || 'all');

  const userItems = users?.data || [];

  const handleFilter = (updates = {}) => {
    const nextFilters = {
      search,
      status: selectedStatus,
      ...updates,
    };

    const query = {};
    if (nextFilters.search) query.search = nextFilters.search;
    if (nextFilters.status && nextFilters.status !== 'all') query.status = nextFilters.status;

    router.get('/admin/users', query, { preserveState: true, replace: true });
  };

  const handleApprove = (userId, name) => {
    if (confirm(`Approve user account for ${name}?`)) {
      router.post(`/admin/users/${userId}/approve`);
    }
  };

  const handleToggleAdmin = (userId, name, isAdmin) => {
    const action = isAdmin ? 'demote from Admin to Standard Host' : 'grant Administrator privileges to';
    if (confirm(`Are you sure you want to ${action} ${name}?`)) {
      router.post(`/admin/users/${userId}/toggle-admin`);
    }
  };

  const handleReject = (userId, name) => {
    if (confirm(`Reject and delete registration for ${name}?`)) {
      router.delete(`/admin/users/${userId}`);
    }
  };

  return (
    <AppLayout title="User Approvals & Roles">
      <Head title="User Approvals" />

      <div className="space-y-6 pb-6">
        {/* Header */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-5 rounded-3xl shadow-xs transition-colors duration-300">
          <div>
            <h1 className="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
              <ShieldCheck className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
              User Approvals & Roles
            </h1>
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage partner hosts, pending registration approvals, and administrative privileges</p>
          </div>

          <div className="flex items-center gap-3">
            <div className="px-4 py-2 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 rounded-2xl text-center">
              <span className="text-[10px] font-bold uppercase block">Pending</span>
              <span className="text-lg font-black">{pendingCount}</span>
            </div>
            <div className="px-4 py-2 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-2xl text-center">
              <span className="text-[10px] font-bold uppercase block">Approved</span>
              <span className="text-lg font-black">{approvedCount}</span>
            </div>
          </div>
        </div>

        {/* Filter Controls */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-4 rounded-3xl shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center gap-3 transition-colors duration-300">
          <div className="relative flex-1">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              value={search}
              onChange={(e) => {
                setSearch(e.target.value);
                handleFilter({ search: e.target.value });
              }}
              placeholder="Search user name, email, or contact number..."
              className="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-emerald-500"
            />
          </div>

          <div className="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
            {['all', 'pending', 'approved'].map((st) => (
              <button
                key={st}
                onClick={() => {
                  setSelectedStatus(st);
                  handleFilter({ status: st });
                }}
                className={`px-3.5 py-1.5 rounded-xl text-xs font-bold capitalize transition-all ${
                  selectedStatus === st
                    ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20'
                    : 'bg-gray-50 dark:bg-gray-950 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800'
                }`}
              >
                {st}
              </button>
            ))}
          </div>
        </div>

        {/* Users Cards List */}
        {userItems.length === 0 ? (
          <div className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-3xl p-10 text-center space-y-3 shadow-xs">
            <Users className="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto" />
            <h3 className="text-base font-bold text-gray-800 dark:text-gray-200">No Users Found</h3>
            <p className="text-xs text-gray-500 dark:text-gray-400">There are no user registration entries matching your criteria.</p>
          </div>
        ) : (
          <div className="space-y-4">
            {userItems.map((u) => {
              const isApproved = u.is_approved;
              const isAdmin = u.is_admin;

              return (
                <div
                  key={u.id}
                  className="bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 hover:border-emerald-500/50 p-5 rounded-3xl shadow-xs hover:shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all"
                >
                  <div className="flex items-center gap-4 min-w-0">
                    <div className="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-sm text-emerald-700 dark:text-emerald-300 overflow-hidden shrink-0">
                      {u.avatar_path ? (
                        <img src={`/${u.avatar_path}`} alt={u.name} className="w-full h-full object-cover" />
                      ) : (
                        u.name?.[0] || 'U'
                      )}
                    </div>

                    <div className="space-y-1 min-w-0">
                      <div className="flex items-center gap-2 flex-wrap">
                        <h3 className="text-base font-extrabold text-gray-900 dark:text-white truncate">{u.name}</h3>
                        <span
                          className={`px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase ${
                            isApproved
                              ? 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                              : 'bg-rose-100 dark:bg-rose-950/70 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800'
                          }`}
                        >
                          {isApproved ? 'Approved' : 'Pending Approval'}
                        </span>
                        {isAdmin && (
                          <span className="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-indigo-100 dark:bg-indigo-950/70 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                            Administrator
                          </span>
                        )}
                      </div>

                      <p className="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 truncate">
                        <Mail className="w-3.5 h-3.5 text-gray-400" />
                        {u.email}
                      </p>

                      {u.contact_number && (
                        <p className="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 truncate">
                          <Phone className="w-3.5 h-3.5 text-gray-400" />
                          {u.contact_number}
                        </p>
                      )}
                    </div>
                  </div>

                  <div className="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100 dark:border-gray-800 shrink-0">
                    {!isApproved && (
                      <button
                        onClick={() => handleApprove(u.id, u.name)}
                        className="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs"
                      >
                        <UserCheck className="w-4 h-4" />
                        Approve User
                      </button>
                    )}

                    <button
                      onClick={() => handleToggleAdmin(u.id, u.name, isAdmin)}
                      className={`px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 border ${
                        isAdmin
                          ? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700'
                          : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60'
                      }`}
                    >
                      <Shield className="w-4 h-4" />
                      {isAdmin ? 'Revoke Admin' : 'Make Admin'}
                    </button>

                    <button
                      onClick={() => handleReject(u.id, u.name)}
                      className="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                      title="Reject Registration"
                    >
                      <UserX className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </AppLayout>
  );
}
