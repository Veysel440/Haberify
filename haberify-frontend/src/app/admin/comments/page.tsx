'use client';

import { useEffect, useState } from "react";
import axios from "axios";

export default function AdminCommentsPage() {
    const [comments, setComments] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    const fetchComments = () => {
        axios.get(
            process.env.NEXT_PUBLIC_API_URL + "/admin/comments",
            { withCredentials: true }
        ).then(res => setComments(res.data.data)).finally(() => setLoading(false));
    };

    useEffect(() => { fetchComments(); }, []);

    const handleApprove = async (id: number) => {
        await axios.post(process.env.NEXT_PUBLIC_API_URL + `/admin/comments/${id}/approve`, {}, { withCredentials: true });
        fetchComments();
    };

    const handleDelete = async (id: number) => {
        if (!window.confirm("Yorumu silmek istediğinize emin misiniz?")) return;
        await axios.delete(process.env.NEXT_PUBLIC_API_URL + `/admin/comments/${id}`, { withCredentials: true });
        fetchComments();
    };

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <div className="max-w-3xl mx-auto mt-8">
            <h1 className="text-2xl font-bold mb-6">Yorum Yönetimi</h1>
            <table className="w-full border mb-6">
                <thead>
                <tr className="bg-gray-100">
                    <th className="py-2 px-4 text-left">Kullanıcı</th>
                    <th className="py-2 px-4 text-left">İçerik</th>
                    <th className="py-2 px-4 text-left">Durum</th>
                    <th className="py-2 px-4"></th>
                </tr>
                </thead>
                <tbody>
                {comments.map(c => (
                    <tr key={c.id}>
                        <td className="border-t px-4 py-2">{c.user?.name || "Anonim"}</td>
                        <td className="border-t px-4 py-2">{c.content.slice(0, 40)}</td>
                        <td className="border-t px-4 py-2">
                            {c.status === "pending" ? "Beklemede" : c.status === "approved" ? "Onaylı" : "Reddedildi"}
                        </td>
                        <td className="border-t px-4 py-2">
                            {c.status !== "approved" && (
                                <button
                                    className="bg-green-600 text-white px-3 py-1 rounded mr-2"
                                    onClick={() => handleApprove(c.id)}
                                >
                                    Onayla
                                </button>
                            )}
                            <button
                                className="bg-red-600 text-white px-3 py-1 rounded"
                                onClick={() => handleDelete(c.id)}
                            >
                                Sil
                            </button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}
