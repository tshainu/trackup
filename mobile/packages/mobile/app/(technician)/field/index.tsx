import React from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  ActivityIndicator, RefreshControl,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { techApi, FieldComplaint, FieldStats } from '../../../lib/api';
import { Colors } from '../../../lib/colors';

// ── Stat grid ──────────────────────────────────────────────────────────────
interface StatTile {
  label: string;
  key: keyof FieldStats;
  icon: keyof typeof Ionicons.glyphMap;
  color: string;
  bg: string;
}

const TILES: StatTile[] = [
  { label: 'New Today',  key: 'new',         icon: 'add-circle-outline',    color: '#2563EB', bg: '#EFF6FF' },
  { label: 'Pending',    key: 'pending',      icon: 'time-outline',          color: '#D97706', bg: '#FFFBEB' },
  { label: 'In Progress',key: 'in_progress',  icon: 'construct-outline',     color: '#7C3AED', bg: '#F5F3FF' },
  { label: 'Done',       key: 'completed',    icon: 'checkmark-circle-outline', color: '#059669', bg: '#ECFDF5' },
  { label: 'Overdue',    key: 'overdue',      icon: 'alert-circle-outline',  color: '#DC2626', bg: '#FEF2F2' },
  { label: 'Total',      key: 'total',        icon: 'list-outline',          color: '#475569', bg: '#F1F5F9' },
];

function StatGrid({ stats }: { stats: FieldStats }) {
  return (
    <View style={sg.grid}>
      {TILES.map(t => (
        <View key={t.key} style={[sg.tile, { backgroundColor: t.bg }]}>
          <View style={[sg.iconWrap, { backgroundColor: t.color + '22' }]}>
            <Ionicons name={t.icon} size={18} color={t.color} />
          </View>
          <Text style={[sg.count, { color: t.color }]}>{stats[t.key] ?? 0}</Text>
          <Text style={sg.label}>{t.label}</Text>
        </View>
      ))}
    </View>
  );
}

// ── Job card ───────────────────────────────────────────────────────────────
function FieldJobItem({ job, onPress }: { job: FieldComplaint; onPress: () => void }) {
  const sc = Colors.statusColors[job.status] ?? { bg: '#F1F5F9', text: '#475569' };
  const pc = Colors.priorityColors[job.priority] ?? { bg: '#EFF6FF', text: '#1D4ED8' };

  const isOverdue = (() => {
    if (!job.scheduled_date) return false;
    if (['Completed', 'Cancelled'].includes(job.status)) return false;
    return new Date(job.scheduled_date) < new Date(new Date().toDateString());
  })();

  return (
    <TouchableOpacity style={[styles.card, isOverdue && styles.cardOverdue]} onPress={onPress} activeOpacity={0.75}>
      <View style={styles.cardHeader}>
        <Text style={styles.complaintNo}>{job.complaint_no}</Text>
        <View style={{ flexDirection: 'row', gap: 6 }}>
          {isOverdue && (
            <View style={[styles.badge, { backgroundColor: '#FEF2F2' }]}>
              <Ionicons name="alert-circle" size={10} color="#DC2626" />
              <Text style={[styles.badgeText, { color: '#DC2626', marginLeft: 2 }]}>Overdue</Text>
            </View>
          )}
          {job.priority !== 'Normal' && (
            <View style={[styles.badge, { backgroundColor: pc.bg }]}>
              <Text style={[styles.badgeText, { color: pc.text }]}>{job.priority}</Text>
            </View>
          )}
          <View style={[styles.badge, { backgroundColor: sc.bg }]}>
            <Text style={[styles.badgeText, { color: sc.text }]}>{job.status}</Text>
          </View>
        </View>
      </View>
      <Text style={styles.customerName}>{job.customer_name}</Text>
      {job.service_type_name && <Text style={styles.serviceType}>{job.service_type_name}</Text>}
      {job.description && <Text style={styles.desc} numberOfLines={2}>{job.description}</Text>}
      <View style={styles.footerRow}>
        {job.address && (
          <View style={styles.addressRow}>
            <Ionicons name="location-outline" size={13} color={Colors.textMuted} />
            <Text style={styles.addressText} numberOfLines={1}>{job.address}</Text>
          </View>
        )}
        {job.scheduled_date && (
          <View style={styles.dateRow}>
            <Ionicons name="calendar-outline" size={13} color={isOverdue ? '#DC2626' : Colors.textMuted} />
            <Text style={[styles.dateText, isOverdue && { color: '#DC2626' }]}>
              {new Date(job.scheduled_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}
            </Text>
          </View>
        )}
      </View>
    </TouchableOpacity>
  );
}

// ── Screen ─────────────────────────────────────────────────────────────────
export default function TechFieldScreen() {
  const router = useRouter();

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['tech-field-jobs'],
    queryFn: techApi.fieldJobs,
  });

  const jobs = data?.jobs ?? [];
  const stats = data?.stats;

  return (
    <View style={styles.root}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Field Complaints</Text>
        <Text style={styles.headerSub}>Your assigned field jobs overview</Text>
      </View>

      {isLoading
        ? <ActivityIndicator style={{ marginTop: 40 }} color={Colors.primary} />
        : (
          <FlatList
            data={jobs}
            keyExtractor={j => String(j.id)}
            contentContainerStyle={{ padding: 12, paddingBottom: 80 }}
            refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} />}
            ListHeaderComponent={stats ? <StatGrid stats={stats} /> : null}
            renderItem={({ item }) => (
              <FieldJobItem
                job={item}
                onPress={() => router.push(`/(technician)/field/${item.id}` as any)}
              />
            )}
            ListEmptyComponent={
              <View style={styles.empty}>
                <Ionicons name="location-outline" size={56} color={Colors.textMuted} />
                <Text style={styles.emptyTitle}>No active field jobs</Text>
                <Text style={styles.emptyText}>Completed or unassigned jobs won't show here</Text>
              </View>
            }
          />
        )
      }
    </View>
  );
}

// ── Styles ─────────────────────────────────────────────────────────────────
const sg = StyleSheet.create({
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
    marginBottom: 14,
  },
  tile: {
    width: '30.5%',
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
    gap: 4,
    elevation: 1,
    shadowColor: '#000',
    shadowOpacity: 0.04,
    shadowRadius: 3,
  },
  iconWrap: {
    width: 34,
    height: 34,
    borderRadius: 10,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 2,
  },
  count: {
    fontSize: 22,
    fontWeight: '800',
    lineHeight: 26,
  },
  label: {
    fontSize: 11,
    fontWeight: '600',
    color: '#64748B',
    textAlign: 'center',
  },
});

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  header: { backgroundColor: Colors.primary, paddingHorizontal: 20, paddingTop: 18, paddingBottom: 18 },
  headerTitle: { fontSize: 20, fontWeight: '800', color: '#fff' },
  headerSub: { fontSize: 13, color: 'rgba(255,255,255,0.7)', marginTop: 2 },
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 14,
    marginBottom: 10,
    elevation: 2,
    shadowColor: '#000',
    shadowOpacity: 0.05,
    shadowRadius: 4,
    borderLeftWidth: 3,
    borderLeftColor: 'transparent',
  },
  cardOverdue: {
    borderLeftColor: '#DC2626',
  },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 },
  complaintNo: { fontSize: 13, fontWeight: '700', color: Colors.primary },
  badge: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 7, paddingVertical: 3, borderRadius: 6 },
  badgeText: { fontSize: 11, fontWeight: '700' },
  customerName: { fontSize: 15, fontWeight: '700', color: Colors.textPrimary },
  serviceType: { fontSize: 13, color: Colors.textSecondary, marginTop: 2 },
  desc: { fontSize: 13, color: Colors.textMuted, marginTop: 4, lineHeight: 18 },
  footerRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 8 },
  addressRow: { flexDirection: 'row', alignItems: 'center', gap: 4, flex: 1 },
  addressText: { fontSize: 12, color: Colors.textMuted, flex: 1 },
  dateRow: { flexDirection: 'row', alignItems: 'center', gap: 4, marginLeft: 8 },
  dateText: { fontSize: 12, color: Colors.textMuted },
  empty: { alignItems: 'center', marginTop: 40, gap: 8 },
  emptyTitle: { fontSize: 18, fontWeight: '700', color: Colors.textPrimary },
  emptyText: { fontSize: 14, color: Colors.textMuted, textAlign: 'center' },
});
