'use client';

import { useEffect, useState } from "react";
import axios from "axios";

export default function AdminStatsPage() {
    const [stats, setStats] = useState<any>(null);

    useEffect(() => {
        axios.get(
            process.env.NEXT_PUBLIC_API_URL + "/admin/stats",
            { withCredentials: true }
        ).then(res => setStats(res.data));
    }, []);

    if (!stats) return <div>Yükleniyor...</div>;

    return (
        <div className="max-w-3xl mx-auto mt-8">
            <h1 className="text-2xl font-bold mb-6">Admin İstatistikler</h1>
            <div className="grid gap-4 mb-8">
                <div className="bg-gray-100 p-4 rounded shadow">
                    <b>Kullanıcı Sayısı:</b> {stats.total_users}
                </div>
                <div className="bg-gray-100 p-4 rounded shadow">
                    <b>Haber Sayısı:</b> {stats.total_news}
                </div>
                <div className="bg-gray-100 p-4 rounded shadow">
                    <b>Yorum Sayısı:</b> {stats.total_comments}
                </div>
            </div>
            <h2 className="text-xl font-semibold mt-6 mb-2">En Popüler Haberler (Okunma)</h2>
            <ul className="mb-6">
                {stats.popular_news.map((news: any) => (
                    <li key={news.id}>{news.title} — {news.views} okunma</li>
                ))}
            </ul>
            <h2 className="text-xl font-semibold mt-6 mb-2">En Çok Favorilenen Haberler</h2>
            <ul className="mb-6">
                {stats.most_favorited.map((news: any) => (
                    <li key={news.id}>{news.title} — {news.favorites_count} favori</li>
                ))}
            </ul>
            <h2 className="text-xl font-semibold mt-6 mb-2">En Çok Şikayet Edilen Yorumlar</h2>
            <ul>
                {stats.most_reported_comments.map((comment: any) => (
                    <li key={comment.id}>"{comment.content.slice(0, 40)}..." — {comment.reports_count} şikayet</li>
                ))}
            </ul>
        </div>
    );
}
