import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TextInput, TouchableOpacity,
  ActivityIndicator, Modal, FlatList,
} from 'react-native';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { adminApi, Employee } from '../../../lib/api';
import { Colors } from '../../../lib/colors';
import { useToast } from '../../../components/Toast';

function Field({ label, children, required }: { label: string; children: React.ReactNode; required?: boolean }) {
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
      value={value}
      onChangeText={onChangeText}
      placeholder={placeholder}
      placeholderTextColor={Colors.textMuted}
      multiline={multiline}
      keyboardType={keyboardType}
    />
  );
}

export default function CreateJobScreen() {
  const router = useRouter();
  const qc = useQueryClient();
  const { showToast, ToastHost } = useToast();
  const [empModal, setEmpModal] = useState(false);

  const [form, setForm] = useState({
    customer_name: '',
    phone_no: '',
    customer_address: '',
    device_name: '',
    device_brand: '',
    serial_no: '',
    device_fault: '',
    rupees: '',
    advance_amount: '',
    discount: '',
    priority: 'Normal',
    accessories: '',
    remark: '',
    employee_id: null as number | null,
    employee_name: '',
  });

  const set = (key: string, val: string | number | null) => setForm(f => ({ ...f, [key]: val }));

  const { data: empData } = useQuery({
    queryKey: ['admin-employees'],
    queryFn: adminApi.employees,
  });

  const mutation = useMutation({
    mutationFn: () => adminApi.createJobCard({
      ...form,
      rupees: form.rupees ? Number(form.rupees) : undefined,
      advance_amount: form.advance_amount ? Number(form.advance_amount) : undefined,
      discount: form.discount ? Number(form.discount) : undefined,
      employee_id: form.employee_id ?? undefined,
    }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['admin-jobs'] });
      qc.invalidateQueries({ queryKey: ['admin-dashboard'] });
      showToast('Job Created', 'Job card has been created successfully', 'success', () => router.back(), 'OK');
    },
    onError: (e: any) => showToast('Error', e.message, 'error'),
  });

  function handleSubmit() {
    if (!form.customer_name || !form.phone_no || !form.device_name) {
      showToast('Required Fields', 'Customer name, phone and device name are required', 'warning');
      return;
    }
    mutation.mutate();
  }

  const PRIORITIES = ['Normal', 'High', 'Low'];

  return (
    <>
      <ScrollView style={styles.root} contentContainerStyle={{ padding: 16, paddingBottom: 100 }}>
        <View style={styles.card}>
          <Text style={styles.cardHeader}>Customer Info</Text>
          <Field label="Customer Name" required>
            <Input value={form.customer_name} onChangeText={(v: string) => set('customer_name', v)} placeholder="Full name" />
          </Field>
          <Field label="Phone No" required>
            <Input value={form.phone_no} onChangeText={(v: string) => set('phone_no', v)} placeholder="Phone number" keyboardType="phone-pad" />
          </Field>
          <Field label="Address">
            <Input value={form.customer_address} onChangeText={(v: string) => set('customer_address', v)} placeholder="Address (optional)" multiline />
          </Field>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardHeader}>Device Info</Text>
          <Field label="Device Name" required>
            <Input value={form.device_name} onChangeText={(v: string) => set('device_name', v)} placeholder="e.g. iPhone 14" />
          </Field>
          <Field label="Brand">
            <Input value={form.device_brand} onChangeText={(v: string) => set('device_brand', v)} placeholder="e.g. Apple" />
          </Field>
          <Field label="Serial No">
            <Input value={form.serial_no} onChangeText={(v: string) => set('serial_no', v)} placeholder="IMEI / Serial number" />
          </Field>
          <Field label="Fault / Issue">
            <Input value={form.device_fault} onChangeText={(v: string) => set('device_fault', v)} placeholder="Describe the fault" multiline />
          </Field>
          <Field label="Accessories">
            <Input value={form.accessories} onChangeText={(v: string) => set('accessories', v)} placeholder="e.g. Charger, Case" />
          </Field>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardHeader}>Assignment</Text>
          <Field label="Priority">
            <View style={styles.segRow}>
              {PRIORITIES.map(p => (
                <TouchableOpacity
                  key={p}
                  style={[styles.seg, form.priority === p && styles.segActive]}
                  onPress={() => set('priority', p)}
                >
                  <Text style={[styles.segText, form.priority === p && styles.segTextActive]}>{p}</Text>
                </TouchableOpacity>
              ))}
            </View>
          </Field>

          <Field label="Assign Technician">
            <TouchableOpacity style={styles.input} onPress={() => setEmpModal(true)}>
              <Text style={{ color: form.employee_id ? Colors.textPrimary : Colors.textMuted }}>
                {form.employee_name || 'Select technician...'}
              </Text>
            </TouchableOpacity>
          </Field>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardHeader}>Payment</Text>
          <Field label="Service Charge (LKR)">
            <Input value={form.rupees} onChangeText={(v: string) => set('rupees', v)} placeholder="0" keyboardType="numeric" />
          </Field>
          <Field label="Advance Amount (LKR)">
            <Input value={form.advance_amount} onChangeText={(v: string) => set('advance_amount', v)} placeholder="0" keyboardType="numeric" />
          </Field>
          <Field label="Discount (LKR)">
            <Input value={form.discount} onChangeText={(v: string) => set('discount', v)} placeholder="0" keyboardType="numeric" />
          </Field>
        </View>

        <View style={styles.card}>
          <Field label="Remarks">
            <Input value={form.remark} onChangeText={(v: string) => set('remark', v)} placeholder="Internal notes..." multiline />
          </Field>
        </View>
      </ScrollView>

      <View style={styles.actionBar}>
        <TouchableOpacity style={styles.cancelBtn} onPress={() => router.back()}>
          <Text style={styles.cancelBtnText}>Cancel</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.submitBtn} onPress={handleSubmit} disabled={mutation.isPending}>
          {mutation.isPending
            ? <ActivityIndicator color="#fff" />
            : <Text style={styles.submitBtnText}>Create Job Card</Text>
          }
        </TouchableOpacity>
      </View>

      {/* Employee picker modal */}
      <Modal visible={empModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Select Technician</Text>
              <TouchableOpacity onPress={() => setEmpModal(false)}>
                <Ionicons name="close" size={22} color={Colors.textPrimary} />
              </TouchableOpacity>
            </View>
            <TouchableOpacity
              style={styles.empOption}
              onPress={() => { set('employee_id', null); set('employee_name', ''); setEmpModal(false); }}
            >
              <Text style={[styles.empName, { color: Colors.textMuted }]}>— Unassigned —</Text>
            </TouchableOpacity>
            {(empData?.employees ?? []).map((emp: Employee) => (
              <TouchableOpacity
                key={emp.id}
                style={styles.empOption}
                onPress={() => { set('employee_id', emp.id); set('employee_name', emp.employee_name); setEmpModal(false); }}
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
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 14, marginBottom: 12 },
  cardHeader: { fontSize: 14, fontWeight: '700', color: Colors.primary, marginBottom: 12, textTransform: 'uppercase', letterSpacing: 0.5 },
  fieldWrap: { marginBottom: 12 },
  fieldLabel: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary, marginBottom: 5 },
  input: {
    borderWidth: 1.5, borderColor: Colors.border, borderRadius: 8, padding: 10,
    fontSize: 14, color: Colors.textPrimary, backgroundColor: Colors.bg,
  },
  inputMulti: { minHeight: 70, textAlignVertical: 'top' },

  segRow: { flexDirection: 'row', gap: 8 },
  seg: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 8, borderWidth: 1.5, borderColor: Colors.border },
  segActive: { backgroundColor: Colors.primary, borderColor: Colors.primary },
  segText: { fontSize: 13, fontWeight: '600', color: Colors.textSecondary },
  segTextActive: { color: '#fff' },

  actionBar: {
    position: 'absolute', bottom: 0, left: 0, right: 0,
    flexDirection: 'row', gap: 10, padding: 12, backgroundColor: '#fff',
    borderTopWidth: 1, borderTopColor: Colors.border,
  },
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
