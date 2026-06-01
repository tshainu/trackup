import React, { useState, useRef } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  ActivityIndicator, ScrollView, Image,
  Dimensions, Keyboard, KeyboardAvoidingView, Platform,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../lib/auth';
import { techApi } from '../lib/api';
import { useToast } from '../components/Toast';

const { height } = Dimensions.get('window');
const ORANGE = '#E8490F';
const DARK   = '#1C1C1E';

export default function LoginScreen() {
  const [shopCode, setShopCode] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPass, setShowPass] = useState(false);
  const [loading, setLoading]   = useState(false);

  const { signIn }   = useAuth();
  const router       = useRouter();
  const insets       = useSafeAreaInsets();
  const { showToast, ToastHost } = useToast();
  const usernameRef  = useRef<TextInput>(null);
  const passwordRef  = useRef<TextInput>(null);

  async function handleLogin() {
    Keyboard.dismiss();
    if (!shopCode.trim()) {
      showToast('Missing Fields', 'Please enter your shop code.', 'warning');
      return;
    }
    if (!username.trim() || !password.trim()) {
      showToast('Missing Fields', 'Please enter your username and password.', 'warning');
      return;
    }

    setLoading(true);
    try {
      const data = await techApi.login(username.trim(), password);
      await signIn({ role: 'technician', token: data.token, employee: data.employee, shop_code: shopCode });
      router.replace('/(technician)/jobs');
    } catch (e: any) {
      showToast('Login Failed', e.message ?? 'Invalid credentials. Please try again.', 'error');
    } finally {
      setLoading(false);
    }
  }

  return (
    <KeyboardAvoidingView
      style={styles.root}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <ScrollView
        contentContainerStyle={[
          styles.scroll,
          { paddingTop: Math.max(insets.top, 24), paddingBottom: Math.max(insets.bottom, 24) },
        ]}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
        bounces={false}
      >
        {/* ── Logo hero ── */}
        <View style={styles.hero}>
          <Image
            source={require('../assets/trackup-logo.png')}
            style={styles.logo}
            resizeMode="contain"
          />
          <Text style={styles.tagline}>Repair Shop Management</Text>
        </View>

        {/* ── Form sheet ── */}
        <View style={styles.sheet}>
          <View style={styles.handle} />

          <Text style={styles.welcomeTitle}>Welcome back</Text>
          <Text style={styles.welcomeSub}>Sign in to continue</Text>

          {/* Shop Code */}
          <View style={styles.field}>
            <Text style={styles.label}>Shop Code</Text>
            <View style={styles.inputRow}>
              <Ionicons name="storefront-outline" size={18} color="#9CA3AF" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="e.g. G115"
                placeholderTextColor="#9CA3AF"
                value={shopCode}
                onChangeText={t => setShopCode(t.toUpperCase())}
                autoCapitalize="characters"
                returnKeyType="next"
                onSubmitEditing={() => usernameRef.current?.focus()}
                blurOnSubmit={false}
              />
            </View>
          </View>

          {/* Username */}
          <View style={styles.field}>
            <Text style={styles.label}>Username</Text>
            <View style={styles.inputRow}>
              <Ionicons name="person-outline" size={18} color="#9CA3AF" style={styles.inputIcon} />
              <TextInput
                ref={usernameRef}
                style={styles.input}
                placeholder="Enter username"
                placeholderTextColor="#9CA3AF"
                value={username}
                onChangeText={setUsername}
                autoCapitalize="none"
                returnKeyType="next"
                onSubmitEditing={() => passwordRef.current?.focus()}
                blurOnSubmit={false}
              />
            </View>
          </View>

          {/* Password */}
          <View style={styles.field}>
            <Text style={styles.label}>Password</Text>
            <View style={styles.inputRow}>
              <Ionicons name="lock-closed-outline" size={18} color="#9CA3AF" style={styles.inputIcon} />
              <TextInput
                ref={passwordRef}
                style={[styles.input, { flex: 1 }]}
                placeholder="Enter password"
                placeholderTextColor="#9CA3AF"
                value={password}
                onChangeText={setPassword}
                secureTextEntry={!showPass}
                returnKeyType="done"
                onSubmitEditing={handleLogin}
              />
              <TouchableOpacity onPress={() => setShowPass(!showPass)} style={styles.eyeBtn}>
                <Ionicons
                  name={showPass ? 'eye-off-outline' : 'eye-outline'}
                  size={18}
                  color="#9CA3AF"
                />
              </TouchableOpacity>
            </View>
          </View>

          {/* Sign In */}
          <TouchableOpacity
            style={[styles.signInBtn, loading && styles.signInBtnDisabled]}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.85}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <>
                <Text style={styles.signInBtnText}>Sign In</Text>
                <Ionicons name="arrow-forward" size={18} color="#fff" />
              </>
            )}
          </TouchableOpacity>

          <Text style={styles.footer}>TrackUp • Repair Shop OS</Text>
        </View>
      </ScrollView>
      <ToastHost />
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#FFFFFF' },
  scroll: { flexGrow: 1, justifyContent: 'flex-end' },

  hero: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 32,
    minHeight: height * 0.30,
  },
  logo: { width: 180, height: 180 },
  tagline: {
    fontSize: 13,
    color: '#9CA3AF',
    marginTop: 6,
    letterSpacing: 0.5,
    fontWeight: '500',
  },

  sheet: {
    backgroundColor: '#F9F9FB',
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    paddingHorizontal: 28,
    paddingTop: 16,
    paddingBottom: 24,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.06,
    shadowRadius: 12,
    elevation: 10,
  },
  handle: {
    alignSelf: 'center',
    width: 40, height: 4,
    borderRadius: 2,
    backgroundColor: '#D1D5DB',
    marginBottom: 20,
  },

  welcomeTitle: { fontSize: 26, fontWeight: '800', color: DARK, letterSpacing: -0.5 },
  welcomeSub: { fontSize: 14, color: '#6B7280', marginTop: 4, marginBottom: 24 },

  field: { marginBottom: 16 },
  label: {
    fontSize: 12, fontWeight: '700', color: '#374151',
    marginBottom: 6, letterSpacing: 0.4, textTransform: 'uppercase',
  },
  inputRow: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#FFFFFF', borderRadius: 12,
    borderWidth: 1.5, borderColor: '#E5E7EB',
    paddingHorizontal: 14, height: 50,
  },
  inputIcon: { marginRight: 10 },
  input: { flex: 1, fontSize: 15, color: DARK },
  eyeBtn: { padding: 4 },

  signInBtn: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
    backgroundColor: ORANGE, borderRadius: 14, paddingVertical: 15, marginTop: 8,
    shadowColor: ORANGE, shadowOpacity: 0.35, shadowRadius: 12,
    shadowOffset: { width: 0, height: 4 }, elevation: 6,
  },
  signInBtnDisabled: { opacity: 0.6 },
  signInBtnText: { color: '#fff', fontSize: 16, fontWeight: '800', letterSpacing: 0.3 },

  footer: { textAlign: 'center', color: '#9CA3AF', fontSize: 12, marginTop: 28 },
});
