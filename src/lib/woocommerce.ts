/**
 * WooCommerce REST API client.
 *
 * In production, configure environment variables:
 *   NEXT_PUBLIC_WOOCOMMERCE_URL — WordPress/WooCommerce base URL
 *   WOOCOMMERCE_CONSUMER_KEY   — REST API consumer key
 *   WOOCOMMERCE_CONSUMER_SECRET — REST API consumer secret
 */

const BASE_URL = process.env.NEXT_PUBLIC_WOOCOMMERCE_URL || "https://api.example.com";
const CONSUMER_KEY = process.env.WOOCOMMERCE_CONSUMER_KEY || "";
const CONSUMER_SECRET = process.env.WOOCOMMERCE_CONSUMER_SECRET || "";

interface WooCommerceRequestOptions {
  endpoint: string;
  method?: "GET" | "POST" | "PUT" | "DELETE";
  params?: Record<string, string>;
  body?: unknown;
}

export async function woocommerceFetch<T>({
  endpoint,
  method = "GET",
  params = {},
  body,
}: WooCommerceRequestOptions): Promise<T> {
  const url = new URL(`/wp-json/wc/v3${endpoint}`, BASE_URL);
  url.searchParams.set("consumer_key", CONSUMER_KEY);
  url.searchParams.set("consumer_secret", CONSUMER_SECRET);
  Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));

  const res = await fetch(url.toString(), {
    method,
    headers: { "Content-Type": "application/json" },
    body: body ? JSON.stringify(body) : undefined,
    next: { revalidate: 60 },
  });

  if (!res.ok) {
    throw new Error(`WooCommerce API error: ${res.status} ${res.statusText}`);
  }

  return res.json() as Promise<T>;
}

// Product types matching WooCommerce REST API response
export interface WooProduct {
  id: number;
  name: string;
  slug: string;
  price: string;
  regular_price: string;
  description: string;
  short_description: string;
  categories: { id: number; name: string; slug: string }[];
  images: { id: number; src: string; alt: string }[];
  attributes: { name: string; options: string[] }[];
  meta_data: { key: string; value: string }[];
}

export interface WooCategory {
  id: number;
  name: string;
  slug: string;
  description: string;
  count: number;
}

// API methods
export async function getProducts(params?: Record<string, string>) {
  return woocommerceFetch<WooProduct[]>({
    endpoint: "/products",
    params: { per_page: "100", ...params },
  });
}

export async function getProduct(slug: string) {
  const products = await woocommerceFetch<WooProduct[]>({
    endpoint: "/products",
    params: { slug },
  });
  return products[0] || null;
}

export async function getCategories() {
  return woocommerceFetch<WooCategory[]>({
    endpoint: "/products/categories",
    params: { per_page: "100" },
  });
}

export async function getProductsByCategory(categorySlug: string) {
  const categories = await woocommerceFetch<WooCategory[]>({
    endpoint: "/products/categories",
    params: { slug: categorySlug },
  });
  if (!categories[0]) return [];
  return woocommerceFetch<WooProduct[]>({
    endpoint: "/products",
    params: { category: String(categories[0].id), per_page: "100" },
  });
}
