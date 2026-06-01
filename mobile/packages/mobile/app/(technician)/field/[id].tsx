import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  ActivityIndicator, Modal, TextInput,
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams } from 'expo-router';
import * as Location from 'expo-location';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { techApi } from '../../../lib/api';
import { Colors } from '../../../lib/colors';
import { useToast } from '../../../components/Toast';

const ORANGE = '#E8490F';

function InfoRow({ label, value }: { label: string; value?: string | number | null }) {
  if (!value && value !== 0) return null;
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{String(value)}</Text>
    </View>
  );
}

type ModalType = 'complete' | 'extend' | 'cant' | null;

export default function TechFieldDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const qc = useQueryClient();
  const insets = useSafeAreaInsets();
  const { showToast, ToastHost } = useToast();

  const [activeModal, setActiveModal] = useState<ModalType>(null);
  const [notes, setNotes] = useState('');
  const [gpsLoading, setGpsLoading] = useState(false);

  const { data, isLoading } = useQuery({
    queryKey: ['tech-field-job', id],
    queryFn: () => techApi.fieldJobDetail(Number(id)),
  });

  const acceptMutation = useMutation({
    mutationFn: () => techApi.acceptFieldJob(Number(id)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-field-job', id] });
      qc.invalidateQueries({ queryKey: ['tech-field-jobs'] });
      showToast('Job Accepted', 'Field job is now In Progress', 'success');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  const completeMutation = useMutation({
    mutationFn: () => techApi.completeFieldJob(Number(id), notes),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-field-job', id] });
      qc.invalidateQueries({ queryKey: ['tech-field-jobs'] });
      setActiveModal(null);
      showToast('Job Completed', 'Field job has been marked as completed', 'success');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  const extendMutation = useMutation({
    mutationFn: () => techApi.extendFieldJob(Number(id), notes),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-field-job', id] });
      setActiveModal(null);
      showToast('Request Submitted', 'Extension request sent to admin', 'info');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  const cantMutation = useMutation({
    mutationFn: () => techApi.cantCompleteFieldJob(Number(id), notes),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-field-job', id] });
      qc.invalidateQueries({ queryKey: ['tech-field-jobs'] });
      setActiveModal(null);
      showToast('On Hold', 'Job has been marked as On Hold', 'warning');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  async function handleGetGps() {
    setGpsLoading(true);
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        showToast('Permission Denied', 'Location permission is required to capture GPS.', 'error');
        return;
      }

      // Use watchPositionAsync to get a FRESH GPS fix, not cached/network location
      // Resolves on first reading with accuracy <= 50m, or after 10 readings whichever first
      const loc = await new Promise<Location.LocationObject>((resolve, reject) => {
        let attempts = 0;
        let sub: Location.LocationSubscription | null = null;
        const timeout = setTimeout(() => {
          sub?.remove();
          reject(new Error('GPS timeout — move to open sky and try again'));
        }, 20000);

        Location.watchPositionAsync(
          {
            accuracy: Location.Accuracy.BestForNavigation,
            timeInterval: 1000,
            distanceInterval: 0,
          },
          (position) => {
            attempts++;
            const acc = position.coords.accuracy ?? 999;
            // Accept if accuracy is good OR after 8 attempts (best we can get)
            if (acc <= 50 || attempts >= 8) {
              clearTimeout(timeout);
              sub?.remove();
              resolve(position);
            }
          },
        ).then((s) => { sub = s; }).catch(reject);
      });

      const { latitude, longitude, accuracy } = loc.coords;

      // Warn if accuracy is still bad
      if (accuracy && accuracy > 200) {
        showToast(
          'Low GPS Accuracy',
          `Signal weak (±${Math.round(accuracy)}m). Move outdoors and try again.`,
          'warning',
        );
        return;
      }

      const accuracyText = accuracy ? ` (±${Math.round(accuracy)}m)` : '';
      showToast(
        'Confirm Location',
        `Save these coordinates${accuracyText}?\n${latitude.toFixed(6)}, ${longitude.toFixed(6)}`,
        'gps',
        async () => {
          try {
            await techApi.updateGps(Number(id), latitude, longitude, 'Customer Location');
            qc.invalidateQueries({ queryKey: ['tech-field-job', id] });
            showToast('GPS Saved', 'Customer location updated successfully', 'success');
          } catch (e: any) {
            showToast('Save Failed', e.message ?? 'Could not save location', 'error');
          }
        },
        'Save Location',
      );
    } catch (e: any) {
      showToast('Location Error', e.message ?? 'Failed to get location', 'error');
    } finally {
      setGpsLoading(false);
    }
  }

  if (isLoading) return <View style={styles.center}><ActivityIndicator size="large" color={ORANGE} /></View>;

  const job = data?.job;
  if (!job) return null;

  const sc = Colors.statusColors[job.status] ?? { bg: '#F1F5F9', text: '#475569' };
  const isAssigned   = job.status === 'Assigned';
  const isInProgress = job.status === 'In Progress';
  const canUpdateGps = isAssigned || isInProgress;

  const modalPending = completeMutation.isPending || extendMutation.isPending || cantMutation.isPending;

  const MODAL_CONFIG: Record<string, { title: string; placeholder: string; btnLabel: string; btnColor: string; onConfirm: () => void }> = {
    complete: {
      title: 'Complete Job',
      placeholder: 'Completion notes (optional)...',
      btnLabel: 'Mark as Completed',
      btnColor: Colors.success,
      onConfirm: () => completeMutation.mutate(),
    },
    extend: {
      title: 'Request Extension',
      placeholder: 'Reason for extension...',
      btnLabel: 'Submit Request',
      btnColor: Colors.warning,
      onConfirm: () => extendMutation.mutate(),
    },
    cant: {
      title: "Can't Complete",
      placeholder: 'Reason why you cannot complete...',
      btnLabel: 'Mark On Hold',
      btnColor: Colors.danger,
      onConfirm: () => cantMutation.mutate(),
    },
  };

  const modalCfg = activeModal ? MODAL_CONFIG[activeModal] : null;

  return (
    <>
      <ScrollView style={styles.root} contentContainerStyle={{ paddingBottom: insets.bottom + 100 }}>
        <View style={styles.headerCard}>
          <View style={{ flex: 1 }}>
            <Text style={styles.complaintNo}>{job.complaint_no}</Text>
            <Text style={styles.customerName}>{job.customer_name}</Text>
            <Text style={styles.phone}>{job.phone_no}</Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: sc.bg }]}>
            <Text style={[styles.statusText, { color: sc.text }]}>{job.status}</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Service</Text>
          <InfoRow label="Type"        value={job.service_type_name} />
          <InfoRow label="Description" value={job.description} />
          <InfoRow label="Priority"    value={job.priority} />
          <InfoRow label="Scheduled"   value={job.scheduled_date?.split('T')[0]} />
        </View>

        {/* Location section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Customer Location</Text>
          <InfoRow label="Address" value={job.address} />

          {job.gps_lat && job.gps_lng ? (
            <View style={styles.gpsExisting}>
              <Ionicons name="location" size={16} color={Colors.success} />
              <Text style={styles.gpsCoords}>{Number(job.gps_lat).toFixed(6)}, {Number(job.gps_lng).toFixed(6)}</Text>
              <View style={styles.gpsSavedBadge}>
                <Text style={styles.gpsSavedText}>Saved</Text>
              </View>
            </View>
          ) : (
            <Text style={styles.noGps}>No GPS location saved yet</Text>
          )}

          {canUpdateGps && (
            <TouchableOpacity
              style={styles.gpsBtn}
              onPress={handleGetGps}
              disabled={gpsLoading}
              activeOpacity={0.8}
            >
              {gpsLoading ? (
                <ActivityIndicator color="#fff" size="small" />
              ) : (
                <Ionicons name="navigate" size={18} color="#fff" />
              )}
              <Text style={styles.gpsBtnText}>
                {gpsLoading ? 'Getting location...' : job.gps_lat ? 'Update GPS Location' : 'Get GPS Location'}
              </Text>
            </TouchableOpacity>
          )}
        </View>

        {job.completion_notes && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Notes</Text>
            <Text style={styles.notes}>{job.completion_notes}</Text>
          </View>
        )}
      </ScrollView>

      {/* Action bar */}
      <View style={[styles.actionBar, { paddingBottom: insets.bottom + 8 }]}>
        {isAssigned && (
          <TouchableOpacity style={styles.actionBtn} onPress={() => acceptMutation.mutate()} disabled={acceptMutation.isPending}>
            {acceptMutation.isPending ? <ActivityIndicator color="#fff" /> : <>
              <Ionicons name="play-circle-outline" size={20} color="#fff" />
              <Text style={styles.actionBtnText}>Accept</Text>
            </>}
          </TouchableOpacity>
        )}

        {isInProgress && (
          <>
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: Colors.danger, flex: 0, paddingHorizontal: 14 }]}
              onPress={() => { setNotes(''); setActiveModal('cant'); }}
            >
              <Ionicons name="close-circle-outline" size={20} color="#fff" />
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: Colors.warning, flex: 0, paddingHorizontal: 14 }]}
              onPress={() => { setNotes(''); setActiveModal('extend'); }}
            >
              <Ionicons name="time-outline" size={20} color="#fff" />
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: Colors.success }]}
              onPress={() => { setNotes(''); setActiveModal('complete'); }}
            >
              <Ionicons name="checkmark-circle-outline" size={20} color="#fff" />
              <Text style={styles.actionBtnText}>Complete</Text>
            </TouchableOpacity>
          </>
        )}
      </View>

      {/* Generic bottom sheet modal */}
      <Modal visible={!!activeModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            {modalCfg && (
              <>
                <View style={styles.modalHeader}>
                  <Text style={styles.modalTitle}>{modalCfg.title}</Text>
                  <TouchableOpacity onPress={() => setActiveModal(null)}>
                    <Ionicons name="close" size={22} color={Colors.textPrimary} />
                  </TouchableOpacity>
                </View>
                <TextInput
                  style={styles.notesInput}
                  placeholder={modalCfg.placeholder}
                  placeholderTextColor={Colors.textMuted}
                  value={notes}
                  onChangeText={setNotes}
                  multiline
                />
                <TouchableOpacity
                  style={[styles.confirmBtn, { backgroundColor: modalCfg.btnColor }]}
                  onPress={modalCfg.onConfirm}
                  disabled={modalPending}
                >
                  {modalPending ? <ActivityIndicator color="#fff" /> : <Text style={styles.confirmBtnText}>{modalCfg.btnLabel}</Text>}
                </TouchableOpacity>
              </>
            )}
          </View>
        </View>
      </Modal>

      {/* Themed toast — always last so it sits on top */}
      <ToastHost />
    </>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  headerCard: { backgroundColor: ORANGE, padding: 20, flexDirection: 'row', alignItems: 'flex-start', gap: 12 },
  complaintNo: { fontSize: 13, fontWeight: '700', color: 'rgba(255,255,255,0.7)' },
  customerName: { fontSize: 20, fontWeight: '800', color: '#fff', marginTop: 2 },
  phone: { fontSize: 14, color: 'rgba(255,255,255,0.8)', marginTop: 2 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8 },
  statusText: { fontSize: 12, fontWeight: '700' },

  section: { backgroundColor: '#fff', margin: 12, borderRadius: 12, padding: 14 },
  sectionTitle: { fontSize: 13, fontWeight: '700', color: Colors.textSecondary, marginBottom: 10, textTransform: 'uppercase', letterSpacing: 0.5 },
  infoRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  infoLabel: { fontSize: 13, color: Colors.textSecondary },
  infoValue: { fontSize: 13, fontWeight: '600', color: Colors.textPrimary, maxWidth: '60%', textAlign: 'right' },

  noGps: { fontSize: 13, color: Colors.textMuted, fontStyle: 'italic', marginBottom: 12 },
  gpsExisting: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 12, flexWrap: 'wrap' },
  gpsCoords: { fontSize: 13, color: Colors.textPrimary, fontWeight: '600', flex: 1 },
  gpsSavedBadge: { backgroundColor: Colors.success + '22', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6 },
  gpsSavedText: { fontSize: 11, color: Colors.success, fontWeight: '700' },

  gpsBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
    backgroundColor: ORANGE, borderRadius: 10, paddingVertical: 12, marginTop: 4,
  },
  gpsBtnText: { color: '#fff', fontWeight: '700', fontSize: 14 },

  notes: { fontSize: 14, color: Colors.textPrimary, lineHeight: 20 },

  actionBar: {
    position: 'absolute', bottom: 0, left: 0, right: 0,
    backgroundColor: '#fff', paddingHorizontal: 12, paddingTop: 10,
    flexDirection: 'row', gap: 8,
    borderTopWidth: 1, borderTopColor: Colors.border,
  },
  actionBtn: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, backgroundColor: Colors.primary, paddingVertical: 12, borderRadius: 10 },
  actionBtnText: { color: '#fff', fontWeight: '700', fontSize: 14 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalTitle: { fontSize: 17, fontWeight: '700', color: Colors.textPrimary },
  notesInput: { borderWidth: 1, borderColor: Colors.border, borderRadius: 8, padding: 10, fontSize: 14, minHeight: 80, textAlignVertical: 'top', marginBottom: 12 },
  confirmBtn: { padding: 14, borderRadius: 10, alignItems: 'center' },
  confirmBtnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
});
