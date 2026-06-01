import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../../lib/auth';
import { techApi } from '../../lib/api';
import { Colors } from '../../lib/colors';
import { useRouter } from 'expo-router';
import { useToast } from '../../components/Toast';

export default function TechProfileScreen() {
  const { session, signOut } = useAuth();
  const router = useRouter();
  const { showToast, ToastHost } = useToast();
  const emp = session?.role === 'technician' ? session.employee : null;

  function handleLogout() {
    showToast(
      'Sign Out',
      'Are you sure you want to sign out?',
      'warning',
      async () => {
        try { await techApi.logout(); } catch {}
        await signOut();
        router.replace('/login');
      },
      'Sign Out',
    );
  }

  return (
    <View style={styles.root}>
      <View style={styles.profileCard}>
        <View style={styles.avatar}>
          <Ionicons name="person" size={40} color={Colors.primary} />
        </View>
        <Text style={styles.name}>{emp?.name ?? '—'}</Text>
        <Text style={styles.username}>@{emp?.user_name}</Text>
        <View style={styles.rolePill}>
          <Text style={styles.roleText}>{emp?.role ?? 'Technician'}</Text>
        </View>
      </View>

      <View style={styles.infoCard}>
        <View style={styles.infoRow}>
          <Ionicons name="briefcase-outline" size={18} color={Colors.textSecondary} />
          <Text style={styles.infoLabel}>Type</Text>
          <Text style={styles.infoValue}>{emp?.type ?? '—'}</Text>
        </View>
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
  profileCard: { backgroundColor: Colors.primary, borderRadius: 16, padding: 24, alignItems: 'center', marginBottom: 16 },
  avatar: { backgroundColor: 'rgba(255,255,255,0.9)', borderRadius: 50, width: 80, height: 80, justifyContent: 'center', alignItems: 'center', marginBottom: 12 },
  name: { fontSize: 22, fontWeight: '800', color: '#fff' },
  username: { fontSize: 14, color: 'rgba(255,255,255,0.7)', marginTop: 4 },
  rolePill: { marginTop: 10, backgroundColor: 'rgba(255,255,255,0.2)', paddingHorizontal: 16, paddingVertical: 5, borderRadius: 20 },
  roleText: { color: '#fff', fontWeight: '600', fontSize: 13 },
  infoCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 16 },
  infoRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  infoLabel: { fontSize: 14, color: Colors.textSecondary, flex: 1 },
  infoValue: { fontSize: 14, fontWeight: '600', color: Colors.textPrimary },
  logoutBtn: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, backgroundColor: '#fff', borderRadius: 12, padding: 16, borderWidth: 1.5, borderColor: Colors.danger + '44' },
  logoutText: { fontSize: 15, fontWeight: '700', color: Colors.danger },
  version: { textAlign: 'center', color: Colors.textMuted, fontSize: 12, marginTop: 20 },
});
