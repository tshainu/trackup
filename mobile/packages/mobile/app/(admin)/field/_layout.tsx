import { Stack } from 'expo-router';
import { Colors } from '../../../lib/colors';

export default function FieldLayout() {
  return (
    <Stack
      screenOptions={{
        headerStyle: { backgroundColor: Colors.primary },
        headerTintColor: '#fff',
        headerTitleStyle: { fontWeight: '700' },
      }}
    >
      <Stack.Screen name="index" options={{ title: 'Field Services' }} />
      <Stack.Screen name="[id]" options={{ title: 'Complaint Detail' }} />
      <Stack.Screen name="create" options={{ title: 'New Field Complaint' }} />
    </Stack>
  );
}
