import { Stack } from 'expo-router';
import { Colors } from '../../../lib/colors';

export default function TechFieldLayout() {
  return (
    <Stack screenOptions={{ headerStyle: { backgroundColor: Colors.primary }, headerTintColor: '#fff', headerTitleStyle: { fontWeight: '700' } }}>
      <Stack.Screen name="index" options={{ title: 'Field Jobs' }} />
      <Stack.Screen name="[id]" options={{ title: 'Field Job Detail' }} />
    </Stack>
  );
}
