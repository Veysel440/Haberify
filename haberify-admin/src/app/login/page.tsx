"use client";
import { useState } from "react";
import { api, setToken } from "@/lib/api";

export default function LoginPage() {
    const [email,setEmail]=useState(""); const [password,setPassword]=useState("");
    const [tmp,setTmp]=useState<string|undefined>();
    const [code,setCode]=useState("");

    const doLogin = async () => {
        const { data } = await api.post("/auth/login",{email,password});
        if (data?.data?.token) { setToken(data.data.token); localStorage.setItem("token",data.data.token); window.location.href="/admin"; }
        else if (data?.data?.requires_2fa) { setTmp(data.data.tmp); }
    };

    const verify2fa = async () => {
        const { data } = await api.post("/auth/2fa/verify",{ tmp, code });
        setToken(data.data.token); localStorage.setItem("token",data.data.token); window.location.href="/admin";
    };

    return (
        <main style={{maxWidth:340,margin:"80px auto"}}>
            {!tmp ? (
                <>
                    <h2>Giriş</h2>
                    <input placeholder="email" value={email} onChange={e=>setEmail(e.target.value)} />
                    <input type="password" placeholder="şifre" value={password} onChange={e=>setPassword(e.target.value)} />
                    <button onClick={doLogin}>Giriş</button>
                </>
            ) : (
                <>
                    <h2>2FA</h2>
                    <input placeholder="6 haneli kod" value={code} onChange={e=>setCode(e.target.value)} />
                    <button onClick={verify2fa}>Doğrula</button>
                </>
            )}
        </main>
    );
}
