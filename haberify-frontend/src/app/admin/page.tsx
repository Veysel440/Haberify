'use client';

import { useAuth } from "@/contexts/AuthContext";
import { useRouter } from "next/navigation";
import { useEffect } from "react";

export default function AdminPage() {
    const { user, loading } = useAuth();
    const router = useRouter();

    useEffect(() => {
        if (!loading && (!user || user.role !== "admin")) {
            router.replace("/");
        }
    }, [user, loading, router]);

    if (loading) return <div>Yükleniyor...</div>;
    if (!user || user.role !== "admin") return null;

    return (
        <div>
            <h1 className="text-2xl font-bold mb-4">Admin Paneli</h1>
            {/* Admin sayfası içeriği */}
        </div>
    );
}
