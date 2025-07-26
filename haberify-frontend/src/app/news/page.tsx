'use client';

import { useEffect, useState } from 'react';
import NewsCard from '@/components/NewsCard';
import { fetchNews } from '@/services/newsApi';

type News = {
    id: number;
    title: string;
    excerpt: string;
    image?: string | null;
};

export default function NewsPage() {
    const [news, setNews] = useState<News[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchNews().then(setNews).finally(() => setLoading(false));
    }, []);

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <div>
            <h1 className="text-2xl font-bold mb-4">Haberler</h1>
            {news.length === 0 && <div>Hiç haber yok.</div>}
            {news.map((item) => (
                <NewsCard key={item.id} {...item} />
            ))}
        </div>
    );
}
