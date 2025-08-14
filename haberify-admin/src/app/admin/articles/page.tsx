"use client";
import { useEffect, useMemo, useState } from "react";
import { API, pickData } from "@/lib/api";
import Link from "next/link";

type Row = { id:number; title:string; slug:string; status:string; is_featured:boolean; published_at?:string|null; };

export default function ArticlesPage() {
    const [rows,setRows]=useState<Row[]>([]);
    const [page,setPage]=useState(1);
    const [meta,setMeta]=useState<any>({});
    const [selected,setSelected]=useState<Set<number>>(new Set());

    const load = async (p=1) => {
        const r = await API.get(`/admin/articles?page=${p}`);
        setRows(pickData<any>(r)?.data ?? []);
        setMeta(pickData<any>(r)?.meta ?? {});
    };
    useEffect(()=>{ load(page); },[page]);

    const toggle = (id:number)=> setSelected(s=>{ const n=new Set(s); n.has(id)?n.delete(id):n.add(id); return n; });
    const allIds = useMemo(()=>rows.map(r=>r.id),[rows]);
    const toggleAll = ()=> setSelected(s=> s.size===rows.length ? new Set() : new Set(allIds));

    const bulk = async (action:"publish"|"unpublish"|"feature"|"unfeature"|"delete") => {
        if (!selected.size) return;
        await API.post("/admin/articles/bulk", { ids: Array.from(selected), action });
        setSelected(new Set()); load(page);
    };

    return (
        <main>
            <div className="flex items-center justify-between mb-4">
                <h1 className="text-xl font-semibold">Makaleler</h1>
                <Link className="px-3 py-2 bg-black text-white rounded" href="/admin/articles/new">Yeni</Link>
            </div>

            <div className="mb-3 flex gap-2">
                <button className="btn" onClick={()=>bulk("publish")}>Yayınla</button>
                <button className="btn" onClick={()=>bulk("unpublish")}>Taslağa Al</button>
                <button className="btn" onClick={()=>bulk("feature")}>Öne Çıkar</button>
                <button className="btn" onClick={()=>bulk("unfeature")}>ÖÇ Kaldır</button>
                <button className="btn !bg-red-600" onClick={()=>bulk("delete")}>Sil</button>
                <style jsx>{`.btn{background:#111;color:#fff;padding:.5rem .75rem;border-radius:.375rem}.btn+.btn{margin-left:.5rem}`}</style>
            </div>

            <table className="w-full bg-white border">
                <thead><tr className="text-left bg-gray-50">
                    <th className="p-2"><input type="checkbox" checked={selected.size===rows.length && rows.length>0} onChange={toggleAll}/></th>
                    <th className="p-2">ID</th><th>Başlık</th><th>Durum</th><th>ÖÇ</th><th>Yayın</th><th></th>
                </tr></thead>
                <tbody>
                {rows.map(r=>(
                    <tr key={r.id} className="border-t">
                        <td className="p-2"><input type="checkbox" checked={selected.has(r.id)} onChange={()=>toggle(r.id)}/></td>
                        <td className="p-2">{r.id}</td>
                        <td className="p-2">{r.title}</td>
                        <td className="p-2">{r.status}</td>
                        <td className="p-2">{r.is_featured ? "Evet":"-"}</td>
                        <td className="p-2">{r.published_at ?? "-"}</td>
                        <td className="p-2 text-right">
                            <Link className="text-blue-600" href={`/admin/articles/${r.slug}`}>Düzenle</Link>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>

            <div className="flex items-center gap-2 mt-4">
                <button disabled={page<=1} onClick={()=>setPage(p=>p-1)} className="px-3 py-1 border rounded">Geri</button>
                <span>Sayfa {meta.current_page ?? page} / {meta.last_page ?? "?"}</span>
                <button disabled={(meta.current_page ?? page) >= (meta.last_page ?? page)} onClick={()=>setPage(p=>p+1)} className="px-3 py-1 border rounded">İleri</button>
            </div>
        </main>
    );
}
