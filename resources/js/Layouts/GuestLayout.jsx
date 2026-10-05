import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { ShieldCheck, Sun, Moon } from 'lucide-react';

export default function GuestLayout({ children, title }) {
  const [darkMode, setDarkMode] = useState(false);

  useEffect(() => {
    const isDark = document.documentElement.classList.contains('dark');
    setDarkMode(isDark);
  }, []);

  const toggleTheme = () => {
    const nextDark = !darkMode;
    setDarkMode(nextDark);
    if (nextDark) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col justify-center items-center p-4 sm:p-6 font-sans selection:bg-emerald-500 selection:text-white transition-colors duration-300 relative">
      {/* Absolute Theme Switcher */}
      <button
        onClick={toggleTheme}
        type="button"
        className="absolute top-4 right-4 p-2.5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 shadow-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors z-20"
        title="Toggle Light / Dark Theme"
      >
        {darkMode ? <Sun className="w-5 h-5 text-amber-400" /> : <Moon className="w-5 h-5 text-gray-600" />}
      </button>

      <div className="w-full max-w-md space-y-4 relative z-10 my-auto">
        {/* Full Brand Logo */}
        <div className="text-center flex justify-center pb-1">
          <Link href="/" className="group block">
            <img
              src="/images/logo-full.png"
              alt="ONEDRIVE WHEELS"
              className="w-48 sm:w-56 h-auto object-contain drop-shadow-sm dark:drop-shadow-[0_4px_16px_rgba(16,185,129,0.2)] dark:brightness-110 group-hover:scale-105 transition-all duration-300"
            />
          </Link>
        </div>

        {/* Card Container */}
        <div className="w-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800/80 shadow-xl overflow-hidden rounded-2xl sm:rounded-3xl p-6 sm:p-8 transition-colors duration-300">
          {children}
        </div>

        <div className="text-center text-xs text-gray-500 dark:text-gray-400 flex items-center justify-center gap-1.5 pt-1">
          <ShieldCheck className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
          <span>ONEDRIVE WHEELS Enterprise Platform</span>
        </div>
      </div>
    </div>
  );
}

