import React from 'react';
import {
  View, Text, StyleSheet, ScrollView, RefreshControl,
  TouchableOpacity, ActivityIndicator,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { adminApi, DashboardData, JobCard } from '../../lib/api';
import { useAuth } from '../../lib/auth';
import { Colors } from '../../lib/colors';

function StatCard({ icon, label, value, color, onPress }: {
  icon: string; label: string; value: number | string;
  color: string; onPress?: () => void;
}) {
  return (
    <TouchableOpacity style={[styles.statCard, { borderLeftColor: color }]} onPress={onPress} activeOpacity={0.8}>
      <View style={[styles.statIcon, { backgroundColor: color + '22' }]}>
        <Ionicons name={icon as any} size={20} color={color} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={styles.statValue}>{value}</Text>
        <Text style={styles.statLabel}>{label}</Text>
      </View>
    </TouchableOpacity>
  );
}

function JobRow({ job }: { job: JobCard }) {
  const sc = Colors.statusColors[job.status] ?? { bg: '#F1F5F9', text: '#475569' };
  return (
    <View style={styles.jobRow}>
      <View style={{ flex: 1 }}>
        <Text style={styles.jobOrderNo}>{job.order_no}</Text>
        <Text style={styles.jobCustomer}>{job.customer_name}</Text>
        <Text style={styles.jobDevice}>{job.device_name}</Text>
      </View>
      <View style={[styles.badge, { backgroundColor: sc.bg }]}>
        <Text style={[styles.badgeText, { color: sc.text }]}>{job.status}</Text>
      </View>
    </View>
  );
}

export default function DashboardScreen() {
  const { session, signOut } = useAuth();
  const router = useRouter();
  const shop = session?.role === 'admin' ? session.shop : null;
  const modules = shop?.modules ?? [];

  const { data, isLoading, refetch, isRefetching } = useQuery<DashboardData>({
    queryKey: ['admin-dashboard'],
    queryFn: adminApi.dashboard,
  });

  if (isLoading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={Colors.primary} />
      </View>
    );
  }

  const js = data?.job_stats;
  const fs = data?.field_stats;

  return (
    <ScrollView
      style={styles.root}
      refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} />}
    >
      <View style={styles.heroSection}>
        <View>
          <Text style={styles.heroShop}>{shop?.name}</Text>
          <Text style={styles.heroSub}>Hello, Admin · {shop?.code}</Text>
        </View>
        <TouchableOpacity onPress={signOut} style={styles.logoutBtn}>
          <Ionicons name="log-out-outline" size={22} color="#fff" />
        </TouchableOpacity>
      </View>

      {js && (
        <>
          <Text style={styles.sectionTitle}>Job Orders</Text>
          <View style={styles.statsGrid}>
            <StatCard icon="list-outline" label="Total" value={js.total} color={Colors.primary} />
            <StatCard icon="time-outline" label="Pending" value={js.pending} color={Colors.warning}
              onPress={() => router.push('/(admin)/jobs?status=Pending' as any)} />
            <StatCard icon="reload-outline" label="In Progress" value={js.in_progress} color={Colors.primaryLight}
              onPress={() => router.push('/(admin)/jobs?status=In Progress' as any)} />
            <StatCard icon="checkmark-circle-outline" label="Completed" value={js.completed} color={Colors.success}
              onPress={() => router.push('/(admin)/jobs?status=Completed' as any)} />
          </View>

          <View style={styles.revenueRow}>
            <View style={styles.revenueCard}>
              <Text style={styles.revenueLabel}>Today's Revenue</Text>
              <Text style={styles.revenueValue}>LKR {Number(js.revenue_today).toLocaleString()}</Text>
            </View>
            <View style={styles.revenueCard}>
              <Text style={styles.revenueLabel}>Total Revenue</Text>
              <Text style={styles.revenueValue}>LKR {Number(js.revenue_total).toLocaleString()}</Text>
            </View>
          </View>
        </>
      )}

      {fs && modules.includes('field_services') && (
        <>
          <Text style={styles.sectionTitle}>Field Services</Text>
          <View style={styles.statsGrid}>
            <StatCard icon="location-outline" label="Total" value={fs.total} color={Colors.info} />
            <StatCard icon="time-outline" label="Pending" value={fs.pending} color={Colors.warning} />
            <StatCard icon="reload-outline" label="In Progress" value={fs.in_progress} color={Colors.primaryLight} />
            <StatCard icon="checkmark-done-outline" label="Completed" value={fs.completed} color={Colors.success} />
          </View>
        </>
      )}

      {data?.recent_jobs && data.recent_jobs.length > 0 && (
        <>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Recent Jobs</Text>
            <TouchableOpacity onPress={() => router.push('/(admin)/jobs' as any)}>
              <Text style={styles.seeAll}>See All</Text>
            </TouchableOpacity>
          </View>
          <View style={styles.recentCard}>
            {data.recent_jobs.slice(0, 5).map((job, i) => (
              <React.Fragment key={job.id}>
                {i > 0 && <View style={styles.divider} />}
                <TouchableOpacity onPress={() => router.push(`/(admin)/jobs/${job.id}` as any)}>
                  <JobRow job={job} />
                </TouchableOpacity>
              </React.Fragment>
            ))}
          </View>
        </>
      )}

      <View style={{ height: 24 }} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },

  heroSection: {
    backgroundColor: Colors.primary,
    padding: 20,
    paddingTop: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  heroShop: { fontSize: 20, fontWeight: '800', color: '#fff' },
  heroSub: { fontSize: 13, color: 'rgba(255,255,255,0.7)', marginTop: 2 },
  logoutBtn: {
    backgroundColor: 'rgba(255,255,255,0.2)', padding: 8, borderRadius: 8,
  },

  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 16, marginTop: 20, marginBottom: 8 },
  sectionTitle: { fontSize: 15, fontWeight: '700', color: Colors.textPrimary, paddingHorizontal: 16, marginTop: 20, marginBottom: 8 },
  seeAll: { fontSize: 13, color: Colors.primary, fontWeight: '600' },

  statsGrid: { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: 12, gap: 8 },
  statCard: {
    width: '47%', backgroundColor: '#fff', borderRadius: 12, padding: 14,
    flexDirection: 'row', alignItems: 'center', gap: 10,
    borderLeftWidth: 4,
    shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  statIcon: { width: 36, height: 36, borderRadius: 8, justifyContent: 'center', alignItems: 'center' },
  statValue: { fontSize: 22, fontWeight: '800', color: Colors.textPrimary },
  statLabel: { fontSize: 12, color: Colors.textSecondary, marginTop: 1 },

  revenueRow: { flexDirection: 'row', paddingHorizontal: 12, gap: 8, marginTop: 8 },
  revenueCard: {
    flex: 1, backgroundColor: Colors.primaryDark, borderRadius: 12, padding: 14,
  },
  revenueLabel: { fontSize: 11, color: 'rgba(255,255,255,0.7)' },
  revenueValue: { fontSize: 18, fontWeight: '800', color: '#fff', marginTop: 4 },

  recentCard: {
    marginHorizontal: 16, backgroundColor: '#fff', borderRadius: 12,
    shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  jobRow: { flexDirection: 'row', alignItems: 'center', padding: 14 },
  jobOrderNo: { fontSize: 13, fontWeight: '700', color: Colors.primary },
  jobCustomer: { fontSize: 14, fontWeight: '600', color: Colors.textPrimary, marginTop: 1 },
  jobDevice: { fontSize: 12, color: Colors.textSecondary, marginTop: 1 },
  badge: { paddingHorizontal: 8, paddingVertical: 4, borderRadius: 6 },
  badgeText: { fontSize: 11, fontWeight: '700' },
  divider: { height: 1, backgroundColor: Colors.border, marginHorizontal: 14 },
});
