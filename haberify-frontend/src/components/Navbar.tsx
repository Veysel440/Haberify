'use client';

import Link from "next/link";
import { useAuth } from "@/contexts/AuthContext";
import { useNotifications } from "@/contexts/NotificationContext";
import { useState } from "react";

export default function Navbar() {
    const { user, logout } = useAuth();
    const { notifications, reload } = useNotifications();
    const unreadCount = notifications.filter(n => !n.read).length;

    // Popup için state
    const [notifOpen, setNotifOpen] = useState(false);

    return (
        <nav className="bg-white shadow px-4 py-3 flex justify-between items-center">
            <div className="flex items-center gap-4">
                <Link href="/" className="text-xl font-bold text-blue-600">Haberify</Link>
                <Link href="/news" className="hover:text-blue-500 font-semibold">Haberler</Link>
            </div>
            <div className="flex gap-4 items-center">
                {user && (
                    <div className="relative">
                        <button
                            onClick={() => { reload(); setNotifOpen(o => !o); }}
                            className="relative text-xl focus:outline-none"
                        >
                            <span role="img" aria-label="Bildirim">🔔</span>
                            {unreadCount > 0 && (
                                <span className="absolute -top-1 -right-2 bg-red-500 text-white text-xs rounded-full px-1">
                                    {unreadCount}
                                </span>
                            )}
                        </button>
                        {/* Popup Bildirim Listesi */}
                        {notifOpen && (
                            <div className="absolute right-0 mt-2 w-72 max-h-80 overflow-y-auto bg-white border shadow-xl rounded-xl z-50">
                                <div className="p-3 border-b font-semibold text-gray-700">Bildirimler</div>
                                {notifications.length === 0 && (
                                    <div className="p-4 text-gray-400 text-center">Hiç bildiriminiz yok.</div>
                                )}
                                {notifications.map(n => (
                                    <div
                                        key={n.id}
                                        className={`px-4 py-3 border-b last:border-0 cursor-pointer ${n.read ? "bg-gray-50" : "bg-blue-50 font-semibold"}`}
                                        {...(n.message ? { title: n.message } : {})}
                                    >
                                        <div className="text-sm">{n.title}</div>
                                        {n.message && <div className="text-xs text-gray-500 mt-1">{n.message}</div>}
                                        <div className="text-xs text-gray-400 mt-1">{new Date(n.created_at).toLocaleString()}</div>
                                    </div>
                                ))}
                                <div className="p-2 text-xs text-center text-blue-700 cursor-pointer hover:underline"
                                     onClick={() => setNotifOpen(false)}>
                                    Kapat
                                </div>
                            </div>
                        )}
                    </div>
                )}
                {user ? (
                    <>
                        <Link href="/profile" className="hover:text-blue-600 font-semibold">
                            Profilim
                        </Link>
                        <span className="font-medium">{user.name}</span>
                        {user.role === "admin" && (
                            <Link href="/admin" className="hover:text-red-500 font-semibold">
                                Admin Paneli
                            </Link>
                        )}
                        <button
                            onClick={logout}
                            className="ml-2 text-sm bg-gray-200 px-3 py-1 rounded hover:bg-gray-300 transition"
                        >
                            Çıkış
                        </button>
                    </>
                ) : (
                    <>
                        <Link href="/login" className="hover:text-blue-600 font-semibold">Giriş Yap</Link>
                        <Link href="/register" className="hover:text-blue-600 font-semibold">Kayıt Ol</Link>
                    </>
                )}
            </div>
        </nav>
    );
}
