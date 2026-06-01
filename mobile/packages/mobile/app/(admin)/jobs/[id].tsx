import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  ActivityIndicator, Modal, TextInput,
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { adminApi, JobCard } from '../../../lib/api';
import { Colors } from '../../../lib/colors';
import { useToast } from '../../../components/Toast';

const STATUSES = ['Pending', 'In Progress', 'Completed', 'Not Completed', 'Broken', 'Cancelled'];

function InfoRow({ label, value }: { label: string; value?: string | number | null }) {
  if (!value) return null;
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{String(value)}</Text>
    </View>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <View style={styles.section}>
      <Text style={styles.sectionTitle}>{title}</Text>
      {children}
    </View>
  );
}

export default function JobDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const qc = useQueryClient();
  const { showToast, ToastHost } = useToast();
  const [statusModal, setStatusModal] = useState(false);
  const [cancelReason, setCancelReason] = useState('');
  const [pendingStatus, setPendingStatus] = useState('');

  const { data, isLoading } = useQuery({
    queryKey: ['admin-job', id],
    queryFn: () => adminApi.jobCard(Number(id)),
  });

  const statusMutation = useMutation({
    mutationFn: ({ status, reason }: { status: string; reason?: string }) =>
      adminApi.updateJobStatus(Number(id), status, reason),
    onSuccess: (_, vars) => {
      qc.invalidateQueries({ queryKey: ['admin-job', id] });
      qc.invalidateQueries({ queryKey: ['admin-jobs'] });
      qc.invalidateQueries({ queryKey: ['admin-dashboard'] });
      setStatusModal(false);
      showToast('Status Updated', `Job moved to ${vars.status}`, 'success');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  function handleStatusSelect(s: string) {
    if (s === 'Cancelled') {
      setPendingStatus(s);
    } else {
      statusMutation.mutate({ status: s });
    }
  }

  if (isLoading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={Colors.primary} /></View>;
  }

  const job = data?.job;
  if (!job) return null;

  const sc = Colors.statusColors[job.status] ?? { bg: '#F1F5F9', text: '#475569' };

  return (
    <>
      <ScrollView style={styles.root} contentContainerStyle={{ paddingBottom: 100 }}>
        {/* Header card */}
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

        <Section title="Device Info">
          <InfoRow label="Device" value={job.device_name} />
          <InfoRow label="Brand" value={job.device_brand} />
          <InfoRow label="Serial No" value={job.serial_no} />
          <InfoRow label="Fault" value={job.device_fault} />
          <InfoRow label="Accessories" value={job.accessories} />
        </Section>

        <Section title="Assignment">
          <InfoRow label="Technician" value={job.employee?.employee_name ?? 'Unassigned'} />
          <InfoRow label="Date" value={job.date?.split('T')[0]} />
          <InfoRow label="Est. Delivery" value={job.estimated_delivery?.split('T')[0]} />
          <InfoRow label="Priority" value={job.priority} />
        </Section>

        <Section title="Payment">
          <InfoRow label="Service Charge" value={`LKR ${Number(job.rupees ?? 0).toLocaleString()}`} />
          <InfoRow label="Discount" value={`LKR ${Number(job.discount ?? 0).toLocaleString()}`} />
          <InfoRow label="Grand Total" value={`LKR ${Number(job.grand_total ?? 0).toLocaleString()}`} />
          <InfoRow label="Advance Paid" value={`LKR ${Number(job.advance_amount ?? 0).toLocaleString()}`} />
          <InfoRow label="Paid Amount" value={`LKR ${Number(job.paid_amount ?? 0).toLocaleString()}`} />
          <InfoRow label="Balance" value={`LKR ${Number(job.balance ?? 0).toLocaleString()}`} />
          <InfoRow label="Payment Status" value={job.payment_status} />
        </Section>

        {job.invoice_items && job.invoice_items.length > 0 && (
          <Section title="Invoice Items">
            {job.invoice_items.map(item => (
              <View key={item.id} style={styles.invoiceItem}>
                <Text style={styles.invoiceItemDesc} numberOfLines={1}>{item.description}</Text>
                <Text style={styles.invoiceItemTotal}>
                  {item.qty} × {item.unit_price} = LKR {item.total}
                </Text>
              </View>
            ))}
          </Section>
        )}

        {job.remark && (
          <Section title="Remark">
            <Text style={styles.remark}>{job.remark}</Text>
          </Section>
        )}

        {job.need_assistant && (
          <View style={styles.assistAlert}>
            <Ionicons name="warning" size={16} color="#92400E" />
            <Text style={styles.assistText}>Assistance Requested</Text>
          </View>
        )}
      </ScrollView>

      {/* Bottom action bar */}
      <View style={styles.actionBar}>
        <TouchableOpacity style={styles.statusBtn} onPress={() => setStatusModal(true)}>
          <Ionicons name="swap-horizontal-outline" size={18} color="#fff" />
          <Text style={styles.statusBtnText}>Change Status</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.statusBtn, { backgroundColor: Colors.primaryDark, flex: 0, paddingHorizontal: 16 }]}
          onPress={() => router.push(`/(admin)/jobs/edit/${id}` as any)}
        >
          <Ionicons name="create-outline" size={18} color="#fff" />
        </TouchableOpacity>
      </View>

      {/* Status Modal */}
      <Modal visible={statusModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Change Status</Text>
              <TouchableOpacity onPress={() => { setStatusModal(false); setPendingStatus(''); }}>
                <Ionicons name="close" size={22} color={Colors.textPrimary} />
              </TouchableOpacity>
            </View>

            {STATUSES.map(s => {
              const sc2 = Colors.statusColors[s] ?? { bg: '#F1F5F9', text: '#475569' };
              const isCurrent = s === job.status;
              return (
                <TouchableOpacity
                  key={s}
                  style={[styles.statusOption, isCurrent && styles.statusOptionActive]}
                  onPress={() => !isCurrent && handleStatusSelect(s)}
                  disabled={isCurrent}
                >
                  <View style={[styles.badge, { backgroundColor: sc2.bg }]}>
                    <Text style={[styles.badgeText, { color: sc2.text }]}>{s}</Text>
                  </View>
                  {isCurrent && <Text style={styles.currentLabel}>Current</Text>}
                </TouchableOpacity>
              );
            })}

            {/* Cancellation reason input */}
            {pendingStatus === 'Cancelled' && (
              <View style={{ marginTop: 12 }}>
                <Text style={styles.infoLabel}>Reason for cancellation</Text>
                <TextInput
                  style={styles.reasonInput}
                  placeholder="Enter reason..."
                  value={cancelReason}
                  onChangeText={setCancelReason}
                  multiline
                />
                <TouchableOpacity
                  style={styles.confirmBtn}
                  onPress={() => statusMutation.mutate({ status: 'Cancelled', reason: cancelReason })}
                >
                  {statusMutation.isPending
                    ? <ActivityIndicator color="#fff" />
                    : <Text style={styles.confirmBtnText}>Confirm Cancel</Text>
                  }
                </TouchableOpacity>
              </View>
            )}
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

  headerCard: {
    backgroundColor: Colors.primary, padding: 20,
    flexDirection: 'row', alignItems: 'flex-start', gap: 12,
  },
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

  invoiceItem: { paddingVertical: 6, borderBottomWidth: 1, borderBottomColor: Colors.border },
  invoiceItemDesc: { fontSize: 13, color: Colors.textPrimary, fontWeight: '600' },
  invoiceItemTotal: { fontSize: 12, color: Colors.textSecondary, marginTop: 2 },

  remark: { fontSize: 14, color: Colors.textPrimary, lineHeight: 20 },

  assistAlert: {
    flexDirection: 'row', alignItems: 'center', gap: 8,
    backgroundColor: '#FEF3C7', margin: 12, padding: 12, borderRadius: 10,
  },
  assistText: { fontSize: 13, fontWeight: '600', color: '#92400E' },

  actionBar: {
    position: 'absolute', bottom: 0, left: 0, right: 0,
    backgroundColor: '#fff', padding: 12, flexDirection: 'row', gap: 8,
    borderTopWidth: 1, borderTopColor: Colors.border,
  },
  statusBtn: {
    flex: 1, backgroundColor: Colors.primary, flexDirection: 'row',
    alignItems: 'center', justifyContent: 'center', gap: 8,
    paddingVertical: 12, borderRadius: 10,
  },
  statusBtnText: { color: '#fff', fontWeight: '700', fontSize: 14 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalTitle: { fontSize: 17, fontWeight: '700', color: Colors.textPrimary },
  statusOption: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 10, borderBottomWidth: 1, borderBottomColor: Colors.border },
  statusOptionActive: { opacity: 0.5 },
  badge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 6 },
  badgeText: { fontSize: 12, fontWeight: '700' },
  currentLabel: { fontSize: 12, color: Colors.textMuted, fontStyle: 'italic' },

  reasonInput: {
    borderWidth: 1, borderColor: Colors.border, borderRadius: 8,
    padding: 10, fontSize: 14, minHeight: 60, marginTop: 6,
  },
  confirmBtn: {
    backgroundColor: Colors.danger, marginTop: 8, padding: 12,
    borderRadius: 10, alignItems: 'center',
  },
  confirmBtnText: { color: '#fff', fontWeight: '700' },
});
