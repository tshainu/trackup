import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import * as SecureStore from 'expo-secure-store';
import { setAuthToken, Shop, TechEmployee } from './api';

type Role = 'admin' | 'technician' | null;

interface AdminSession {
  role: 'admin';
  token: string;
  shop: Shop;
}

interface TechSession {
  role: 'technician';
  token: string;
  employee: TechEmployee;
  shop_code: string;
}

type Session = AdminSession | TechSession | null;

interface AuthContextType {
  session: Session;
  isLoading: boolean;
  signIn: (session: Session) => Promise<void>;
  signOut: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | null>(null);

const SESSION_KEY = 'trackup_session';

export function AuthProvider({ children }: { children: ReactNode }) {
  const [session, setSession] = useState<Session>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    loadSession();
  }, []);

  async function loadSession() {
    try {
      const stored = await SecureStore.getItemAsync(SESSION_KEY);
      if (stored) {
        const s: Session = JSON.parse(stored);
        setSession(s);
        if (s) setAuthToken(s.token);
      }
    } catch {
      // ignore
    } finally {
      setIsLoading(false);
    }
  }

  async function signIn(s: Session) {
    setSession(s);
    if (s) {
      setAuthToken(s.token);
      await SecureStore.setItemAsync(SESSION_KEY, JSON.stringify(s));
    }
  }

  async function signOut() {
    setSession(null);
    setAuthToken(null);
    await SecureStore.deleteItemAsync(SESSION_KEY);
  }

  return (
    <AuthContext.Provider value={{ session, isLoading, signIn, signOut }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
