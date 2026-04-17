"use client";
import { useEffect, useState } from "react";
import { setToken } from "@/lib/api";

export default function RequireAuth({ children }: { children: React.ReactNode }) {
    const [ready, setReady] = useState(false);

    useEffect(() => {
        const token = localStorage.getItem("token");
        if (!token) {
            location.href = "/login";
            return;
        }
        setToken(token);
        setReady(true);
    }, []);

    if (!ready) return null;
    return <>{children}</>;
}
