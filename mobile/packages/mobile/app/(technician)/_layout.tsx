import { Tabs, useRouter } from 'expo-router';
import { useEffect } from 'react';
import { View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '../../lib/auth';
import { Colors } from '../../lib/colors';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { techApi } from '../../lib/api';
import { useBadge } from '../../lib/useBadge';

function BadgeIcon({
  name, color, size, hasNew,
}: { name: any; color: string; size: number; hasNew: boolean }) {
  return (
    <View>
      <Ionicons name={name} size={size} color={color} />
      {hasNew && (
        <View style={{
          position: 'absolute', top: -2, right: -4,
          width: 9, height: 9, borderRadius: 5,
          backgroundColor: Colors.danger,
          borderWidth: 1.5, borderColor: '#fff',
        }} />
      )}
    </View>
  );
}

export default function TechLayout() {
  const { session } = useAuth();
  const router = useRouter();
  const insets = useSafeAreaInsets();

  useEffect(() => {
    if (session && session.role !== 'technician') {
      router.replace('/(admin)/dashboard');
    }
  }, [session]);

  // Poll jobs + field jobs for badge counts (every 30s)
  const { data: jobsData } = useQuery({
    queryKey: ['tech-jobs'],
    queryFn: techApi.jobs,
    refetchInterval: 30_000,
    enabled: session?.role === 'technician',
  });

  const { data: fieldData } = useQuery({
    queryKey: ['tech-field-jobs'],
    queryFn: techApi.fieldJobs,
    refetchInterval: 30_000,
    enabled: session?.role === 'technician',
  });

  const jobIds   = (jobsData?.jobs  ?? []).map(j => j.id);
  const fieldIds = (fieldData?.jobs ?? []).map(j => j.id);

  const { hasNew: jobsHasNew,  markAllSeen: markJobsSeen  } = useBadge('jobs',  jobIds);
  const { hasNew: fieldHasNew, markAllSeen: markFieldSeen } = useBadge('field', fieldIds);

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
      screenListeners={{
        tabPress: (e) => {
          const name = (e.target as string)?.split('-')[0];
          if (name === 'jobs')  markJobsSeen();
          if (name === 'field') markFieldSeen();
        },
      }}
    >
      <Tabs.Screen
        name="jobs"
        options={{
          title: 'My Jobs',
          tabBarIcon: ({ color, size }) => (
            <BadgeIcon name="briefcase-outline" color={color} size={size} hasNew={jobsHasNew} />
          ),
          headerShown: false,
        }}
      />
      <Tabs.Screen
        name="report"
        options={{
          title: 'Report',
          tabBarIcon: ({ color, size }) => <Ionicons name="bar-chart-outline" size={size} color={color} />,
          headerShown: false,
        }}
      />
      <Tabs.Screen
        name="field"
        options={{
          title: 'Field',
          tabBarIcon: ({ color, size }) => (
            <BadgeIcon name="location-outline" color={color} size={size} hasNew={fieldHasNew} />
          ),
          headerShown: false,
        }}
      />
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
