'use client';

import { useAuth } from "@/contexts/AuthContext";
import { useState } from "react";
import { updateProfile } from "@/services/authApi";

export default function ProfilePage() {
    const { user } = useAuth();
    const [name, setName] = useState(user?.name || "");
    const [message, setMessage] = useState("");
    const [loading, setLoading] = useState(false);

    if (!user) return <div>Giriş yapmalısınız.</div>;

    const handleUpdate = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setMessage("");
        try {
            await updateProfile({ name });
            setMessage("Profil güncellendi!");

        } catch {
            setMessage("Güncelleme sırasında hata oluştu.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="max-w-lg mx-auto bg-white p-8 rounded-xl shadow mt-8">
            <h2 className="text-xl font-bold mb-4">Profilim</h2>
            <form onSubmit={handleUpdate} className="space-y-4">
                <input
                    type="text"
                    className="w-full border p-2 rounded"
                    value={name}
                    onChange={e => setName(e.target.value)}
                />
                <input
                    type="email"
                    className="w-full border p-2 rounded bg-gray-100"
                    value={user.email}
                    disabled
                />
                <button
                    className="w-full bg-blue-600 text-white py-2 rounded font-semibold"
                    disabled={loading}
                >
                    {loading ? "Kaydediliyor..." : "Kaydet"}
                </button>
                {message && <div className="text-green-600">{message}</div>}
            </form>
        </div>
    );
}
