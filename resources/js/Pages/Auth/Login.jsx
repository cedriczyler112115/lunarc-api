import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { CheckCircle2, UserPlus } from 'lucide-react';

export default function Login({ status }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
    is_mobile: typeof window !== 'undefined' ? (window.innerWidth < 640 ? '1' : '0') : '0',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/login', {
      onFinish: () => reset('password'),
    });
  };

  return (
    <GuestLayout>
      <Head title="Log In" />

      <div className="mb-6 text-center">
        <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Log in to manage vehicles, bookings & Mindanao trip schedules
        </p>
      </div>

      {/* Session Status / Registration Pending Notice */}
      {status && (
        <div className="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 rounded-r-xl shadow-xs text-xs font-semibold leading-relaxed flex items-start gap-2">
          <CheckCircle2 className="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" />
          <span>{status}</span>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <input type="hidden" name="is_mobile" value={data.is_mobile} />

        {/* Email Address */}
        <div>
          <label className="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
            Email Address
          </label>
          <input
            type="email"
            value={data.email}
            onChange={(e) => setData('email', e.target.value)}
            required
            autoFocus
            autoComplete="username"
            placeholder="e.g. user@example.com"
            className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
          />
          {errors.email && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.email}</p>}
        </div>

        {/* Password */}
        <div>
          <div className="flex items-center justify-between mb-1">
            <label className="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Password
            </label>
            <Link
              href="/forgot-password"
              className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold"
            >
              Forgot password?
            </Link>
          </div>

          <input
            type="password"
            value={data.password}
            onChange={(e) => setData('password', e.target.value)}
            required
            autoComplete="current-password"
            placeholder="••••••••"
            className="w-full text-sm px-3.5 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-colors"
          />
          {errors.password && <p className="text-xs text-rose-500 font-semibold mt-1">{errors.password}</p>}
        </div>

        {/* Remember Me */}
        <div className="block pt-1">
          <label className="inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              checked={data.remember}
              onChange={(e) => setData('remember', e.target.checked)}
              className="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-emerald-600 shadow-xs focus:ring-emerald-500"
            />
            <span className="ms-2 text-xs font-semibold text-gray-600 dark:text-gray-400">
              Remember me on this device
            </span>
          </label>
        </div>

        {/* Submit Log in Button */}
        <div className="pt-2">
          <button
            type="submit"
            disabled={processing}
            className="w-full py-3 px-4 justify-center text-sm font-extrabold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl shadow-md transition duration-150 flex items-center gap-2"
          >
            {processing ? 'Logging in...' : 'Log in'}
          </button>
        </div>
      </form>

      {/* Registration Button & Section */}
      <div className="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 text-center space-y-3">
        <p className="text-xs text-gray-500 dark:text-gray-400 font-medium">
          Don't have an account yet?
        </p>
        <Link
          href="/register"
          className="inline-flex items-center justify-center w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition duration-150 gap-2"
        >
          <UserPlus className="w-4 h-4" />
          Register New Account
        </Link>
        <p className="text-[10px] text-gray-400 dark:text-gray-500 italic">
          Note: Newly registered accounts require Admin approval before access is enabled.
        </p>
      </div>
    </GuestLayout>
  );
}

