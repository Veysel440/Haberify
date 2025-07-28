'use client';

import { useEffect, useState } from "react";
import { useSearchParams, useRouter } from "next/navigation";
import NewsCard from "@/components/NewsCard";
import { fetchNews } from "@/services/newsApi";
import { fetchCategories } from "@/services/categoryApi";
import { fetchTags } from "@/services/tagApi";

type News = {
    id: number;
    title: string;
    excerpt: string;
    image?: string | null;
    category?: { id: number; name: string };
    tags?: { id: number; name: string }[];
    is_favorite?: boolean;
    favorite_count?: number;
};

export default function NewsPage() {
    const searchParams = useSearchParams();
    const router = useRouter();
    const categoryId = searchParams.get("category");
    const tagId = searchParams.get("tag");

    const [news, setNews] = useState<News[]>([]);
    const [categories, setCategories] = useState<any[]>([]);
    const [tags, setTags] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetchNews({ categoryId, tagId })
            .then(setNews)
            .finally(() => setLoading(false));
        fetchCategories().then(setCategories);
        fetchTags().then(setTags);
    }, [categoryId, tagId]);

    const handleCategoryChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const value = e.target.value;
        router.push(value ? `/news?category=${value}` : "/news");
    };

    const handleTagChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const value = e.target.value;
        router.push(value ? `/news?tag=${value}` : "/news");
    };

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <div>
            <h1 className="text-2xl font-bold mb-4">Haberler</h1>
            <div className="flex gap-3 mb-4">
                <select
                    value={categoryId || ""}
                    onChange={handleCategoryChange}
                    className="border px-2 py-1 rounded"
                >
                    <option value="">Tüm Kategoriler</option>
                    {categories.map((cat) => (
                        <option value={cat.id} key={cat.id}>{cat.name}</option>
                    ))}
                </select>
                <select
                    value={tagId || ""}
                    onChange={handleTagChange}
                    className="border px-2 py-1 rounded"
                >
                    <option value="">Tüm Etiketler</option>
                    {tags.map((tag) => (
                        <option value={tag.id} key={tag.id}>#{tag.name}</option>
                    ))}
                </select>
            </div>
            {news.length === 0 && <div>Hiç haber yok.</div>}
            {news.map((item) => (
                <NewsCard key={item.id} {...item} />
            ))}
        </div>
    );
}
