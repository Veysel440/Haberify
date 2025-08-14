import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api/v1",
    withCredentials: true,
});

export const setToken = (t?: string) => {
    if (t) api.defaults.headers.common["Authorization"] = `Bearer ${t}`;
    else delete api.defaults.headers.common["Authorization"];
};

export const pickData = <T = any>(r: any): T => r?.data?.data ?? r?.data;

export default api;   // default export
export const API = api;
