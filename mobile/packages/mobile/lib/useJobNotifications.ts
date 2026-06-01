import { useEffect, useRef } from 'react';
import { Platform } from 'react-native';
import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import { techApi, JobCard } from './api';
import { useAuth } from './auth';

// Configure how notifications appear when app is foregrounded
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
    shouldShowBanner: true,
    shouldShowList: true,
  }),
});

async function requestPermissions() {
  if (Platform.OS === 'web') return false;
  if (!Device.isDevice) return false; // simulator — skip
  const { status: existing } = await Notifications.getPermissionsAsync();
  if (existing === 'granted') return true;
  const { status } = await Notifications.requestPermissionsAsync();
  return status === 'granted';
}

async function fireNotification(job: JobCard) {
  await Notifications.scheduleNotificationAsync({
    content: {
      title: '🔧 New Job Assigned',
      body: `${job.order_no} · ${job.customer_name} — ${job.device_name}`,
      data: { jobId: job.id },
      sound: true,
    },
    trigger: null, // fire immediately
  });
}

export function useJobNotifications(onNewJobs?: () => void) {
  const { session } = useAuth();
  const knownIds = useRef<Set<number>>(new Set());
  const initialized = useRef(false);
  const interval = useRef<ReturnType<typeof setInterval> | null>(null);

  useEffect(() => {
    if (session?.role !== 'technician') return;
    if (Platform.OS === 'web') return; // no native notifications on web

    requestPermissions();

    async function check() {
      try {
        const res = await techApi.jobs();
        const jobs: JobCard[] = res.jobs ?? [];

        if (!initialized.current) {
          // First load — seed known IDs, don't notify
          jobs.forEach(j => knownIds.current.add(j.id));
          initialized.current = true;
          return;
        }

        const newJobs = jobs.filter(j => !knownIds.current.has(j.id));
        if (newJobs.length > 0) {
          newJobs.forEach(j => {
            knownIds.current.add(j.id);
            fireNotification(j);
          });
          onNewJobs?.(); // trigger refetch in UI
        }
      } catch {
        // silently ignore network errors during bg poll
      }
    }

    check(); // immediate first check
    interval.current = setInterval(check, 30_000); // every 30s

    return () => {
      if (interval.current) clearInterval(interval.current);
    };
  }, [session?.role]);
}
