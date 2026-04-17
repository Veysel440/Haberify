"use client";
import { Suspense, useState } from "react";
import { useSearchParams } from "next/navigation";
import { API, setToken } from "@/lib/api";

const SAFE_REDIRECT = /^\/(admin|profile)(\/|$|\?)/;

const resolveNext = (raw: string | null): string => {
    if (!raw) return "/admin";
    return SAFE_REDIRECT.test(raw) ? raw : "/admin";
};

function LoginForm() {
    const params = useSearchParams();
    const next = resolveNext(params.get("next"));

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [tmp, setTmp] = useState<string>();
    const [code, setCode] = useState("");

    const finishLogin = (token?: string) => {
        if (!token) return;
        setToken(token);
        location.href = next;
    };

    const doLogin = async () => {
        const response = await API.post("/auth/login", { email, password });
        const data = response.data?.data;
        if (data?.token) finishLogin(data.token);
        else if (data?.requires_2fa) setTmp(data.tmp);
    };

    const verify2fa = async () => {
        const response = await API.post("/auth/2fa/verify", { tmp, code });
        finishLogin(response.data?.data?.token);
    };

    return !tmp ? (
        <>
            <h1 className="text-xl font-semibold">Giriş</h1>
            <input className="border p-2 rounded w-full" placeholder="E-posta" value={email} onChange={e => setEmail(e.target.value)} />
            <input className="border p-2 rounded w-full" type="password" placeholder="Şifre" value={password} onChange={e => setPassword(e.target.value)} />
            <button className="bg-black text-white px-4 py-2 rounded" onClick={doLogin}>Giriş</button>
        </>
    ) : (
        <>
            <h1 className="text-xl font-semibold">2FA Doğrulama</h1>
            <input className="border p-2 rounded w-full" placeholder="6 haneli kod" value={code} onChange={e => setCode(e.target.value)} />
            <button className="bg-black text-white px-4 py-2 rounded" onClick={verify2fa}>Doğrula</button>
        </>
    );
}

export default function LoginPage() {
    return (
        <main className="max-w-sm mx-auto mt-24 space-y-3">
            <Suspense fallback={null}>
                <LoginForm />
            </Suspense>
        </main>
    );
}
