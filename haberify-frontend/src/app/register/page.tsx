'use client';

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/contexts/AuthContext";

export default function RegisterPage() {
    const [name, setName] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const { register, loading, user } = useAuth();
    const router = useRouter();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError("");
        try {
            await register(name, email, password);
            router.push("/");
        } catch (err: any) {
            setError("Kayıt başarısız. " + (err?.response?.data?.message || ""));
        }
    };

    if (user) router.push("/");

    return (
        <div className="max-w-sm mx-auto bg-white p-8 rounded-xl shadow">
            <h2 className="text-2xl font-bold mb-4">Kayıt Ol</h2>
            <form onSubmit={handleSubmit} className="space-y-4">
                <input type="text" placeholder="Ad Soyad" className="w-full border p-2 rounded"
                       value={name} onChange={e => setName(e.target.value)} required />
                <input type="email" placeholder="E-posta" className="w-full border p-2 rounded"
                       value={email} onChange={e => setEmail(e.target.value)} required />
                <input type="password" placeholder="Şifre" className="w-full border p-2 rounded"
                       value={password} onChange={e => setPassword(e.target.value)} required />
                {error && <div className="text-red-500 text-sm">{error}</div>}
                <button type="submit" className="w-full bg-blue-600 text-white py-2 rounded font-semibold" disabled={loading}>
                    {loading ? "Kayıt Olunuyor..." : "Kayıt Ol"}
                </button>
            </form>
        </div>
    );
}
