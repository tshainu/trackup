import React, { useMemo } from 'react';
import {
  View, Text, StyleSheet, ScrollView,
  RefreshControl, ActivityIndicator,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { techApi, JobCard, FieldComplaint } from '../../lib/api';
import { useAuth } from '../../lib/auth';
import { Colors } from '../../lib/colors';

const ORANGE = '#E8490F';
const PURPLE = '#6366F1';

// ── Small helpers ──────────────────────────────────────────────────────────

function hoursAgo(dateStr?: string): string {
  if (!dateStr) return '—';
  const diff = Date.now() - new Date(dateStr).getTime();
  const h = Math.floor(diff / 3_600_000);
  const d = Math.floor(h / 24);
  if (d >= 1) return `${d}d ${h % 24}h`;
  if (h >= 1) return `${h}h`;
  const m = Math.floor(diff / 60_000);
  return `${m}m`;
}

function turnaround(start?: string, end?: string): string {
  if (!start) return '—';
  const s = new Date(start).getTime();
  const e = end ? new Date(end).getTime() : Date.now();
  const h = Math.floor((e - s) / 3_600_000);
  const d = Math.floor(h / 24);
  if (d >= 1) return `${d}d ${h % 24}h`;
  return `${h}h`;
}

function fmt(dateStr?: string): string {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ── Sub-components ─────────────────────────────────────────────────────────

function SectionTitle({ title, icon }: { title: string; icon: string }) {
  return (
    <View style={styles.sectionTitleRow}>
      <Ionicons name={icon as any} size={15} color={Colors.textSecondary} />
      <Text style={styles.sectionTitleText}>{title}</Text>
    </View>
  );
}

function StatBox({ icon, label, value, color }: { icon: string; label: string; value: string | number; color: string }) {
  return (
    <View style={[styles.statBox, { borderLeftColor: color }]}>
      <View style={[styles.statIcon, { backgroundColor: color + '18' }]}>
        <Ionicons name={icon as any} size={18} color={color} />
      </View>
      <View>
        <Text style={styles.statValue}>{value}</Text>
        <Text style={styles.statLabel}>{label}</Text>
      </View>
    </View>
  );
}

function StatusPill({ status }: { status: string }) {
  const map: Record<string, { bg: string; text: string }> = {
    'Completed':     { bg: '#D1FAE5', text: '#065F46' },
    'In Progress':   { bg: '#DBEAFE', text: '#1E40AF' },
    'Pending':       { bg: '#FEF3C7', text: '#92400E' },
    'Assigned':      { bg: '#EDE9FE', text: '#5B21B6' },
    'Not Completed': { bg: '#FEE2E2', text: '#991B1B' },
    'On Hold':       { bg: '#F3F4F6', text: '#374151' },
  };
  const c = map[status] ?? { bg: '#F1F5F9', text: '#475569' };
  return (
    <View style={[styles.pill, { backgroundColor: c.bg }]}>
      <Text style={[styles.pillText, { color: c.text }]}>{status}</Text>
    </View>
  );
}

function JobRow({ job }: { job: JobCard }) {
  return (
    <View style={styles.jobRow}>
      <View style={styles.jobRowLeft}>
        {/* Device + order */}
        <View style={styles.jobRowHeader}>
          <Text style={styles.jobOrderNo}>{job.order_no}</Text>
          <StatusPill status={job.status} />
        </View>
        {/* Device info */}
        <View style={styles.jobMeta}>
          <Ionicons name="phone-portrait-outline" size={12} color={Colors.textMuted} />
          <Text style={styles.jobMetaText}>
            {[job.device_brand, job.device_name].filter(Boolean).join(' ') || 'Device N/A'}
            {job.serial_no ? `  ·  S/N: ${job.serial_no}` : ''}
          </Text>
        </View>
        {/* Customer */}
        <View style={styles.jobMeta}>
          <Ionicons name="person-outline" size={12} color={Colors.textMuted} />
          <Text style={styles.jobMetaText}>{job.customer_name}</Text>
        </View>
        {/* Fault */}
        {job.device_fault && (
          <View style={styles.jobMeta}>
            <Ionicons name="build-outline" size={12} color={Colors.textMuted} />
            <Text style={styles.jobMetaText} numberOfLines={1}>{job.device_fault}</Text>
          </View>
        )}
        {/* Dates + turnaround */}
        <View style={styles.jobFooter}>
          <Text style={styles.jobDate}>Received: {fmt(job.date)}</Text>
          {job.estimated_delivery && (
            <Text style={styles.jobDate}>  Est: {fmt(job.estimated_delivery)}</Text>
          )}
          <View style={styles.taChip}>
            <Ionicons name="time-outline" size={11} color={ORANGE} />
            <Text style={styles.taText}>{turnaround(job.date)}</Text>
          </View>
        </View>

      </View>
    </View>
  );
}

function FieldRow({ job }: { job: FieldComplaint }) {
  return (
    <View style={styles.jobRow}>
      <View style={styles.jobRowLeft}>
        <View style={styles.jobRowHeader}>
          <Text style={styles.jobOrderNo}>{job.complaint_no}</Text>
          <StatusPill status={job.status} />
        </View>
        <View style={styles.jobMeta}>
          <Ionicons name="construct-outline" size={12} color={Colors.textMuted} />
          <Text style={styles.jobMetaText}>{job.service_type_name ?? 'Field Service'}</Text>
        </View>
        <View style={styles.jobMeta}>
          <Ionicons name="person-outline" size={12} color={Colors.textMuted} />
          <Text style={styles.jobMetaText}>{job.customer_name}</Text>
        </View>
        {job.description && (
          <View style={styles.jobMeta}>
            <Ionicons name="document-text-outline" size={12} color={Colors.textMuted} />
            <Text style={styles.jobMetaText} numberOfLines={1}>{job.description}</Text>
          </View>
        )}
        <View style={styles.jobFooter}>
          <Text style={styles.jobDate}>Scheduled: {fmt(job.scheduled_date)}</Text>
          <View style={styles.taChip}>
            <Ionicons name="time-outline" size={11} color={PURPLE} />
            <Text style={[styles.taText, { color: PURPLE }]}>{turnaround(job.scheduled_date)}</Text>
          </View>
        </View>

      </View>
    </View>
  );
}

// ── Main screen ────────────────────────────────────────────────────────────

export default function ReportScreen() {
  const { session } = useAuth();
  const emp = session?.role === 'technician' ? session.employee : null;
  const insets = useSafeAreaInsets();

  const { data: jobsData, isLoading: jobsLoading, refetch: refetchJobs, isRefetching: refetchingJobs } =
    useQuery({ queryKey: ['tech-all-jobs'], queryFn: () => techApi.allJobs() });

  const { data: fieldData, isLoading: fieldLoading, refetch: refetchField, isRefetching: refetchingField } =
    useQuery({ queryKey: ['tech-all-field'], queryFn: () => techApi.allFieldJobs() });

  const isLoading = jobsLoading || fieldLoading;
  const isRefetching = refetchingJobs || refetchingField;
  function refetch() { refetchJobs(); refetchField(); }

  const jobs: JobCard[]         = jobsData?.jobs  ?? [];
  const field: FieldComplaint[] = fieldData?.jobs ?? [];

  const jobStats = useMemo(() => {
    const total        = jobs.length;
    const completed    = jobs.filter(j => j.status === 'Completed').length;
    const inProgress   = jobs.filter(j => j.status === 'In Progress').length;
    const pending      = jobs.filter(j => j.status === 'Pending').length;
    const notCompleted = jobs.filter(j => j.status === 'Not Completed').length;
    const onHold       = jobs.filter(j => j.status === 'On Hold').length;
    const rate         = total > 0 ? Math.round((completed / total) * 100) : 0;
    return { total, completed, inProgress, pending, notCompleted, onHold, rate };
  }, [jobs]);

  const fieldStats = useMemo(() => {
    const total      = field.length;
    const completed  = field.filter(f => f.status === 'Completed').length;
    const inProgress = field.filter(f => f.status === 'In Progress').length;
    const pending    = field.filter(f => f.status === 'Pending').length;
    const assigned   = field.filter(f => f.status === 'Assigned').length;
    const rate       = total > 0 ? Math.round((completed / total) * 100) : 0;
    return { total, completed, inProgress, pending, assigned, rate };
  }, [field]);

  const thisMonth = new Date().toISOString().slice(0, 7);
  const jobsThisMonth       = jobs.filter(j => j.date?.startsWith(thisMonth)).length;
  const completedThisMonth  = jobs.filter(j => j.date?.startsWith(thisMonth) && j.status === 'Completed').length;
  const fieldThisMonth      = field.filter(f => f.scheduled_date?.startsWith(thisMonth)).length;

  // Sort: in-progress first, then pending, then completed
  const sortedJobs = [...jobs].sort((a, b) => {
    const order: Record<string, number> = { 'In Progress': 0, 'Pending': 1, 'Assigned': 2, 'Completed': 3, 'Not Completed': 4 };
    return (order[a.status] ?? 5) - (order[b.status] ?? 5);
  });
  const sortedField = [...field].sort((a, b) => {
    const order: Record<string, number> = { 'In Progress': 0, 'Assigned': 1, 'Pending': 2, 'Completed': 3 };
    return (order[a.status] ?? 5) - (order[b.status] ?? 5);
  });

  if (isLoading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={ORANGE} /></View>;
  }

  return (
    <ScrollView
      style={styles.root}
      contentContainerStyle={{ paddingBottom: insets.bottom + 80 }}
      refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} tintColor={ORANGE} colors={[ORANGE]} />}
    >
      {/* Header */}
      <View style={[styles.header, { paddingTop: insets.top + 16 }]}>
        <View>
          <Text style={styles.headerTitle}>Performance Report</Text>
          <Text style={styles.headerSub}>{emp?.name ?? 'Technician'}</Text>
          {emp?.role && <Text style={styles.headerRole}>{emp.role}</Text>}
        </View>
        <View style={styles.headerIcon}>
          <Ionicons name="bar-chart" size={24} color="#fff" />
        </View>
      </View>

      <View style={styles.body}>

        {/* This month banner */}
        <View style={styles.monthCard}>
          <View style={styles.monthItem}>
            <Ionicons name="calendar-outline" size={16} color={ORANGE} />
            <Text style={styles.monthLabel}>This Month</Text>
          </View>
          <View style={styles.monthDivider} />
          <View style={styles.monthItem}>
            <Text style={styles.monthNum}>{completedThisMonth}</Text>
            <Text style={styles.monthSub}>Jobs Done</Text>
          </View>
          <View style={styles.monthDivider} />
          <View style={styles.monthItem}>
            <Text style={styles.monthNum}>{jobsThisMonth}</Text>
            <Text style={styles.monthSub}>Jobs Total</Text>
          </View>
          <View style={styles.monthDivider} />
          <View style={styles.monthItem}>
            <Text style={styles.monthNum}>{fieldThisMonth}</Text>
            <Text style={styles.monthSub}>Field Visits</Text>
          </View>
        </View>

        {/* ── JOB ORDERS ── */}
        {jobs.length > 0 && (
          <>
            <SectionTitle title="Job Orders Summary" icon="briefcase-outline" />

            <View style={styles.statsGrid}>
              <StatBox icon="list-outline"             label="Total"        value={jobStats.total}       color={ORANGE} />
              <StatBox icon="checkmark-circle-outline" label="Completed"    value={jobStats.completed}   color={Colors.success} />
              <StatBox icon="reload-outline"           label="In Progress"  value={jobStats.inProgress}  color={Colors.info} />
              <StatBox icon="time-outline"             label="Pending"      value={jobStats.pending}     color={Colors.warning} />
              {jobStats.notCompleted > 0 && (
                <StatBox icon="close-circle-outline"   label="Not Completed" value={jobStats.notCompleted} color={Colors.danger} />
              )}
              {jobStats.onHold > 0 && (
                <StatBox icon="pause-circle-outline"   label="On Hold"      value={jobStats.onHold}      color="#94A3B8" />
              )}
            </View>

            {/* Completion rate card */}
            <View style={styles.card}>
              <Text style={styles.cardLabel}>COMPLETION RATE</Text>
              <View style={styles.rateRow}>
                <Text style={styles.rateValue}>{jobStats.rate}%</Text>
                <View style={styles.rateBarBg}>
                  <View style={[styles.rateBarFill, { width: `${jobStats.rate}%` as any, backgroundColor: ORANGE }]} />
                </View>
              </View>

            </View>

            {/* Job list */}
            <SectionTitle title={`All Job Orders (${jobs.length})`} icon="list-outline" />
            <View style={styles.listCard}>
              {sortedJobs.map((job, i) => (
                <View key={job.id}>
                  <JobRow job={job} />
                  {i < sortedJobs.length - 1 && <View style={styles.divider} />}
                </View>
              ))}
            </View>
          </>
        )}

        {/* ── FIELD JOBS ── */}
        {field.length > 0 && (
          <>
            <SectionTitle title="Field Services Summary" icon="location-outline" />

            <View style={styles.statsGrid}>
              <StatBox icon="location-outline"         label="Total"       value={fieldStats.total}      color={PURPLE} />
              <StatBox icon="checkmark-done-outline"   label="Completed"   value={fieldStats.completed}  color={Colors.success} />
              <StatBox icon="reload-outline"           label="In Progress" value={fieldStats.inProgress} color={Colors.info} />
              <StatBox icon="time-outline"             label="Pending"     value={fieldStats.pending}    color={Colors.warning} />
            </View>

            <View style={styles.card}>
              <Text style={styles.cardLabel}>FIELD COMPLETION RATE</Text>
              <View style={styles.rateRow}>
                <Text style={[styles.rateValue, { color: PURPLE }]}>{fieldStats.rate}%</Text>
                <View style={styles.rateBarBg}>
                  <View style={[styles.rateBarFill, { width: `${fieldStats.rate}%` as any, backgroundColor: PURPLE }]} />
                </View>
              </View>

            </View>

            <SectionTitle title={`All Field Visits (${field.length})`} icon="map-outline" />
            <View style={styles.listCard}>
              {sortedField.map((f, i) => (
                <View key={f.id}>
                  <FieldRow job={f} />
                  {i < sortedField.length - 1 && <View style={styles.divider} />}
                </View>
              ))}
            </View>
          </>
        )}

        {jobs.length === 0 && field.length === 0 && (
          <View style={styles.empty}>
            <Ionicons name="bar-chart-outline" size={56} color={Colors.textMuted} />
            <Text style={styles.emptyText}>No job history yet</Text>
          </View>
        )}

      </View>
    </ScrollView>
  );
}

// ── Styles ─────────────────────────────────────────────────────────────────

const styles = StyleSheet.create({
  root:   { flex: 1, backgroundColor: Colors.bg },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },

  header: {
    backgroundColor: ORANGE,
    paddingHorizontal: 20,
    paddingBottom: 20,
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
  },
  headerTitle: { fontSize: 22, fontWeight: '900', color: '#fff' },
  headerSub:   { fontSize: 14, color: 'rgba(255,255,255,0.85)', marginTop: 2, fontWeight: '600' },
  headerRole:  { fontSize: 12, color: 'rgba(255,255,255,0.65)', marginTop: 1 },
  headerIcon:  { backgroundColor: 'rgba(255,255,255,0.2)', padding: 10, borderRadius: 12, marginTop: 4 },

  body: { padding: 16 },

  // Month banner
  monthCard: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#fff', borderRadius: 14, padding: 14,
    marginBottom: 20, justifyContent: 'space-around',
    elevation: 2, shadowColor: '#000', shadowOpacity: 0.06, shadowRadius: 6,
  },
  monthItem:    { alignItems: 'center', gap: 2 },
  monthLabel:   { fontSize: 11, fontWeight: '700', color: ORANGE },
  monthNum:     { fontSize: 22, fontWeight: '900', color: Colors.textPrimary },
  monthSub:     { fontSize: 10, color: Colors.textSecondary },
  monthDivider: { width: 1, height: 36, backgroundColor: Colors.border },

  // Section title
  sectionTitleRow: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 10, marginTop: 4 },
  sectionTitleText: {
    fontSize: 12, fontWeight: '800', color: Colors.textSecondary,
    textTransform: 'uppercase', letterSpacing: 0.8,
  },

  // Stat grid
  statsGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 12 },
  statBox: {
    width: '47.5%', backgroundColor: '#fff', borderRadius: 12, padding: 12,
    flexDirection: 'row', alignItems: 'center', gap: 10, borderLeftWidth: 3,
    elevation: 1, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 3,
  },
  statIcon:  { width: 34, height: 34, borderRadius: 8, justifyContent: 'center', alignItems: 'center' },
  statValue: { fontSize: 22, fontWeight: '900', color: Colors.textPrimary },
  statLabel: { fontSize: 11, color: Colors.textSecondary, marginTop: 1 },

  // Summary card
  card: {
    backgroundColor: '#fff', borderRadius: 14, padding: 16, marginBottom: 20,
    elevation: 1, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4,
  },
  cardLabel: { fontSize: 11, fontWeight: '800', color: Colors.textMuted, letterSpacing: 0.8, marginBottom: 10, textTransform: 'uppercase' },
  rateRow:    { flexDirection: 'row', alignItems: 'center', gap: 12, marginBottom: 14 },
  rateValue:  { fontSize: 30, fontWeight: '900', color: ORANGE, width: 66 },
  rateBarBg:  { flex: 1, height: 10, backgroundColor: '#F1F5F9', borderRadius: 6, overflow: 'hidden' },
  rateBarFill:{ height: 10, borderRadius: 6 },


  // Job list card
  listCard: {
    backgroundColor: '#fff', borderRadius: 14, marginBottom: 24,
    elevation: 1, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4,
    overflow: 'hidden',
  },
  divider: { height: 1, backgroundColor: Colors.border, marginHorizontal: 14 },

  // Job row
  jobRow:       { padding: 14 },
  jobRowLeft:   { gap: 4 },
  jobRowHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 4 },
  jobOrderNo:   { fontSize: 13, fontWeight: '800', color: Colors.textPrimary },
  jobMeta:      { flexDirection: 'row', alignItems: 'center', gap: 5 },
  jobMetaText:  { fontSize: 12, color: Colors.textSecondary, flex: 1 },
  jobFooter:    { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 4, flexWrap: 'wrap' },
  jobDate:      { fontSize: 11, color: Colors.textMuted },
  taChip:       { flexDirection: 'row', alignItems: 'center', gap: 3, backgroundColor: ORANGE + '15', paddingHorizontal: 6, paddingVertical: 2, borderRadius: 6 },
  taText:       { fontSize: 11, fontWeight: '700', color: ORANGE },


  // Status pill
  pill:     { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 20 },
  pillText: { fontSize: 10, fontWeight: '700' },

  empty:     { alignItems: 'center', marginTop: 60, gap: 10 },
  emptyText: { fontSize: 15, color: Colors.textMuted },
});
