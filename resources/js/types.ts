// Temel model tipleri
export interface Table {
  id: number;
  name: string;
  description: string | null;
  deleted_at: string | null;
  created_at: string;
  updated_at: string;
  orders_count?: number;
  active_order?: Order;
}

export interface Category {
  id: number;
  name: string;
  is_active: boolean;
  deleted_at: string | null;
  created_at: string;
  updated_at: string;
  products_count?: number;
}

export interface Product {
  id: number;
  category_id: number;
  name: string;
  image_path: string | null;
  description: string | null;
  price: number;
  stock: number;
  is_active: boolean;
  deleted_at: string | null;
  created_at: string;
  updated_at: string;
  category?: Category;
}

export interface OrderItem {
  id: number;
  order_id: number;
  product_id: number;
  quantity: number;
  price: number;
  note: string | null;
  status: 'hazırlanıyor' | 'hazır' | 'teslim' | 'iptal';
  created_at: string;
  updated_at: string;
  product?: Product;
}

export interface Order {
  id: number;
  table_id: number;
  user_id: number;
  note: string | null;
  status: 'hazırlanıyor' | 'hazır' | 'teslim' | 'kapandı' | 'ödendi' | 'iptal';
  total: number | null;
  closed_at: string | null;
  created_at: string;
  updated_at: string;
  table?: Table;
  user?: any;
  items?: OrderItem[];
  payments?: Payment[];
}

export interface Payment {
  id: number;
  order_id: number;
  amount: number;
  payment_method: 'nakit' | 'kredi_kartı' | 'online' | 'fiş' | 'diğer';
  status: 'beklemede' | 'ödendi' | 'iptal';
  note: string | null;
  paid_at: string | null;
  created_at: string;
  updated_at: string;
  order?: Order;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: any;
}

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  avatar?: string | null;
}
