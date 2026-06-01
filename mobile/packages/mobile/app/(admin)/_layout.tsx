import { Tabs, useRouter } from 'expo-router';
import { useEffect } from 'react';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../../lib/auth';
import { Colors } from '../../lib/colors';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export default function AdminLayout() {
  const { session } = useAuth();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  useEffect(() => {
    if (session && session.role !== 'admin') {
      router.replace('/(technician)/jobs');
    }
  }, [session]);

  const shop = session?.role === 'admin' ? session.shop : null;
  const modules = shop?.modules ?? [];

  const hasJobs   = modules.includes('job_orders');
  const hasField  = modules.includes('field_services');

  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor: Colors.primary,
        tabBarInactiveTintColor: Colors.textMuted,
        tabBarStyle: {
          height: 56 + insets.bottom,
          paddingBottom: insets.bottom,
          paddingTop: 6,
          borderTopWidth: 1,
          borderTopColor: '#E5E7EB',
          backgroundColor: '#fff',
        },
        tabBarLabelStyle: { fontSize: 11, fontWeight: '600' },
        headerStyle: { backgroundColor: Colors.primary },
        headerTintColor: '#fff',
        headerTitleStyle: { fontWeight: '700' },
      }}
    >
      <Tabs.Screen
        name="dashboard"
        options={{
          title: 'Dashboard',
          tabBarIcon: ({ color, size }) => <Ionicons name="grid-outline" size={size} color={color} />,
          headerTitle: shop?.name ?? 'Dashboard',
        }}
      />
      {hasJobs && (
        <Tabs.Screen
          name="jobs"
          options={{
            title: 'Job Orders',
            tabBarIcon: ({ color, size }) => <Ionicons name="briefcase-outline" size={size} color={color} />,
            headerShown: false,
          }}
        />
      )}
      {hasField && (
        <Tabs.Screen
          name="field"
          options={{
            title: 'Field',
            tabBarIcon: ({ color, size }) => <Ionicons name="location-outline" size={size} color={color} />,
            headerShown: false,
          }}
        />
      )}
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profile',
          tabBarIcon: ({ color, size }) => <Ionicons name="person-circle-outline" size={size} color={color} />,
        }}
      />
    </Tabs>
  );
}
