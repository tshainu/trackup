import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TextInput, TouchableOpacity,
  ActivityIndicator, Modal,
} from 'react-native';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { adminApi, Employee } from '../../../lib/api';
import { Colors } from '../../../lib/colors';
import { useToast } from '../../../components/Toast';

function Field({ label, children, required }: any) {
  return (
    <View style={styles.fieldWrap}>
      <Text style={styles.fieldLabel}>{label}{required && <Text style={{ color: Colors.danger }}> *</Text>}</Text>
      {children}
    </View>
  );
}
function Input({ value, onChangeText, placeholder, multiline, keyboardType }: any) {
  return (
    <TextInput
      style={[styles.input, multiline && styles.inputMulti]}
      value={value} onChangeText={onChangeText} placeholder={placeholder}
      placeholderTextColor={Colors.textMuted} multiline={multiline} keyboardType={keyboardType}
    />
  );
}

export default function CreateFieldScreen() {
  const router = useRouter();
  const qc = useQueryClient();
  const { showToast, ToastHost } = useToast();
  const [empModal, setEmpModal] = useState(false);

  const [form, setForm] = useState({
    customer_name: '', phone_no: '', address: '',
    service_type_name: '', description: '', priority: 'Normal',
    assigned_to: null as number | null, employee_name: '',
    scheduled_date: '', gps_lat: '', gps_lng: '',
  });

  const set = (k: string, v: any) => setForm(f => ({ ...f, [k]: v }));

  const { data: empData } = useQuery({ queryKey: ['admin-employees'], queryFn: adminApi.employees });

  const mutation = useMutation({
    mutationFn: () => adminApi.createFieldComplaint({
      ...form,
      assigned_to: form.assigned_to ?? undefined,
      gps_lat: form.gps_lat ? Number(form.gps_lat) : undefined,
      gps_lng: form.gps_lng ? Number(form.gps_lng) : undefined,
      scheduled_date: form.scheduled_date || undefined,
    }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['admin-field'] });
      qc.invalidateQueries({ queryKey: ['admin-dashboard'] });
      showToast('Job Created', 'Field complaint created successfully', 'success', () => router.back(), 'OK');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  function submit() {
    if (!form.customer_name || !form.phone_no) {
      showToast('Required Fields', 'Customer name and phone are required', 'warning');
      return;
    }
    mutation.mutate();
  }

  return (
    <>
      <ScrollView style={styles.root} contentContainerStyle={{ padding: 16, paddingBottom: 100 }}>
        <View style={styles.card}>
          <Text style={styles.cardHeader}>Customer Info</Text>
          <Field label="Customer Name" required><Input value={form.customer_name} onChangeText={(v: string) => set('customer_name', v)} placeholder="Full name" /></Field>
          <Field label="Phone No" required><Input value={form.phone_no} onChangeText={(v: string) => set('phone_no', v)} placeholder="Phone number" keyboardType="phone-pad" /></Field>
          <Field label="Address"><Input value={form.address} onChangeText={(v: string) => set('address', v)} placeholder="Service address" multiline /></Field>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardHeader}>Service Info</Text>
          <Field label="Service Type"><Input value={form.service_type_name} onChangeText={(v: string) => set('service_type_name', v)} placeholder="e.g. AC Repair, Plumbing" /></Field>
          <Field label="Description"><Input value={form.description} onChangeText={(v: string) => set('description', v)} placeholder="Problem description..." multiline /></Field>
          <Field label="Priority">
            <View style={styles.segRow}>
              {['Normal', 'High', 'Low'].map(p => (
                <TouchableOpacity key={p} style={[styles.seg, form.priority === p && styles.segActive]} onPress={() => set('priority', p)}>
                  <Text style={[styles.segText, form.priority === p && styles.segTextActive]}>{p}</Text>
                </TouchableOpacity>
              ))}
            </View>
          </Field>
          <Field label="Scheduled Date"><Input value={form.scheduled_date} onChangeText={(v: string) => set('scheduled_date', v)} placeholder="YYYY-MM-DD" /></Field>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardHeader}>Assignment</Text>
          <Field label="Assign Technician">
            <TouchableOpacity style={styles.input} onPress={() => setEmpModal(true)}>
              <Text style={{ color: form.assigned_to ? Colors.textPrimary : Colors.textMuted }}>
                {form.employee_name || 'Select technician...'}
              </Text>
            </TouchableOpacity>
          </Field>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardHeader}>GPS (Optional)</Text>
          <Field label="Latitude"><Input value={form.gps_lat} onChangeText={(v: string) => set('gps_lat', v)} placeholder="e.g. 7.2906" keyboardType="numeric" /></Field>
          <Field label="Longitude"><Input value={form.gps_lng} onChangeText={(v: string) => set('gps_lng', v)} placeholder="e.g. 80.6337" keyboardType="numeric" /></Field>
        </View>
      </ScrollView>

      <View style={styles.actionBar}>
        <TouchableOpacity style={styles.cancelBtn} onPress={() => router.back()}>
          <Text style={styles.cancelBtnText}>Cancel</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.submitBtn} onPress={submit} disabled={mutation.isPending}>
          {mutation.isPending ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Create Complaint</Text>}
        </TouchableOpacity>
      </View>

      <Modal visible={empModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Select Technician</Text>
              <TouchableOpacity onPress={() => setEmpModal(false)}><Ionicons name="close" size={22} color={Colors.textPrimary} /></TouchableOpacity>
            </View>
            <TouchableOpacity style={styles.empOption} onPress={() => { set('assigned_to', null); set('employee_name', ''); setEmpModal(false); }}>
              <Text style={[styles.empName, { color: Colors.textMuted }]}>— Unassigned —</Text>
            </TouchableOpacity>
            {(empData?.employees ?? []).map(emp => (
              <TouchableOpacity key={emp.id} style={styles.empOption} onPress={() => { set('assigned_to', emp.id); set('employee_name', emp.employee_name); setEmpModal(false); }}>
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
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 14, marginBottom: 12 },
  cardHeader: { fontSize: 14, fontWeight: '700', color: Colors.primary, marginBottom: 12, textTransform: 'uppercase', letterSpacing: 0.5 },
  fieldWrap: { marginBottom: 12 },
  fieldLabel: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary, marginBottom: 5 },
  input: { borderWidth: 1.5, borderColor: Colors.border, borderRadius: 8, padding: 10, fontSize: 14, color: Colors.textPrimary, backgroundColor: Colors.bg },
  inputMulti: { minHeight: 70, textAlignVertical: 'top' },
  segRow: { flexDirection: 'row', gap: 8 },
  seg: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 8, borderWidth: 1.5, borderColor: Colors.border },
  segActive: { backgroundColor: Colors.primary, borderColor: Colors.primary },
  segText: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary },
  segTextActive: { color: '#fff' },
  actionBar: { position: 'absolute', bottom: 0, left: 0, right: 0, flexDirection: 'row', gap: 10, padding: 12, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: Colors.border },
  cancelBtn: { flex: 1, padding: 13, borderRadius: 10, borderWidth: 1.5, borderColor: Colors.border, alignItems: 'center' },
  cancelBtnText: { fontSize: 15, fontWeight: '600', color: Colors.textSecondary },
  submitBtn: { flex: 2, padding: 13, borderRadius: 10, backgroundColor: Colors.primary, alignItems: 'center' },
  submitBtnText: { fontSize: 15, fontWeight: '700', color: '#fff' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20, maxHeight: '70%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalTitle: { fontSize: 17, fontWeight: '700', color: Colors.textPrimary },
  empOption: { paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: Colors.border },
  empName: { fontSize: 15, fontWeight: '600', color: Colors.textPrimary },
  empRole: { fontSize: 12, color: Colors.textMuted, marginTop: 2 },
});
