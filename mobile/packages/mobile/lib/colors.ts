export const Colors = {
  primary: '#1E40AF',      // blue-800
  primaryLight: '#3B82F6', // blue-500
  primaryDark: '#1E3A8A',  // blue-900
  accent: '#F59E0B',       // amber-500
  success: '#10B981',      // emerald-500
  warning: '#F59E0B',      // amber-500
  danger: '#EF4444',       // red-500
  info: '#6366F1',         // indigo-500

  bg: '#F0F4FF',
  card: '#FFFFFF',
  border: '#E2E8F0',
  textPrimary: '#1E293B',
  textSecondary: '#64748B',
  textMuted: '#94A3B8',

  statusColors: {
    'Pending':       { bg: '#FEF3C7', text: '#92400E' },
    'In Progress':   { bg: '#DBEAFE', text: '#1E40AF' },
    'Completed':     { bg: '#D1FAE5', text: '#065F46' },
    'Not Completed': { bg: '#FEE2E2', text: '#991B1B' },
    'Broken':        { bg: '#F3E8FF', text: '#6B21A8' },
    'Cancelled':     { bg: '#F1F5F9', text: '#475569' },
    'Assigned':      { bg: '#E0F2FE', text: '#0369A1' },
    'On Hold':       { bg: '#FFF7ED', text: '#9A3412' },
    'Billed':        { bg: '#F0FDF4', text: '#166534' },
  } as Record<string, { bg: string; text: string }>,

  priorityColors: {
    'High':   { bg: '#FEE2E2', text: '#991B1B' },
    'Normal': { bg: '#EFF6FF', text: '#1D4ED8' },
    'Low':    { bg: '#F1F5F9', text: '#475569' },
  } as Record<string, { bg: string; text: string }>,
};
