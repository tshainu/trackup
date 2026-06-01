/**
 * Themed in-app toast / confirm dialog — replaces all Alert.alert calls.
 *
 * Usage (toast):
 *   const { showToast, ToastHost } = useToast();
 *   showToast('GPS Updated', 'Location saved', 'success');
 *
 * Usage (confirm):
 *   showToast('Sign Out', 'Are you sure?', 'warning', () => doSignOut());
 *
 * Put <ToastHost /> at the root of your screen (inside the fragment / View).
 */
import React, { useState, useCallback, useRef } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  Modal, Animated,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../lib/colors';

const ORANGE = '#E8490F';

type ToastType = 'success' | 'error' | 'warning' | 'info' | 'gps';

interface ToastConfig {
  title: string;
  message?: string;
  type: ToastType;
  /** If provided → shows Cancel + Confirm buttons (confirm dialog) */
  onConfirm?: () => void;
  confirmLabel?: string;
}

const TYPE_META: Record<ToastType, { icon: string; iconColor: string; accent: string }> = {
  success: { icon: 'checkmark-circle', iconColor: Colors.success,    accent: Colors.success },
  error:   { icon: 'close-circle',     iconColor: Colors.danger,     accent: Colors.danger  },
  warning: { icon: 'alert-circle',     iconColor: Colors.warning,    accent: Colors.warning },
  info:    { icon: 'information-circle', iconColor: Colors.info,     accent: Colors.info    },
  gps:     { icon: 'navigate-circle',  iconColor: ORANGE,            accent: ORANGE         },
};

export function useToast() {
  const [config, setConfig] = useState<ToastConfig | null>(null);
  const [visible, setVisible] = useState(false);
  const scale = useRef(new Animated.Value(0.85)).current;

  const showToast = useCallback(
    (
      title: string,
      message?: string,
      type: ToastType = 'info',
      onConfirm?: () => void,
      confirmLabel?: string,
    ) => {
      setConfig({ title, message, type, onConfirm, confirmLabel });
      setVisible(true);
      Animated.spring(scale, {
        toValue: 1,
        useNativeDriver: true,
        friction: 6,
        tension: 120,
      }).start();
    },
    [scale],
  );

  const hide = useCallback(() => {
    Animated.timing(scale, {
      toValue: 0.85,
      duration: 150,
      useNativeDriver: true,
    }).start(() => {
      setVisible(false);
      setConfig(null);
    });
  }, [scale]);

  const ToastHost = useCallback(() => {
    if (!config) return null;
    const meta = TYPE_META[config.type];
    const isConfirm = !!config.onConfirm;

    return (
      <Modal visible={visible} transparent animationType="fade" statusBarTranslucent>
        <View style={styles.overlay}>
          <Animated.View style={[styles.card, { transform: [{ scale }] }]}>
            {/* Accent top bar */}
            <View style={[styles.accentBar, { backgroundColor: meta.accent }]} />

            {/* Icon */}
            <View style={[styles.iconWrap, { backgroundColor: meta.accent + '18' }]}>
              <Ionicons name={meta.icon as any} size={36} color={meta.accent} />
            </View>

            {/* Text */}
            <Text style={styles.title}>{config.title}</Text>
            {config.message ? <Text style={styles.message}>{config.message}</Text> : null}

            {/* Buttons */}
            <View style={styles.btnContainer}>
              {isConfirm ? (
                <View style={styles.btnRow}>
                  <TouchableOpacity style={[styles.btnBase, styles.cancelBtn, { flex: 1 }]} onPress={hide}>
                    <Text style={styles.cancelBtnText}>Cancel</Text>
                  </TouchableOpacity>
                  <TouchableOpacity
                    style={[styles.btnBase, { backgroundColor: meta.accent, flex: 1 }]}
                    onPress={() => { hide(); config.onConfirm?.(); }}
                  >
                    <Text style={styles.confirmBtnText}>{config.confirmLabel ?? 'Confirm'}</Text>
                  </TouchableOpacity>
                </View>
              ) : (
                <TouchableOpacity
                  style={[styles.btnBase, styles.singleBtn, { backgroundColor: meta.accent }]}
                  onPress={hide}
                >
                  <Text style={styles.confirmBtnText}>OK</Text>
                </TouchableOpacity>
              )}
            </View>
          </Animated.View>
        </View>
      </Modal>
    );
  }, [config, visible, scale, hide]);

  return { showToast, ToastHost };
}

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(15,23,42,0.55)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 28,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 20,
    width: '100%',
    maxWidth: 340,
    overflow: 'hidden',
    alignItems: 'center',
    paddingBottom: 22,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.15,
    shadowRadius: 24,
    elevation: 12,
  },
  accentBar: {
    height: 4,
    width: '100%',
    marginBottom: 24,
  },
  iconWrap: {
    width: 72,
    height: 72,
    borderRadius: 36,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  title: {
    fontSize: 18,
    fontWeight: '800',
    color: Colors.textPrimary,
    textAlign: 'center',
    paddingHorizontal: 20,
  },
  message: {
    fontSize: 14,
    color: Colors.textSecondary,
    textAlign: 'center',
    marginTop: 8,
    lineHeight: 20,
    paddingHorizontal: 20,
  },
  btnContainer: {
    marginTop: 22,
    width: '100%',
    paddingHorizontal: 20,
  },
  btnRow: {
    flexDirection: 'row',
    gap: 10,
  },
  btnBase: {
    borderRadius: 10,
    paddingVertical: 13,
    alignItems: 'center',
    justifyContent: 'center',
  },
  singleBtn: {
    width: '100%',
  },
  cancelBtn: {
    backgroundColor: Colors.bg,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  cancelBtnText: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textSecondary,
  },
  confirmBtnText: {
    fontSize: 15,
    fontWeight: '700',
    color: '#ffffff',
    letterSpacing: 0.3,
  },
});
