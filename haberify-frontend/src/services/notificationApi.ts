import axios from "axios";
const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
    withCredentials: true,
});

export const fetchNotifications = async () => {
    const res = await api.get("/notifications");
    return res.data.data;
};

export const markNotificationAsRead = async (id: number) => {
    await api.post(`/notifications/${id}/read`);
};
