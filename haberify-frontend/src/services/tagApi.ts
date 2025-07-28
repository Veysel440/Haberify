import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
});

export const fetchTags = async () => {
    const res = await api.get("/public-tags");
    return res.data.data;
};
