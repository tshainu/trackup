import { Stack } from 'expo-router';
import { Colors } from '../../../lib/colors';

export default function JobsLayout() {
  return (
    <Stack
      screenOptions={{
        headerStyle: { backgroundColor: Colors.primary },
        headerTintColor: '#fff',
        headerTitleStyle: { fontWeight: '700' },
      }}
    >
      <Stack.Screen name="index" options={{ title: 'Job Orders' }} />
      <Stack.Screen name="[id]" options={{ title: 'Job Detail' }} />
      <Stack.Screen name="create" options={{ title: 'Create Job Card' }} />
    </Stack>
  );
}
