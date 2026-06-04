import { Platform } from 'react-native';

// On web (preview), use relative /api/* so requests go through Metro's proxy.
// On native (Expo Go / APK), hit the server directly.
const BASE_URL = Platform.OS === 'web' ? '/api' : 'https://50ktgky-smi88-8081.exp.direct/api';

let authToken: string | null = null;

export function setAuthToken(token: string | null) {
  authToken = token;
}

async function request<T>(
  method: string,
  path: string,
  body?: object,
  params?: Record<string, string | number | undefined>
): Promise<T> {
  const url = new URL(BASE_URL + path);
  if (params) {
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined) url.searchParams.set(k, String(v));
    });
  }

  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };
  if (authToken) headers['Authorization'] = `Bearer ${authToken}`;

  const res = await fetch(url.toString(), {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });

  if (!res.ok) {
    const err = await res.json().catch(() => ({ error: 'Request failed' }));
    throw new Error((err as any).error ?? 'Request failed');
  }

  return res.json();
}

// ── Admin Auth ─────────────────────────────────────────────────────────────
export const adminApi = {
  login: (shop_code: string, username: string, password: string) =>
    request<{ token: string; role: string; shop: Shop }>('POST', '/admin/login', { shop_code, username, password }),

  logout: () => request<{ message: string }>('POST', '/admin/logout'),

  dashboard: () => request<DashboardData>('GET', '/admin/dashboard'),

  employees: () => request<{ employees: Employee[] }>('GET', '/admin/employees'),

  // Job Cards
  jobCards: (params?: { status?: string; search?: string; per_page?: number; page?: number }) =>
    request<PaginatedResponse<JobCard>>('GET', '/admin/job-cards', undefined, params as any),

  jobCard: (id: number) =>
    request<{ job: JobCard }>('GET', `/admin/job-cards/${id}`),

  createJobCard: (data: Partial<JobCard>) =>
    request<{ job: JobCard; message: string }>('POST', '/admin/job-cards', data),

  updateJobCard: (id: number, data: Partial<JobCard>) =>
    request<{ job: JobCard; message: string }>('PUT', `/admin/job-cards/${id}`, data),

  updateJobStatus: (id: number, status: string, reason?: string) =>
    request<{ job: JobCard; message: string }>('PATCH', `/admin/job-cards/${id}/status`, { status, reason }),

  // Field Complaints
  fieldComplaints: (params?: { status?: string; search?: string; per_page?: number; page?: number }) =>
    request<PaginatedResponse<FieldComplaint>>('GET', '/admin/field-complaints', undefined, params as any),

  fieldComplaint: (id: number) =>
    request<{ complaint: FieldComplaint }>('GET', `/admin/field-complaints/${id}`),

  createFieldComplaint: (data: Partial<FieldComplaint>) =>
    request<{ complaint: FieldComplaint; message: string }>('POST', '/admin/field-complaints', data),

  updateFieldComplaint: (id: number, data: Partial<FieldComplaint>) =>
    request<{ complaint: FieldComplaint; message: string }>('PUT', `/admin/field-complaints/${id}`, data),
};

// ── Technician Auth ────────────────────────────────────────────────────────
export const techApi = {
  login: (user_name: string, password: string) =>
    request<{ token: string; employee: TechEmployee }>('POST', '/technician/login', { user_name, password }),

  logout: () => request<{ message: string }>('POST', '/technician/logout'),

  me: () => request<TechEmployee>('GET', '/technician/me'),

  // Jobs
  jobs: () => request<{ jobs: JobCard[]; stats: JobStats }>('GET', '/technician/jobs'),

  allJobs: (status?: string) =>
    request<{ jobs: JobCard[] }>('GET', '/technician/jobs/all', undefined, status ? { status } : {}),

  jobDetail: (id: number) => request<{ job: JobCard }>('GET', `/technician/jobs/${id}`),

  acceptJob: (id: number) => request<{ message: string; job: JobCard }>('POST', `/technician/jobs/${id}/accept`),

  completeJob: (id: number, status: string, remark?: string) =>
    request<{ message: string; job: JobCard }>('POST', `/technician/jobs/${id}/complete`, { status, remark }),

  requestAssistance: (id: number) =>
    request<{ message: string }>('POST', `/technician/jobs/${id}/assist`),

  // Field Jobs
  fieldJobs: () =>
    request<{ jobs: FieldComplaint[]; stats: FieldStats }>('GET', '/technician/field-jobs'),

  allFieldJobs: () =>
    request<{ jobs: FieldComplaint[] }>('GET', '/technician/field-jobs/history'),

  fieldJobDetail: (id: number) =>
    request<{ job: FieldComplaint }>('GET', `/technician/field-jobs/${id}`),

  acceptFieldJob: (id: number) =>
    request<{ message: string; job: FieldComplaint }>('POST', `/technician/field-jobs/${id}/accept`),

  completeFieldJob: (id: number, completion_notes?: string) =>
    request<{ message: string; job: FieldComplaint }>('POST', `/technician/field-jobs/${id}/complete`, { completion_notes }),

  extendFieldJob: (id: number, reason: string) =>
    request<{ message: string; job: FieldComplaint }>('POST', `/technician/field-jobs/${id}/extend`, { reason }),

  cantCompleteFieldJob: (id: number, reason: string) =>
    request<{ message: string; job: FieldComplaint }>('POST', `/technician/field-jobs/${id}/cant-complete`, { reason }),

  updateGps: (id: number, gps_lat: number, gps_lng: number, gps_label?: string) =>
    request<{ message: string; job: FieldComplaint }>('POST', `/technician/field-jobs/${id}/update-gps`, { gps_lat, gps_lng, gps_label }),

  changePassword: (current_password: string, new_password: string) =>
    request<{ message: string }>('POST', '/technician/change-password', { current_password, new_password }),
};

// ── Types ──────────────────────────────────────────────────────────────────
export interface Shop {
  id: number;
  name: string;
  code: string;
  logo: string | null;
  modules: string[];
}

export interface Employee {
  id: number;
  employee_name: string;
  role: string;
  type: string;
  phone: string;
  photo: string | null;
}

export interface TechEmployee {
  id: number;
  name: string;
  user_name: string;
  role: string;
  type: string;
  photo: string | null;
}

export interface JobCard {
  id: number;
  order_no: string;
  customer_name: string;
  phone_no: string;
  customer_address?: string;
  device_name: string;
  device_brand?: string;
  serial_no?: string;
  device_fault?: string;
  status: string;
  priority: string;
  date: string;
  estimated_delivery?: string;
  rupees?: number;
  advance_amount?: number;
  discount?: number;
  paid_amount?: number;
  payment_status?: string;
  grand_total?: number;
  balance?: number;
  subtotal?: number;
  employee_id?: number;
  employee?: { id: number; employee_name: string };
  remark?: string;
  need_assistant?: boolean;
  accessories?: string;
  invoice_items?: InvoiceItem[];
}

export interface InvoiceItem {
  id: number;
  description: string;
  qty: number;
  unit_price: number;
  total: number;
}

export interface FieldComplaint {
  id: number;
  complaint_no: string;
  customer_name: string;
  phone_no: string;
  address?: string;
  service_type_name?: string;
  description?: string;
  status: string;
  priority: string;
  scheduled_date?: string;
  assigned_to?: number;
  assigned_employee?: { id: number; employee_name: string };
  completion_notes?: string;
  gps_lat?: number;
  gps_lng?: number;
  service_charge?: number;
  discount?: number;
  paid_amount?: number;
  grand_total?: number;
}

export interface DashboardData {
  job_stats: {
    total: number;
    pending: number;
    in_progress: number;
    completed: number;
    today: number;
    revenue_today: number;
    revenue_total: number;
  };
  field_stats: {
    total: number;
    pending: number;
    in_progress: number;
    completed: number;
    today: number;
  };
  recent_jobs: JobCard[];
}

export interface JobStats {
  pending: number;
  in_progress: number;
  completed: number;
  total: number;
}

export interface FieldStats {
  new: number;
  assigned: number;
  in_progress: number;
  completed: number;
  pending: number;
  overdue: number;
  total: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  total: number;
  per_page: number;
}
