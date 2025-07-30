'use client';

import { useEffect, useState } from "react";
import { fetchHistory } from "@/services/newsApi";
import NewsCard from "@/components/NewsCard";
import { useAuth } from "@/contexts/AuthContext";

export default function HistoryPage() {
    const { user } = useAuth();
    const [history, setHistory] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!user) return;
        fetchHistory().then(setHistory).finally(() => setLoading(false));
    }, [user]);

    if (!user) return <div>Giriş yapmanız gerekmektedir.</div>;
    if (loading) return <div>Yükleniyor...</div>;

    return (
        <div className="max-w-3xl mx-auto mt-8">
            <h1 className="text-xl font-bold mb-4">Okuma Geçmişim</h1>
            {history.length === 0 && <div>Hiç haber görüntülemediniz.</div>}
            <div className="grid gap-4">
                {history.map(item => <NewsCard key={item.id} {...item} />)}
            </div>
        </div>
    );
}
