import React, { useState } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  TextInput, ActivityIndicator, RefreshControl,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { adminApi, FieldComplaint } from '../../../lib/api';
import { Colors } from '../../../lib/colors';

const STATUSES = ['All', 'Pending', 'Assigned', 'In Progress', 'Completed', 'On Hold'];

function ComplaintItem({ item, onPress }: { item: FieldComplaint; onPress: () => void }) {
  const sc = Colors.statusColors[item.status] ?? { bg: '#F1F5F9', text: '#475569' };

  return (
    <TouchableOpacity style={styles.card} onPress={onPress} activeOpacity={0.75}>
      <View style={styles.cardHeader}>
        <Text style={styles.complaintNo}>{item.complaint_no}</Text>
        <View style={[styles.badge, { backgroundColor: sc.bg }]}>
          <Text style={[styles.badgeText, { color: sc.text }]}>{item.status}</Text>
        </View>
      </View>

      <Text style={styles.customerName}>{item.customer_name}</Text>
      {item.service_type_name && (
        <Text style={styles.serviceType}>{item.service_type_name}</Text>
      )}

      <View style={styles.cardFooter}>
        {item.address && (
          <View style={styles.footerItem}>
            <Ionicons name="location-outline" size={12} color={Colors.textMuted} />
            <Text style={styles.footerText} numberOfLines={1}>{item.address}</Text>
          </View>
        )}
        {item.assigned_employee && (
          <View style={styles.footerItem}>
            <Ionicons name="person-outline" size={12} color={Colors.textMuted} />
            <Text style={styles.footerText}>{item.assigned_employee.employee_name}</Text>
          </View>
        )}
      </View>
    </TouchableOpacity>
  );
}

export default function FieldListScreen() {
  const router = useRouter();
  const [status, setStatus] = useState('');
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['admin-field', status, search, page],
    queryFn: () => adminApi.fieldComplaints({ status: status || undefined, search: search || undefined, page }),
  });

  const items = data?.data ?? [];

  return (
    <View style={styles.root}>
      <View style={styles.searchBar}>
        <Ionicons name="search-outline" size={16} color={Colors.textMuted} style={{ marginRight: 8 }} />
        <TextInput
          style={styles.searchInput}
          placeholder="Search name, phone, complaint no..."
          placeholderTextColor={Colors.textMuted}
          value={search}
          onChangeText={t => { setSearch(t); setPage(1); }}
        />
        {search ? <TouchableOpacity onPress={() => setSearch('')}><Ionicons name="close-circle" size={16} color={Colors.textMuted} /></TouchableOpacity> : null}
      </View>

      <FlatList
        horizontal
        data={STATUSES}
        showsHorizontalScrollIndicator={false}
        style={styles.filterRow}
        keyExtractor={i => i}
        renderItem={({ item }) => {
          const active = (status === '' && item === 'All') || item === status;
          return (
            <TouchableOpacity
              style={[styles.filterChip, active && styles.filterChipActive]}
              onPress={() => { setStatus(item === 'All' ? '' : item); setPage(1); }}
            >
              <Text style={[styles.filterChipText, active && styles.filterChipTextActive]}>{item}</Text>
            </TouchableOpacity>
          );
        }}
      />

      {isLoading
        ? <ActivityIndicator style={{ marginTop: 40 }} color={Colors.primary} />
        : (
          <FlatList
            data={items}
            keyExtractor={i => String(i.id)}
            contentContainerStyle={{ padding: 12, paddingBottom: 100 }}
            refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} />}
            renderItem={({ item }) => (
              <ComplaintItem item={item} onPress={() => router.push(`/(admin)/field/${item.id}` as any)} />
            )}
            ListEmptyComponent={
              <View style={styles.empty}>
                <Ionicons name="location-outline" size={48} color={Colors.textMuted} />
                <Text style={styles.emptyText}>No field complaints found</Text>
              </View>
            }
            onEndReached={() => { if (data && page < data.last_page) setPage(p => p + 1); }}
            onEndReachedThreshold={0.4}
          />
        )
      }

      <TouchableOpacity style={styles.fab} onPress={() => router.push('/(admin)/field/create' as any)}>
        <Ionicons name="add" size={28} color="#fff" />
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: Colors.bg },
  searchBar: { flexDirection: 'row', alignItems: 'center', margin: 12, padding: 10, backgroundColor: '#fff', borderRadius: 10, borderWidth: 1, borderColor: Colors.border },
  searchInput: { flex: 1, fontSize: 14, color: Colors.textPrimary },
  filterRow: { paddingHorizontal: 8, marginBottom: 4, maxHeight: 44 },
  filterChip: { paddingHorizontal: 14, paddingVertical: 6, borderRadius: 20, backgroundColor: '#fff', borderWidth: 1, borderColor: Colors.border, marginHorizontal: 4 },
  filterChipActive: { backgroundColor: Colors.primary, borderColor: Colors.primary },
  filterChipText: { fontSize: 12, fontWeight: '600', color: Colors.textSecondary },
  filterChipTextActive: { color: '#fff' },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 14, marginBottom: 10, elevation: 2, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 4 },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 },
  complaintNo: { fontSize: 13, fontWeight: '700', color: Colors.primary },
  badge: { paddingHorizontal: 7, paddingVertical: 3, borderRadius: 6 },
  badgeText: { fontSize: 11, fontWeight: '700' },
  customerName: { fontSize: 15, fontWeight: '700', color: Colors.textPrimary },
  serviceType: { fontSize: 13, color: Colors.textSecondary, marginTop: 2 },
  cardFooter: { flexDirection: 'row', gap: 12, marginTop: 8, flexWrap: 'wrap' },
  footerItem: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  footerText: { fontSize: 12, color: Colors.textMuted, maxWidth: 150 },
  empty: { alignItems: 'center', marginTop: 60, gap: 12 },
  emptyText: { fontSize: 15, color: Colors.textMuted },
  fab: { position: 'absolute', bottom: 20, right: 20, width: 56, height: 56, borderRadius: 28, backgroundColor: Colors.primary, justifyContent: 'center', alignItems: 'center', elevation: 6 },
});
