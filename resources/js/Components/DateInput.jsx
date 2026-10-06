import React, { useState, useEffect, useRef } from 'react';
import { Calendar } from 'lucide-react';

/**
 * Formats YYYY-MM-DD to MM/DD/YYYY
 */
export const formatIsoToMdy = (isoStr) => {
  if (!isoStr) return '';
  const clean = String(isoStr).split('T')[0];
  const parts = clean.split('-');
  if (parts.length === 3 && parts[0].length === 4) {
    const [y, m, d] = parts;
    return `${m.padStart(2, '0')}/${d.padStart(2, '0')}/${y}`;
  }
  return isoStr;
};

/**
 * Parses MM/DD/YYYY to YYYY-MM-DD
 */
export const parseMdyToIso = (mdyStr) => {
  if (!mdyStr) return '';
  const trimmed = mdyStr.trim();
  
  // Match MM/DD/YYYY or M/D/YYYY
  const match = trimmed.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
  if (match) {
    const month = parseInt(match[1], 10);
    const day = parseInt(match[2], 10);
    const year = parseInt(match[3], 10);

    if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year >= 1900 && year <= 2100) {
      return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    }
  }

  // If already in YYYY-MM-DD format
  if (/^\d{4}-\d{2}-\d{2}$/.test(trimmed)) {
    return trimmed;
  }

  return '';
};

/**
 * DateInput component with MM/DD/YYYY display and native calendar picker trigger
 */
export default function DateInput({
  value = '',
  onChange = () => {},
  min = '',
  max = '',
  required = false,
  placeholder = 'mm/dd/yyyy',
  className = '',
  disabled = false,
  id,
  name,
}) {
  const [displayText, setDisplayText] = useState(() => formatIsoToMdy(value));
  const hiddenDateRef = useRef(null);

  // Synchronize display text when value prop changes externally
  useEffect(() => {
    setDisplayText(formatIsoToMdy(value));
  }, [value]);

  const handleTextChange = (e) => {
    let raw = e.target.value;
    
    // Auto format if user is typing only numbers: e.g. 10252026 -> 10/25/2026
    const digitsOnly = raw.replace(/\D/g, '');
    if (raw.length > displayText.length && digitsOnly.length <= 8 && !raw.includes('/')) {
      if (digitsOnly.length > 4) {
        raw = `${digitsOnly.slice(0, 2)}/${digitsOnly.slice(2, 4)}/${digitsOnly.slice(4, 8)}`;
      } else if (digitsOnly.length > 2) {
        raw = `${digitsOnly.slice(0, 2)}/${digitsOnly.slice(2)}`;
      }
    }

    setDisplayText(raw);

    const iso = parseMdyToIso(raw);
    if (iso) {
      onChange(iso);
    } else if (raw === '') {
      onChange('');
    }
  };

  const handleBlur = () => {
    const iso = parseMdyToIso(displayText);
    if (iso) {
      setDisplayText(formatIsoToMdy(iso));
      onChange(iso);
    } else if (displayText.trim() === '') {
      setDisplayText('');
      onChange('');
    } else {
      // If invalid, revert back to current valid value
      setDisplayText(formatIsoToMdy(value));
    }
  };

  const handleNativePickerChange = (e) => {
    const newIso = e.target.value;
    setDisplayText(formatIsoToMdy(newIso));
    onChange(newIso);
  };

  const openDatePicker = () => {
    if (disabled) return;
    if (hiddenDateRef.current) {
      try {
        if (typeof hiddenDateRef.current.showPicker === 'function') {
          hiddenDateRef.current.showPicker();
        } else {
          hiddenDateRef.current.focus();
        }
      } catch (err) {
        hiddenDateRef.current.focus();
      }
    }
  };

  return (
    <div className="relative flex items-center">
      <input
        type="text"
        id={id}
        name={name}
        value={displayText}
        onChange={handleTextChange}
        onBlur={handleBlur}
        placeholder={placeholder}
        required={required}
        disabled={disabled}
        pattern="\d{2}/\d{2}/\d{4}"
        className={`w-full pr-10 ${className}`}
      />
      
      {/* Calendar picker trigger button */}
      <button
        type="button"
        onClick={openDatePicker}
        disabled={disabled}
        className="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
        title="Open calendar"
        tabIndex={-1}
      >
        <Calendar className="w-4 h-4" />
      </button>

      {/* Hidden native date input for calendar event popover */}
      <input
        type="date"
        ref={hiddenDateRef}
        value={value || ''}
        min={min}
        max={max}
        onChange={handleNativePickerChange}
        tabIndex={-1}
        aria-hidden="true"
        className="sr-only"
      />
    </div>
  );
}
