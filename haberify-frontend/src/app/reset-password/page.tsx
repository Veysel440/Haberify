'use client';

import { useState } from "react";
import { useSearchParams, useRouter } from "next/navigation";
import { resetPassword } from "@/services/authApi";

export default function ResetPasswordPage() {
    const searchParams = useSearchParams();
    const router = useRouter();

    const [password, setPassword] = useState("");
    const [passwordConfirmation, setPasswordConfirmation] = useState("");
    const [success, setSuccess] = useState("");
    const [error, setError] = useState("");

    const token = searchParams.get("token") || "";
    const email = searchParams.get("email") || "";

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError("");
        setSuccess("");
        try {
            await resetPassword({ email, token, password, password_confirmation: passwordConfirmation });
            setSuccess("Şifre başarıyla güncellendi! Giriş yapabilirsiniz.");
            setTimeout(() => router.push("/login"), 2000);
        } catch (err: any) {
            setError("Şifre sıfırlanamadı. Token veya e-posta yanlış olabilir.");
        }
    };

    return (
        <div className="max-w-md mx-auto mt-10 bg-white shadow rounded p-8">
            <h2 className="text-xl font-bold mb-4">Yeni Şifre Belirle</h2>
            <form onSubmit={handleSubmit} className="space-y-4">
                <input type="password" value={password} onChange={e => setPassword(e.target.value)} placeholder="Yeni şifre" className="w-full border px-3 py-2 rounded" required minLength={6} />
                <input type="password" value={passwordConfirmation} onChange={e => setPasswordConfirmation(e.target.value)} placeholder="Yeni şifre tekrar" className="w-full border px-3 py-2 rounded" required minLength={6} />
                <button className="w-full bg-blue-600 text-white py-2 rounded font-semibold" type="submit">
                    Şifreyi Sıfırla
                </button>
            </form>
            {success && <div className="mt-4 text-green-600">{success}</div>}
            {error && <div className="mt-4 text-red-600">{error}</div>}
        </div>
    );
}
