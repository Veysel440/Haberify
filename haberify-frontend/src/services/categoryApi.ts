import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
});

export const fetchCategories = async () => {
    const res = await api.get("/public-categories");
    return res.data.data;
};
