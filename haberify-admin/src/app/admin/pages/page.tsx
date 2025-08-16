"use client";
import { useEffect, useState } from "react";
import { API, pickData } from "@/lib/api";

export default function PagesAdmin(){
    const [slug,setSlug]=useState("");
    const [page,setPage]=useState<any|null>(null);
    const [title,setTitle]=useState(""); const [body,setBody]=useState(""); const [is_active,setActive]=useState(true);

    const load=async()=>{
        const r = await API.get(`/pages/${slug}`);
        const p = pickData(r); setPage(p);
        setTitle(p?.title ?? ""); setBody(p?.body ?? ""); setActive(!!p?.is_active);
    };

    const create=async()=>{
        const r = await API.post("/pages",{ title, slug, body, is_active });
        const p = r.data?.data; setPage(p);
        alert("Oluşturuldu");
    };

    const update=async()=>{
        if (!page?.id) return;
        await API.put(`/pages/${page.id}`, { title, body, is_active });
        alert("Güncellendi");
    };

    const del=async()=>{
        if (!page?.id) return;
        await API.delete(`/pages/${page.id}`); setPage(null); alert("Silindi");
    };

    useEffect(()=>{ /* boş */ },[]);

    return (
        <div className="space-y-4">
            <h1 className="text-xl font-semibold">Sayfalar</h1>
            <div className="flex gap-2">
                <input className="border p-2 rounded" placeholder="slug" value={slug} onChange={e=>setSlug(e.target.value)} />
                <button className="px-3 py-2 border rounded" onClick={load}>Yükle</button>
            </div>

            <div className="space-y-2">
                <input className="border p-2 rounded w-full" placeholder="Başlık" value={title} onChange={e=>setTitle(e.target.value)} />
                <textarea className="border p-2 rounded w-full h-64" placeholder="İçerik" value={body} onChange={e=>setBody(e.target.value)} />
                <label className="flex items-center gap-2 text-sm"><input type="checkbox" checked={is_active} onChange={e=>setActive(e.target.checked)} /> Aktif</label>
                <div className="flex gap-2">
                    <button className="px-3 py-2 bg-black text-white rounded" onClick={page?update:create}>{page?"Güncelle":"Oluştur"}</button>
                    {page && <button className="px-3 py-2 border rounded text-red-600" onClick={del}>Sil</button>}
                </div>
            </div>
        </div>
    );
}
