import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
    withCredentials: true,
});

export const login = async (email: string, password: string) => {
    const res = await api.post("/login", { email, password });
    return res.data;
};

export const register = async (name: string, email: string, password: string) => {
    const res = await api.post("/register", { name, email, password });
    return res.data;
};

export const fetchProfile = async () => {
    const res = await api.get("/profile");
    return res.data;
};

export const logout = async () => {
    const res = await api.post("/logout");
    return res.data;
};
