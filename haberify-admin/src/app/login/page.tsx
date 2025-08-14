"use client";
import { useState } from "react";
import { API, setToken, pickData } from "@/lib/api";

export default function LoginPage() {
    const [email,setEmail]=useState(""); const [password,setPassword]=useState("");
    const [tmp,setTmp]=useState<string>(); const [code,setCode]=useState("");

    const doLogin = async () => {
        const r = await API.post("/auth/login",{email,password});
        const d = r.data?.data;
        if (d?.token) { setToken(d.token); localStorage.setItem("token", d.token); location.href="/admin"; }
        else if (d?.requires_2fa) setTmp(d.tmp);
    };
    const verify2fa = async () => {
        const r = await API.post("/auth/2fa/verify",{ tmp, code });
        const t = r.data?.data?.token; setToken(t); localStorage.setItem("token",t); location.href="/admin";
    };

    return (
        <main className="max-w-sm mx-auto mt-24 space-y-3">
            {!tmp ? (
                <>
                    <h1 className="text-xl font-semibold">Giriş</h1>
                    <input className="border p-2 rounded w-full" placeholder="E-posta" value={email} onChange={e=>setEmail(e.target.value)} />
                    <input className="border p-2 rounded w-full" type="password" placeholder="Şifre" value={password} onChange={e=>setPassword(e.target.value)} />
                    <button className="bg-black text-white px-4 py-2 rounded" onClick={doLogin}>Giriş</button>
                </>
            ) : (
                <>
                    <h1 className="text-xl font-semibold">2FA Doğrulama</h1>
                    <input className="border p-2 rounded w-full" placeholder="6 haneli kod" value={code} onChange={e=>setCode(e.target.value)} />
                    <button className="bg-black text-white px-4 py-2 rounded" onClick={verify2fa}>Doğrula</button>
                </>
            )}
        </main>
    );
}
