"use client";
import { useEffect, useState } from "react";
import { setToken } from "@/lib/api";

export default function RequireAuth({ children }:{children: React.ReactNode}) {
    const [ok,setOk]=useState(false);
    useEffect(()=>{
        const t = localStorage.getItem("token");
        if (!t) location.href="/login";
        else { setToken(t); setOk(true); }
    },[]);
    if (!ok) return null;
    return <>{children}</>;
}
