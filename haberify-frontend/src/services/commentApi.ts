import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
    withCredentials: true,
});

export const fetchComments = async (newsId: number) => {
    const res = await api.get(`/comments?news_id=${newsId}`);
    return res.data.data;
};

export const addComment = async (newsId: number, content: string) => {
    const res = await api.post("/comments", { news_id: newsId, content });
    return res.data.data;
};
