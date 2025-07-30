'use client';

import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import { fetchNewsSearch } from "@/services/newsApi";
import NewsCard from "@/components/NewsCard";

export default function SearchPage() {
    const searchParams = useSearchParams();
    const q = searchParams.get("q") || "";
    const [results, setResults] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (!q) return;
        setLoading(true);
        fetchNewsSearch(q).then(setResults).finally(() => setLoading(false));
    }, [q]);

    return (
        <div className="max-w-3xl mx-auto mt-8">
            <h1 className="text-xl font-bold mb-4">
                Arama Sonuçları {q && <span className="text-gray-500">: "{q}"</span>}
            </h1>
            {loading && <div>Yükleniyor...</div>}
            {!loading && results.length === 0 && <div>Sonuç bulunamadı.</div>}
            <div className="grid gap-4">
                {results.map(item => <NewsCard key={item.id} {...item} />)}
            </div>
        </div>
    );
}
