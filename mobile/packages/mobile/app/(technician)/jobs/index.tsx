import React from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  ActivityIndicator, RefreshControl,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { techApi, JobCard, JobStats } from '../../../lib/api';
import { useAuth } from '../../../lib/auth';
import { Colors } from '../../../lib/colors';
import { useJobNotifications } from '../../../lib/useJobNotifications';

function StatPill({ label, count, color }: { label: string; count: number; color: string }) {
  return (
    <View style={[styles.statPill, { backgroundColor: color + '22', borderColor: color + '44' }]}>
      <Text style={[styles.statCount, { color }]}>{count}</Text>
      <Text style={[styles.statLabel, { color }]}>{label}</Text>
    </View>
  );
}

function JobItem({ job, onPress }: { job: JobCard; onPress: () => void }) {
  const sc = Colors.statusColors[job.status] ?? { bg: '#F1F5F9', text: '#475569' };
  return (
    <TouchableOpacity style={styles.jobCard} onPress={onPress} activeOpacity={0.75}>
      <View style={styles.jobHeader}>
        <Text style={styles.orderNo}>{job.order_no}</Text>
        <View style={[styles.badge, { backgroundColor: sc.bg }]}>
          <Text style={[styles.badgeText, { color: sc.text }]}>{job.status}</Text>
        </View>
      </View>
      <Text style={styles.customerName}>{job.customer_name}</Text>
      <Text style={styles.deviceText}>{job.device_name}{job.device_brand ? ` · ${job.device_brand}` : ''}</Text>
      {job.device_fault && <Text style={styles.faultText} numberOfLines={1}>{job.device_fault}</Text>}
      <View style={styles.jobFooter}>
        <View style={styles.footerItem}>
          <Ionicons name="calendar-outline" size={12} color={Colors.textMuted} />
          <Text style={styles.footerText}>{job.date?.split('T')[0]}</Text>
        </View>
        {job.need_assistant && (
          <View style={[styles.badge, { backgroundColor: '#FEF3C7' }]}>
            <Text style={[styles.badgeText, { color: '#92400E' }]}>Assist Requested</Text>
          </View>
        )}
      </View>
    </TouchableOpacity>
  );
}

export default function TechJobsScreen() {
  const { session } = useAuth();
  const emp = session?.role === 'technician' ? session.employee : null;
  const router = useRouter();
  const insets = useSafeAreaInsets();

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['tech-jobs'],
    queryFn: techApi.jobs,
  });

  // Poll for new jobs and fire notifications when new ones arrive
  useJobNotifications(() => refetch());

  const jobs = data?.jobs ?? [];
  const stats = data?.stats;

  return (
    <View style={styles.root}>
      {/* Stats */}
      {stats && (
        <View style={styles.statsRow}>
          <StatPill label="Total" count={stats.total} color={Colors.primary} />
          <StatPill label="Pending" count={stats.pending} color={Colors.warning} />
          <StatPill label="In Progress" count={stats.in_progress} color={Colors.primaryLight} />
          <StatPill label="Done" count={stats.completed} color={Colors.success} />
        </View>
      )}

      <Text style={styles.sectionTitle}>Active Jobs</Text>

      {isLoading
        ? <ActivityIndicator style={{ marginTop: 40 }} color={Colors.primary} />
        : (
          <FlatList
            data={jobs}
            keyExtractor={j => String(j.id)}
            contentContainerStyle={[
              styles.listContent,
              { paddingBottom: insets.bottom + 80 }, // clears tab bar
            ]}
            refreshControl={
              <RefreshControl
                refreshing={isRefetching}
                onRefresh={refetch}
                colors={[Colors.primary]}
                tintColor={Colors.primary}
              />
            }
            renderItem={({ item }) => (
              <JobItem job={item} onPress={() => router.push(`/(technician)/jobs/${item.id}` as any)} />
            )}
            ListEmptyComponent={
              <View style={styles.empty}>
                <Ionicons name="checkmark-done-circle-outline" size={56} color={Colors.success} />
                <Text style={styles.emptyTitle}>All clear!</Text>
                <Text style={styles.emptyText}>No active jobs assigned to you</Text>
              </View>
            }
          />
        )
      }
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  statsRow: { flexDirection: 'row', padding: 12, gap: 8 },
  statPill: { flex: 1, borderRadius: 10, padding: 10, alignItems: 'center', borderWidth: 1 },
  statCount: { fontSize: 20, fontWeight: '800' },
  statLabel: { fontSize: 11, fontWeight: '600', marginTop: 1 },

  sectionTitle: { fontSize: 15, fontWeight: '700', color: Colors.textPrimary, paddingHorizontal: 16, marginBottom: 4 },

  listContent: { padding: 12 },

  jobCard: { backgroundColor: '#fff', borderRadius: 12, padding: 14, marginBottom: 10, elevation: 2, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 4 },
  jobHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 },
  orderNo: { fontSize: 13, fontWeight: '700', color: Colors.primary },
  badge: { paddingHorizontal: 7, paddingVertical: 3, borderRadius: 6 },
  badgeText: { fontSize: 11, fontWeight: '700' },
  customerName: { fontSize: 15, fontWeight: '700', color: Colors.textPrimary },
  deviceText: { fontSize: 13, color: Colors.textSecondary, marginTop: 2 },
  faultText: { fontSize: 12, color: Colors.textMuted, marginTop: 3, fontStyle: 'italic' },
  jobFooter: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 8 },
  footerItem: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  footerText: { fontSize: 12, color: Colors.textMuted },

  empty: { alignItems: 'center', marginTop: 60, gap: 8 },
  emptyTitle: { fontSize: 18, fontWeight: '700', color: Colors.textPrimary },
  emptyText: { fontSize: 14, color: Colors.textMuted },
});
