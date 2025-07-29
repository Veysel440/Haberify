'use client';

import { useState } from "react";
import { sendResetLink } from "@/services/authApi";

export default function ForgotPasswordPage() {
    const [email, setEmail] = useState("");
    const [success, setSuccess] = useState("");
    const [error, setError] = useState("");

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError("");
        setSuccess("");
        try {
            await sendResetLink(email);
            setSuccess("Sıfırlama linki e-posta adresinize gönderildi.");
        } catch (err: any) {
            setError("E-posta bulunamadı veya bir hata oluştu.");
        }
    };

    return (
        <div className="max-w-md mx-auto mt-10 bg-white shadow rounded p-8">
            <h2 className="text-xl font-bold mb-4">Şifremi Unuttum</h2>
            <form onSubmit={handleSubmit} className="space-y-4">
                <input
                    type="email"
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    placeholder="E-posta adresiniz"
                    className="w-full border px-3 py-2 rounded"
                    required
                />
                <button
                    className="w-full bg-blue-600 text-white py-2 rounded font-semibold"
                    type="submit"
                >
                    Sıfırlama Linki Gönder
                </button>
            </form>
            {success && <div className="mt-4 text-green-600">{success}</div>}
            {error && <div className="mt-4 text-red-600">{error}</div>}
        </div>
    );
}
