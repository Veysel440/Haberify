import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
    withCredentials: true,
});

export const sendResetLink = async (email: string) => {
    await api.post("/password/email", { email });
};

export const resetPassword = async (data: {
    email: string;
    token: string;
    password: string;
    password_confirmation: string;
}) => {
    await api.post("/password/reset", data);
};

export const fetchProfile = async () => {
    const res = await api.get("/profile");
    return res.data.data;
};

export const updateProfile = async (data: { name: string; avatar?: File | null }) => {
    const formData = new FormData();
    formData.append("name", data.name);
    if (data.avatar) formData.append("avatar", data.avatar);

    const res = await api.post("/profile", formData, {
        headers: { "Content-Type": "multipart/form-data" }
    });
    return res.data.data;
};
export const login = async (email: string, password: string) => {
    const res = await api.post("/login", { email, password });
    return res.data;
};

export const register = async (name: string, email: string, password: string) => {
    const res = await api.post("/register", { name, email, password });
    return res.data;
};


export const logout = async () => {
    const res = await api.post("/logout");
    return res.data;
};

export const changePassword = async (payload: { old_password: string; new_password: string }) => {
    await api.post("/profile/change-password", payload);
};
