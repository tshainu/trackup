import React, { useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity, ScrollView,
  Modal, TextInput, ActivityIndicator, KeyboardAvoidingView, Platform,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../../lib/auth';
import { techApi } from '../../lib/api';
import { Colors } from '../../lib/colors';
import { useRouter } from 'expo-router';
import { useToast } from '../../components/Toast';

// ── Change Password Modal ──────────────────────────────────────────────────
function ChangePasswordModal({ visible, onClose }: { visible: boolean; onClose: () => void }) {
  const [current, setCurrent]     = useState('');
  const [next, setNext]           = useState('');
  const [confirm, setConfirm]     = useState('');
  const [loading, setLoading]     = useState(false);
  const [error, setError]         = useState('');
  const [success, setSuccess]     = useState(false);
  const [showCurrent, setShowCurrent] = useState(false);
  const [showNext, setShowNext]       = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  function reset() {
    setCurrent(''); setNext(''); setConfirm('');
    setError(''); setSuccess(false); setLoading(false);
  }

  function handleClose() { reset(); onClose(); }

  async function handleSubmit() {
    setError('');
    if (!current || !next || !confirm) { setError('All fields are required.'); return; }
    if (next.length < 6) { setError('New password must be at least 6 characters.'); return; }
    if (next !== confirm) { setError('New passwords do not match.'); return; }
    setLoading(true);
    try {
      await techApi.changePassword(current, next);
      setSuccess(true);
    } catch (e: any) {
      setError(e?.message ?? 'Failed to change password. Check your current password.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Modal visible={visible} animationType="slide" transparent onRequestClose={handleClose}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={pw.overlay}>
        <View style={pw.sheet}>
          <View style={pw.header}>
            <Text style={pw.title}>Change Password</Text>
            <TouchableOpacity onPress={handleClose} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          {success ? (
            <View style={pw.successBox}>
              <Ionicons name="checkmark-circle" size={52} color="#22C55E" />
              <Text style={pw.successTitle}>Password Changed!</Text>
              <Text style={pw.successSub}>Your password has been updated successfully.</Text>
              <TouchableOpacity style={pw.doneBtn} onPress={handleClose}>
                <Text style={pw.doneBtnText}>Done</Text>
              </TouchableOpacity>
            </View>
          ) : (
            <>
              <PasswordField
                label="Current Password" value={current} onChange={setCurrent}
                show={showCurrent} onToggle={() => setShowCurrent(v => !v)}
              />
              <PasswordField
                label="New Password" value={next} onChange={setNext}
                show={showNext} onToggle={() => setShowNext(v => !v)}
              />
              <PasswordField
                label="Confirm New Password" value={confirm} onChange={setConfirm}
                show={showConfirm} onToggle={() => setShowConfirm(v => !v)}
              />

              {!!error && (
                <View style={pw.errorBox}>
                  <Ionicons name="alert-circle-outline" size={15} color="#DC2626" />
                  <Text style={pw.errorText}>{error}</Text>
                </View>
              )}

              <TouchableOpacity
                style={[pw.submitBtn, loading && { opacity: 0.6 }]}
                onPress={handleSubmit}
                disabled={loading}
              >
                {loading
                  ? <ActivityIndicator color="#fff" size="small" />
                  : <Text style={pw.submitText}>Update Password</Text>
                }
              </TouchableOpacity>
            </>
          )}
        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
}

function PasswordField({
  label, value, onChange, show, onToggle,
}: { label: string; value: string; onChange: (v: string) => void; show: boolean; onToggle: () => void }) {
  return (
    <View style={pw.fieldWrap}>
      <Text style={pw.fieldLabel}>{label}</Text>
      <View style={pw.inputRow}>
        <TextInput
          style={pw.input}
          value={value}
          onChangeText={onChange}
          secureTextEntry={!show}
          placeholder="••••••••"
          placeholderTextColor="#CBD5E1"
          autoCapitalize="none"
        />
        <TouchableOpacity onPress={onToggle} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
          <Ionicons name={show ? 'eye-off-outline' : 'eye-outline'} size={20} color={Colors.textMuted} />
        </TouchableOpacity>
      </View>
    </View>
  );
}

// ── Menu row ───────────────────────────────────────────────────────────────
function MenuRow({
  icon, label, sublabel, onPress, danger = false, iconBg,
}: {
  icon: keyof typeof Ionicons.glyphMap;
  label: string;
  sublabel?: string;
  onPress?: () => void;
  danger?: boolean;
  iconBg?: string;
}) {
  return (
    <TouchableOpacity style={styles.menuRow} onPress={onPress} activeOpacity={onPress ? 0.7 : 1}>
      <View style={[styles.menuIconWrap, { backgroundColor: iconBg ?? (danger ? '#FEF2F2' : '#F1F5F9') }]}>
        <Ionicons name={icon} size={18} color={danger ? Colors.danger : Colors.primary} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={[styles.menuLabel, danger && { color: Colors.danger }]}>{label}</Text>
        {sublabel && <Text style={styles.menuSub}>{sublabel}</Text>}
      </View>
      {onPress && <Ionicons name="chevron-forward" size={16} color="#CBD5E1" />}
    </TouchableOpacity>
  );
}

// ── Screen ─────────────────────────────────────────────────────────────────
export default function TechProfileScreen() {
  const { session, signOut } = useAuth();
  const router = useRouter();
  const { showToast, ToastHost } = useToast();
  const [pwModal, setPwModal] = useState(false);

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
    <ScrollView style={styles.root} contentContainerStyle={{ paddingBottom: 40 }}>
      {/* Profile card */}
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

      {/* Info */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Details</Text>
        <View style={styles.card}>
          <MenuRow icon="briefcase-outline" iconBg="#EFF6FF" label="Type" sublabel={emp?.type ?? '—'} />
          {emp?.phone && <MenuRow icon="call-outline" iconBg="#F0FDF4" label="Phone" sublabel={emp.phone} />}
          {emp?.email && <MenuRow icon="mail-outline" iconBg="#FFF7ED" label="Email" sublabel={emp.email} />}
        </View>
      </View>

      {/* Account */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Account</Text>
        <View style={styles.card}>
          <MenuRow
            icon="lock-closed-outline"
            iconBg="#F5F3FF"
            label="Change Password"
            sublabel="Update your login password"
            onPress={() => setPwModal(true)}
          />
          <View style={styles.divider} />
          <MenuRow
            icon="notifications-outline"
            iconBg="#FFF7ED"
            label="Notifications"
            sublabel="Job alerts and reminders"
          />
        </View>
      </View>

      {/* App info */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>App</Text>
        <View style={styles.card}>
          <MenuRow icon="information-circle-outline" iconBg="#F0FDF4" label="Version" sublabel="TrackUp Mobile v1.0" />
          <View style={styles.divider} />
          <MenuRow icon="shield-checkmark-outline" iconBg="#EFF6FF" label="Privacy Policy" />
        </View>
      </View>

      {/* Sign out */}
      <View style={styles.section}>
        <View style={styles.card}>
          <MenuRow icon="log-out-outline" label="Sign Out" danger onPress={handleLogout} />
        </View>
      </View>

      <ChangePasswordModal visible={pwModal} onClose={() => setPwModal(false)} />
      <ToastHost />
    </ScrollView>
  );
}

// ── Styles ─────────────────────────────────────────────────────────────────
const pw = StyleSheet.create({
  overlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)', justifyContent: 'flex-end' },
  sheet: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    padding: 24,
    paddingBottom: 36,
  },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  title: { fontSize: 18, fontWeight: '800', color: Colors.textPrimary },
  fieldWrap: { marginBottom: 14 },
  fieldLabel: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary, marginBottom: 6 },
  inputRow: {
    flexDirection: 'row', alignItems: 'center',
    borderWidth: 1.5, borderColor: '#E2E8F0', borderRadius: 10,
    paddingHorizontal: 14, paddingVertical: 12, backgroundColor: '#F8FAFC',
  },
  input: { flex: 1, fontSize: 15, color: Colors.textPrimary },
  errorBox: { flexDirection: 'row', alignItems: 'center', gap: 6, backgroundColor: '#FEF2F2', borderRadius: 8, padding: 10, marginBottom: 12 },
  errorText: { fontSize: 13, color: '#DC2626', flex: 1 },
  submitBtn: { backgroundColor: Colors.primary, borderRadius: 12, padding: 15, alignItems: 'center', marginTop: 4 },
  submitText: { color: '#fff', fontWeight: '700', fontSize: 15 },
  successBox: { alignItems: 'center', paddingVertical: 20, gap: 10 },
  successTitle: { fontSize: 20, fontWeight: '800', color: Colors.textPrimary },
  successSub: { fontSize: 14, color: Colors.textSecondary, textAlign: 'center' },
  doneBtn: { marginTop: 8, backgroundColor: '#22C55E', borderRadius: 12, paddingHorizontal: 32, paddingVertical: 13 },
  doneBtnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
});

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  profileCard: {
    backgroundColor: Colors.primary,
    margin: 16,
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
  },
  avatar: {
    backgroundColor: 'rgba(255,255,255,0.9)',
    borderRadius: 50, width: 80, height: 80,
    justifyContent: 'center', alignItems: 'center', marginBottom: 12,
  },
  name: { fontSize: 22, fontWeight: '800', color: '#fff' },
  username: { fontSize: 14, color: 'rgba(255,255,255,0.7)', marginTop: 4 },
  rolePill: {
    marginTop: 10, backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: 16, paddingVertical: 5, borderRadius: 20,
  },
  roleText: { color: '#fff', fontWeight: '600', fontSize: 13 },
  section: { paddingHorizontal: 16, marginBottom: 12 },
  sectionTitle: { fontSize: 12, fontWeight: '700', color: Colors.textMuted, textTransform: 'uppercase', letterSpacing: 0.8, marginBottom: 8, marginLeft: 4 },
  card: { backgroundColor: '#fff', borderRadius: 14, overflow: 'hidden', elevation: 1, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 4 },
  menuRow: { flexDirection: 'row', alignItems: 'center', padding: 14, gap: 12 },
  menuIconWrap: { width: 36, height: 36, borderRadius: 10, alignItems: 'center', justifyContent: 'center' },
  menuLabel: { fontSize: 14, fontWeight: '600', color: Colors.textPrimary },
  menuSub: { fontSize: 12, color: Colors.textMuted, marginTop: 1 },
  divider: { height: 1, backgroundColor: '#F1F5F9', marginLeft: 62 },
});
