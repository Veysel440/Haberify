'use client';

import { useEffect, useState } from "react";
import { fetchFavorites } from "@/services/newsApi";
import NewsCard from "@/components/NewsCard";
import { useAuth } from "@/contexts/AuthContext";

export default function FavoritesPage() {
    const { user } = useAuth();
    const [favorites, setFavorites] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!user) return;
        fetchFavorites().then(setFavorites).finally(() => setLoading(false));
    }, [user]);

    if (!user) return <div>Giriş yapmanız gerekmektedir.</div>;
    if (loading) return <div>Yükleniyor...</div>;

    return (
        <div className="max-w-3xl mx-auto mt-8">
            <h1 className="text-xl font-bold mb-4">Favori Haberlerim</h1>
            {favorites.length === 0 && <div>Henüz favori eklemediniz.</div>}
            <div className="grid gap-4">
                {favorites.map(item => <NewsCard key={item.id} {...item} />)}
            </div>
        </div>
    );
}
