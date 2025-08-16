"use client";
import { useEffect, useState } from "react";

export default function Comments({ articleId }:{articleId:number}) {
    const [items,setItems]=useState<any[]>([]);
    const [body,setBody]=useState(""); const [name,setName]=useState(""); const [email,setEmail]=useState("");
    const base = (process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api/v1").replace(/\/+$/,'');
    const load = async()=>{ const r = await fetch(`${base}/articles/${articleId}/comments`, { cache:"no-store" }); setItems((await r.json())?.data ?? []); };
    useEffect(()=>{ load(); },[articleId]);

    const submit = async()=>{
        if (!body.trim()) return;
        const r = await fetch(`${base}/articles/${articleId}/comments`, {
            method:"POST", headers:{ "Content-Type":"application/json" }, body: JSON.stringify({ body, name, email })
        });
        if (r.ok) { setBody(""); await load(); alert("Gönderildi, onay bekliyor."); } else alert("Hata");
    };

    return (
        <section className="space-y-4">
            <h3 className="font-semibold text-lg">Yorumlar</h3>
            <ul className="space-y-3">
                {items.map(c=>(
                    <li key={c.id} className="bg-white border rounded p-3">
                        <div className="text-sm text-gray-600">{c.name ?? "Anonim"} • {c.created_at}</div>
                        <p className="text-sm mt-1">{c.body}</p>
                    </li>
                ))}
                {!items.length && <li className="text-sm text-gray-500">Henüz yorum yok.</li>}
            </ul>

            <div className="bg-white border rounded p-4 space-y-2">
                <h4 className="font-medium">Yorum yaz</h4>
                <div className="grid grid-cols-2 gap-2">
                    <input className="border p-2 rounded" placeholder="Ad (opsiyonel)" value={name} onChange={e=>setName(e.target.value)} />
                    <input className="border p-2 rounded" placeholder="E-posta (opsiyonel)" value={email} onChange={e=>setEmail(e.target.value)} />
                </div>
                <textarea className="border p-2 rounded w-full" rows={4} placeholder="Mesaj" value={body} onChange={e=>setBody(e.target.value)} />
                <button className="px-3 py-2 bg-black text-white rounded" onClick={submit}>Gönder</button>
            </div>
        </section>
    );
}
