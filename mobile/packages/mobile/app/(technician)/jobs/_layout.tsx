import { Stack } from 'expo-router';
import { Colors } from '../../../lib/colors';

export default function TechJobsLayout() {
  return (
    <Stack screenOptions={{ headerStyle: { backgroundColor: Colors.primary }, headerTintColor: '#fff', headerTitleStyle: { fontWeight: '700' } }}>
      <Stack.Screen name="index" options={{ title: 'My Jobs' }} />
      <Stack.Screen name="[id]" options={{ title: 'Job Detail' }} />
    </Stack>
  );
}
