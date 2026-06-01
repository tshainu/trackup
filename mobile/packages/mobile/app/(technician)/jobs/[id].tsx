import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  ActivityIndicator, Modal, TextInput,
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { techApi } from '../../../lib/api';
import { Colors } from '../../../lib/colors';
import { useToast } from '../../../components/Toast';

function InfoRow({ label, value }: { label: string; value?: string | number | null }) {
  if (!value && value !== 0) return null;
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{String(value)}</Text>
    </View>
  );
}

export default function TechJobDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const qc = useQueryClient();
  const router = useRouter();
  const { showToast, ToastHost } = useToast();

  const [completeModal, setCompleteModal] = useState(false);
  const [completeStatus, setCompleteStatus] = useState<'Completed' | 'Not Completed'>('Completed');
  const [remark, setRemark] = useState('');

  const { data, isLoading } = useQuery({
    queryKey: ['tech-job', id],
    queryFn: () => techApi.jobDetail(Number(id)),
  });

  const acceptMutation = useMutation({
    mutationFn: () => techApi.acceptJob(Number(id)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-job', id] });
      qc.invalidateQueries({ queryKey: ['tech-jobs'] });
      showToast('Job Accepted', 'Status set to In Progress', 'success');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  const completeMutation = useMutation({
    mutationFn: () => techApi.completeJob(Number(id), completeStatus, remark),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-job', id] });
      qc.invalidateQueries({ queryKey: ['tech-jobs'] });
      setCompleteModal(false);
      showToast(
        completeStatus === 'Completed' ? 'Job Completed' : 'Job Closed',
        `Marked as ${completeStatus}`,
        completeStatus === 'Completed' ? 'success' : 'warning',
      );
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  const assistMutation = useMutation({
    mutationFn: () => techApi.requestAssistance(Number(id)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['tech-job', id] });
      showToast('Assistance Requested', 'Admin has been notified', 'info');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  if (isLoading) return <View style={styles.center}><ActivityIndicator size="large" color={Colors.primary} /></View>;

  const job = data?.job;
  if (!job) return null;

  const sc = Colors.statusColors[job.status] ?? { bg: '#F1F5F9', text: '#475569' };
  const isPending    = job.status === 'Pending';
  const isInProgress = job.status === 'In Progress';

  return (
    <>
      <ScrollView style={styles.root} contentContainerStyle={{ paddingBottom: 120 }}>
        <View style={styles.headerCard}>
          <View style={{ flex: 1 }}>
            <Text style={styles.orderNo}>{job.order_no}</Text>
            <Text style={styles.customerName}>{job.customer_name}</Text>
            <Text style={styles.phone}>{job.phone_no}</Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: sc.bg }]}>
            <Text style={[styles.statusText, { color: sc.text }]}>{job.status}</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Device</Text>
          <InfoRow label="Device" value={job.device_name} />
          <InfoRow label="Brand" value={job.device_brand} />
          <InfoRow label="Serial No" value={job.serial_no} />
          <InfoRow label="Fault" value={job.device_fault} />
          <InfoRow label="Accessories" value={job.accessories} />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Timeline</Text>
          <InfoRow label="Received" value={job.date?.split('T')[0]} />
          <InfoRow label="Est. Delivery" value={job.estimated_delivery?.split('T')[0]} />
          <InfoRow label="Priority" value={job.priority} />
        </View>

        {job.remark && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Remark</Text>
            <Text style={styles.remark}>{job.remark}</Text>
          </View>
        )}

        {job.need_assistant && (
          <View style={styles.assistBadge}>
            <Ionicons name="warning" size={16} color="#92400E" />
            <Text style={styles.assistText}>Assistance Requested</Text>
          </View>
        )}
      </ScrollView>

      {/* Action bar */}
      <View style={styles.actionBar}>
        {isPending && (
          <TouchableOpacity
            style={styles.actionBtn}
            onPress={() => acceptMutation.mutate()}
            disabled={acceptMutation.isPending}
          >
            {acceptMutation.isPending
              ? <ActivityIndicator color="#fff" />
              : <>
                  <Ionicons name="play-circle-outline" size={20} color="#fff" />
                  <Text style={styles.actionBtnText}>Accept Job</Text>
                </>
            }
          </TouchableOpacity>
        )}

        {isInProgress && (
          <>
            <TouchableOpacity
              style={[styles.actionBtn, { flex: 0, paddingHorizontal: 14, backgroundColor: Colors.warning }]}
              onPress={() => !job.need_assistant && assistMutation.mutate()}
              disabled={!!job.need_assistant || assistMutation.isPending}
            >
              <Ionicons name="hand-left-outline" size={20} color="#fff" />
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.actionBtn, { backgroundColor: Colors.success }]}
              onPress={() => setCompleteModal(true)}
            >
              <Ionicons name="checkmark-circle-outline" size={20} color="#fff" />
              <Text style={styles.actionBtnText}>Complete</Text>
            </TouchableOpacity>
          </>
        )}
      </View>

      {/* Complete modal */}
      <Modal visible={completeModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Complete Job</Text>
              <TouchableOpacity onPress={() => setCompleteModal(false)}>
                <Ionicons name="close" size={22} color={Colors.textPrimary} />
              </TouchableOpacity>
            </View>

            <Text style={styles.fieldLabel}>Outcome</Text>
            <View style={styles.segRow}>
              {(['Completed', 'Not Completed'] as const).map(s => {
                const color = s === 'Completed' ? Colors.success : Colors.danger;
                return (
                  <TouchableOpacity
                    key={s}
                    style={[styles.seg, completeStatus === s && { backgroundColor: color, borderColor: color }]}
                    onPress={() => setCompleteStatus(s)}
                  >
                    <Text style={[styles.segText, completeStatus === s && { color: '#fff' }]}>{s}</Text>
                  </TouchableOpacity>
                );
              })}
            </View>

            <Text style={[styles.fieldLabel, { marginTop: 12 }]}>Remarks (optional)</Text>
            <TextInput
              style={styles.remarkInput}
              placeholder="Add notes..."
              value={remark}
              onChangeText={setRemark}
              multiline
            />

            <TouchableOpacity
              style={[styles.confirmBtn, { backgroundColor: completeStatus === 'Completed' ? Colors.success : Colors.danger }]}
              onPress={() => completeMutation.mutate()}
              disabled={completeMutation.isPending}
            >
              {completeMutation.isPending
                ? <ActivityIndicator color="#fff" />
                : <Text style={styles.confirmBtnText}>Confirm — {completeStatus}</Text>
              }
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      <ToastHost />
    </>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  headerCard: { backgroundColor: Colors.primary, padding: 20, flexDirection: 'row', alignItems: 'flex-start', gap: 12 },
  orderNo: { fontSize: 13, fontWeight: '700', color: 'rgba(255,255,255,0.7)' },
  customerName: { fontSize: 20, fontWeight: '800', color: '#fff', marginTop: 2 },
  phone: { fontSize: 14, color: 'rgba(255,255,255,0.8)', marginTop: 2 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8 },
  statusText: { fontSize: 12, fontWeight: '700' },
  section: { backgroundColor: '#fff', margin: 12, borderRadius: 12, padding: 14 },
  sectionTitle: { fontSize: 13, fontWeight: '700', color: Colors.textSecondary, marginBottom: 10, textTransform: 'uppercase', letterSpacing: 0.5 },
  infoRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  infoLabel: { fontSize: 13, color: Colors.textSecondary },
  infoValue: { fontSize: 13, fontWeight: '600', color: Colors.textPrimary, maxWidth: '60%', textAlign: 'right' },
  remark: { fontSize: 14, color: Colors.textPrimary, lineHeight: 20 },
  assistBadge: { flexDirection: 'row', alignItems: 'center', gap: 8, backgroundColor: '#FEF3C7', margin: 12, padding: 12, borderRadius: 10 },
  assistText: { fontSize: 13, fontWeight: '600', color: '#92400E' },
  actionBar: { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: '#fff', padding: 12, flexDirection: 'row', gap: 8, borderTopWidth: 1, borderTopColor: Colors.border },
  actionBtn: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, backgroundColor: Colors.primary, paddingVertical: 12, borderRadius: 10 },
  actionBtnText: { color: '#fff', fontWeight: '700', fontSize: 14 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalTitle: { fontSize: 17, fontWeight: '700', color: Colors.textPrimary },
  fieldLabel: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary, marginBottom: 6 },
  segRow: { flexDirection: 'row', gap: 8 },
  seg: { flex: 1, padding: 10, borderRadius: 8, borderWidth: 1.5, borderColor: Colors.border, alignItems: 'center' },
  segText: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary },
  remarkInput: { borderWidth: 1, borderColor: Colors.border, borderRadius: 8, padding: 10, fontSize: 14, minHeight: 70, textAlignVertical: 'top', marginBottom: 12 },
  confirmBtn: { padding: 14, borderRadius: 10, alignItems: 'center' },
  confirmBtnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
});
