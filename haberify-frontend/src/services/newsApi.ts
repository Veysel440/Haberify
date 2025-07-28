import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api",
    withCredentials: true,
});

export const fetchNewsDetail = async (id: number) => {
    const res = await api.get(`/news/${id}`);
    return res.data.data;
};

export const toggleFavorite = async (newsId: number, isFav: boolean) => {
    if (isFav) {
        await api.delete(`/news/${newsId}/favorite`);
    } else {
        await api.post(`/news/${newsId}/favorite`);
    }
};
export const fetchNews = async (params?: { categoryId?: string | null, tagId?: string | null }) => {
    let url = "/news";
    const queries = [];
    if (params?.categoryId) queries.push(`category_id=${params.categoryId}`);
    if (params?.tagId) queries.push(`tag_id=${params.tagId}`);
    if (queries.length) url += `?${queries.join("&")}`;

    const res = await api.get(url);
    return res.data.data;
};
