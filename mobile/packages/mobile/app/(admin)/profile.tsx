import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../../lib/auth';
import { adminApi } from '../../lib/api';
import { Colors } from '../../lib/colors';
import { useRouter } from 'expo-router';
import { useToast } from '../../components/Toast';

export default function ProfileScreen() {
  const { session, signOut } = useAuth();
  const router = useRouter();
  const { showToast, ToastHost } = useToast();
  const shop = session?.role === 'admin' ? session.shop : null;

  function handleLogout() {
    showToast(
      'Sign Out',
      'Are you sure you want to sign out?',
      'warning',
      async () => {
        try { await adminApi.logout(); } catch {}
        await signOut();
        router.replace('/login');
      },
      'Sign Out',
    );
  }

  return (
    <View style={styles.root}>
      <View style={styles.shopCard}>
        <View style={styles.shopIcon}>
          <Ionicons name="storefront" size={36} color={Colors.primary} />
        </View>
        <Text style={styles.shopName}>{shop?.name ?? '—'}</Text>
        <Text style={styles.shopCode}>Code: {shop?.code}</Text>
        <Text style={styles.shopRole}>Admin</Text>
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Active Modules</Text>
        {(shop?.modules ?? []).map(m => (
          <View key={m} style={styles.moduleRow}>
            <Ionicons name="checkmark-circle" size={18} color={Colors.success} />
            <Text style={styles.moduleText}>{m === 'job_orders' ? 'Job Orders' : 'Field Services'}</Text>
          </View>
        ))}
      </View>

      <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout}>
        <Ionicons name="log-out-outline" size={20} color={Colors.danger} />
        <Text style={styles.logoutText}>Sign Out</Text>
      </TouchableOpacity>

      <Text style={styles.version}>TrackUp Mobile v1.0</Text>

      <ToastHost />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg, padding: 16 },
  shopCard: { backgroundColor: Colors.primary, borderRadius: 16, padding: 24, alignItems: 'center', marginBottom: 16 },
  shopIcon: { backgroundColor: 'rgba(255,255,255,0.2)', borderRadius: 50, width: 72, height: 72, justifyContent: 'center', alignItems: 'center', marginBottom: 12 },
  shopName: { fontSize: 22, fontWeight: '800', color: '#fff' },
  shopCode: { fontSize: 14, color: 'rgba(255,255,255,0.7)', marginTop: 4 },
  shopRole: { marginTop: 8, backgroundColor: 'rgba(255,255,255,0.2)', paddingHorizontal: 12, paddingVertical: 4, borderRadius: 20 },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 16 },
  cardTitle: { fontSize: 14, fontWeight: '700', color: Colors.textSecondary, marginBottom: 12, textTransform: 'uppercase', letterSpacing: 0.5 },
  moduleRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 10 },
  moduleText: { fontSize: 15, color: Colors.textPrimary, fontWeight: '600' },
  logoutBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, backgroundColor: '#fff', borderRadius: 12, padding: 16, borderWidth: 1.5, borderColor: Colors.danger + '44' },
  logoutText: { fontSize: 15, fontWeight: '700', color: Colors.danger },
  version: { textAlign: 'center', color: Colors.textMuted, fontSize: 12, marginTop: 20 },
});
