import axios from 'axios';

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api',
    withCredentials: true,
});

export const fetchNews = async () => {
    const res = await api.get('/news');
    return res.data.data;
};
