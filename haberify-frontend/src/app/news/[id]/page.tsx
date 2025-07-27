'use client';

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { fetchNewsDetail } from "@/services/newsApi";
import { fetchComments, addComment } from "@/services/commentApi";
import { useAuth } from "@/contexts/AuthContext";

type Comment = {
    id: number;
    content: string;
    user: { name: string };
    created_at: string;
};

export default function NewsDetailPage() {
    const { id } = useParams<{ id: string }>();
    const [news, setNews] = useState<any>(null);
    const [comments, setComments] = useState<Comment[]>([]);
    const [comment, setComment] = useState("");
    const [error, setError] = useState("");
    const { user } = useAuth();

    useEffect(() => {
        fetchNewsDetail(Number(id)).then(setNews);
        fetchComments(Number(id)).then(setComments);
    }, [id]);

    const handleAddComment = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!comment.trim()) return;
        try {
            await addComment(Number(id), comment);
            setComment("");
            fetchComments(Number(id)).then(setComments);
        } catch {
            setError("Yorum eklenemedi. Giriş yaptığınızdan emin olun.");
        }
    };

    if (!news) return <div>Yükleniyor...</div>;

    return (
        <div className="bg-white p-6 rounded-xl shadow">
            {news.image && (
                <img src={news.image} alt={news.title} className="rounded mb-4 max-h-72 w-full object-cover" />
            )}
            <h1 className="text-2xl font-bold mb-2">{news.title}</h1>
            <div className="mb-4 text-gray-700">{news.content}</div>
            <hr className="my-6" />
            <h3 className="text-xl font-semibold mb-3">Yorumlar</h3>
            {comments.length === 0 && <div>Henüz yorum yok.</div>}
            <ul className="mb-6">
                {comments.map((c) => (
                    <li key={c.id} className="mb-3 p-2 border-b">
                        <div className="font-medium">{c.user?.name || "Anonim"}</div>
                        <div className="text-gray-800">{c.content}</div>
                        <div className="text-xs text-gray-500">{new Date(c.created_at).toLocaleString()}</div>
                    </li>
                ))}
            </ul>
            {user ? (
                <form onSubmit={handleAddComment} className="flex gap-2">
                    <input
                        value={comment}
                        onChange={e => setComment(e.target.value)}
                        placeholder="Yorumunuzu yazın..."
                        className="flex-1 border rounded px-3 py-2"
                    />
                    <button className="bg-blue-600 text-white px-4 rounded" type="submit">Ekle</button>
                </form>
            ) : (
                <div className="text-gray-600">Yorum eklemek için giriş yapınız.</div>
            )}
            {error && <div className="text-red-500 mt-2">{error}</div>}
        </div>
    );
}
