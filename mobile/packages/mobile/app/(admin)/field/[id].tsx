import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  ActivityIndicator, Modal,
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { adminApi } from '../../../lib/api';
import { Colors } from '../../../lib/colors';
import { useToast } from '../../../components/Toast';

const STATUSES = ['Pending', 'Assigned', 'In Progress', 'Completed', 'On Hold', 'Billed'];

function InfoRow({ label, value }: { label: string; value?: string | number | null }) {
  if (!value && value !== 0) return null;
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{String(value)}</Text>
    </View>
  );
}

export default function FieldDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const qc = useQueryClient();
  const { showToast, ToastHost } = useToast();
  const [statusModal, setStatusModal] = useState(false);
  const [assignModal, setAssignModal] = useState(false);

  const { data, isLoading } = useQuery({
    queryKey: ['admin-field-item', id],
    queryFn: () => adminApi.fieldComplaint(Number(id)),
  });

  const { data: empData } = useQuery({
    queryKey: ['admin-employees'],
    queryFn: adminApi.employees,
  });

  const statusMutation = useMutation({
    mutationFn: (status: string) => adminApi.updateFieldComplaint(Number(id), { status }),
    onSuccess: (_, status) => {
      qc.invalidateQueries({ queryKey: ['admin-field-item', id] });
      qc.invalidateQueries({ queryKey: ['admin-field'] });
      qc.invalidateQueries({ queryKey: ['admin-dashboard'] });
      setStatusModal(false);
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  const assignMutation = useMutation({
    mutationFn: (empId: number) => adminApi.updateFieldComplaint(Number(id), { assigned_to: empId }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['admin-field-item', id] });
      setAssignModal(false);
      showToast('Technician Assigned', 'The job has been assigned successfully', 'success');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  if (isLoading) return <View style={styles.center}><ActivityIndicator size="large" color={Colors.primary} /></View>;

  const fc = data?.complaint;
  if (!fc) return null;

  const sc = Colors.statusColors[fc.status] ?? { bg: '#F1F5F9', text: '#475569' };

  return (
    <>
      <ScrollView style={styles.root} contentContainerStyle={{ paddingBottom: 100 }}>
        <View style={styles.headerCard}>
          <View style={{ flex: 1 }}>
            <Text style={styles.complaintNo}>{fc.complaint_no}</Text>
            <Text style={styles.customerName}>{fc.customer_name}</Text>
            <Text style={styles.phone}>{fc.phone_no}</Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: sc.bg }]}>
            <Text style={[styles.statusText, { color: sc.text }]}>{fc.status}</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Service Info</Text>
          <InfoRow label="Service Type" value={fc.service_type_name} />
          <InfoRow label="Description" value={fc.description} />
          <InfoRow label="Priority" value={fc.priority} />
          <InfoRow label="Scheduled Date" value={fc.scheduled_date?.split('T')[0]} />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Location</Text>
          <InfoRow label="Address" value={fc.address} />
          {fc.gps_lat && fc.gps_lng && (
            <View style={styles.gpsRow}>
              <Ionicons name="navigate-circle" size={16} color='#E8490F' />
              <Text style={styles.gpsText}>{Number(fc.gps_lat).toFixed(6)}, {Number(fc.gps_lng).toFixed(6)}</Text>
              <View style={styles.gpsBadge}>
                <Text style={styles.gpsBadgeText}>GPS Saved</Text>
              </View>
            </View>
          )}
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Assignment</Text>
          <InfoRow label="Technician" value={fc.assigned_employee?.employee_name ?? 'Unassigned'} />
          <TouchableOpacity style={styles.assignBtn} onPress={() => setAssignModal(true)}>
            <Ionicons name="person-add-outline" size={16} color={Colors.primary} />
            <Text style={styles.assignBtnText}>Change Technician</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Payment</Text>
          <InfoRow label="Service Charge" value={`LKR ${Number(fc.service_charge ?? 0).toLocaleString()}`} />
          <InfoRow label="Discount" value={`LKR ${Number(fc.discount ?? 0).toLocaleString()}`} />
          <InfoRow label="Grand Total" value={`LKR ${Number(fc.grand_total ?? 0).toLocaleString()}`} />
          <InfoRow label="Paid Amount" value={`LKR ${Number(fc.paid_amount ?? 0).toLocaleString()}`} />
        </View>

        {fc.completion_notes && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Notes</Text>
            <Text style={styles.notes}>{fc.completion_notes}</Text>
          </View>
        )}
      </ScrollView>

      <View style={styles.actionBar}>
        <TouchableOpacity style={styles.statusBtn} onPress={() => setStatusModal(true)}>
          <Ionicons name="swap-horizontal-outline" size={18} color="#fff" />
          <Text style={styles.statusBtnText}>Change Status</Text>
        </TouchableOpacity>
      </View>

      {/* Status Modal */}
      <Modal visible={statusModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Change Status</Text>
              <TouchableOpacity onPress={() => setStatusModal(false)}>
                <Ionicons name="close" size={22} color={Colors.textPrimary} />
              </TouchableOpacity>
            </View>
            {STATUSES.map(s => {
              const sc2 = Colors.statusColors[s] ?? { bg: '#F1F5F9', text: '#475569' };
              const isCurrent = s === fc.status;
              return (
                <TouchableOpacity
                  key={s}
                  style={[styles.statusOption, isCurrent && { opacity: 0.5 }]}
                  onPress={() => !isCurrent && statusMutation.mutate(s)}
                  disabled={isCurrent || statusMutation.isPending}
                >
                  <View style={[styles.badge, { backgroundColor: sc2.bg }]}>
                    <Text style={[styles.badgeText, { color: sc2.text }]}>{s}</Text>
                  </View>
                  {isCurrent && <Text style={styles.currentLabel}>Current</Text>}
                </TouchableOpacity>
              );
            })}
          </View>
        </View>
      </Modal>

      {/* Assign Modal */}
      <Modal visible={assignModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Assign Technician</Text>
              <TouchableOpacity onPress={() => setAssignModal(false)}>
                <Ionicons name="close" size={22} color={Colors.textPrimary} />
              </TouchableOpacity>
            </View>
            {(empData?.employees ?? []).map(emp => (
              <TouchableOpacity
                key={emp.id}
                style={styles.empOption}
                onPress={() => assignMutation.mutate(emp.id)}
              >
                <Text style={styles.empName}>{emp.employee_name}</Text>
                <Text style={styles.empRole}>{emp.role}</Text>
              </TouchableOpacity>
            ))}
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
  gpsRow: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 4, flexWrap: 'wrap' },
  gpsText: { fontSize: 13, color: Colors.textPrimary, fontWeight: '600', flex: 1 },
  gpsBadge: { backgroundColor: '#E8490F18', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 6 },
  gpsBadgeText: { fontSize: 11, color: '#E8490F', fontWeight: '700' },
  assignBtn: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 4 },
  assignBtnText: { fontSize: 13, fontWeight: '600', color: Colors.primary },
  notes: { fontSize: 14, color: Colors.textPrimary, lineHeight: 20 },
  actionBar: { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: '#fff', padding: 12, borderTopWidth: 1, borderTopColor: Colors.border },
  statusBtn: { backgroundColor: Colors.primary, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, paddingVertical: 12, borderRadius: 10 },
  statusBtnText: { color: '#fff', fontWeight: '700', fontSize: 14 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20, maxHeight: '70%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalTitle: { fontSize: 17, fontWeight: '700', color: Colors.textPrimary },
  statusOption: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 10, borderBottomWidth: 1, borderBottomColor: Colors.border },
  badge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 6 },
  badgeText: { fontSize: 12, fontWeight: '700' },
  currentLabel: { fontSize: 12, color: Colors.textMuted, fontStyle: 'italic' },
  empOption: { paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: Colors.border },
  empName: { fontSize: 15, fontWeight: '600', color: Colors.textPrimary },
  empRole: { fontSize: 12, color: Colors.textMuted, marginTop: 2 },
});
