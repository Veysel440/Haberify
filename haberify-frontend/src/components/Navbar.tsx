'use client';

import Link from "next/link";
import { useAuth } from "@/contexts/AuthContext";

export default function Navbar() {
    const { user, logout } = useAuth();

    return (
        <nav className="bg-white shadow px-4 py-3 flex justify-between items-center">
            <div className="text-xl font-bold text-blue-600">
                <Link href="/">Haberify</Link>
            </div>
            <div className="flex gap-4 items-center">
                <Link href="/news" className="hover:text-blue-500">Haberler</Link>
                {user ? (
                    <>
                        <span className="font-medium">{user.name}</span>
                        {user.role === "admin" && (
                            <Link href="/admin" className="hover:text-blue-500">Admin</Link>
                        )}
                        <button onClick={logout} className="ml-2 text-sm bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">Çıkış</button>
                    </>
                ) : (
                    <>
                        <Link href="/login" className="hover:text-blue-500">Giriş Yap</Link>
                        <Link href="/register" className="hover:text-blue-500">Kayıt Ol</Link>
                    </>
                )}
            </div>
        </nav>
    );
}
