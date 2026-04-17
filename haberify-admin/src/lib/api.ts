import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api/v1",
    withCredentials: true,
});

export const AUTH_COOKIE = "auth_token";
const COOKIE_MAX_AGE_SECONDS = 60 * 60 * 24 * 7;

const isBrowser = () => typeof document !== "undefined";

const writeCookie = (token: string) => {
    if (!isBrowser()) return;
    const secure = location.protocol === "https:" ? "; Secure" : "";
    document.cookie = `${AUTH_COOKIE}=${encodeURIComponent(token)}; Path=/; Max-Age=${COOKIE_MAX_AGE_SECONDS}; SameSite=Lax${secure}`;
};

const clearCookie = () => {
    if (!isBrowser()) return;
    document.cookie = `${AUTH_COOKIE}=; Path=/; Max-Age=0; SameSite=Lax`;
};

export const setToken = (token?: string) => {
    if (token) {
        api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        if (isBrowser()) localStorage.setItem("token", token);
        writeCookie(token);
    } else {
        delete api.defaults.headers.common["Authorization"];
        if (isBrowser()) localStorage.removeItem("token");
        clearCookie();
    }
};

export const clearAuth = () => setToken(undefined);

export const pickData = <T = any>(r: any): T => r?.data?.data ?? r?.data;

export default api;
export const API = api;
