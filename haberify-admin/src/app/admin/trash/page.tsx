"use client";
import { useEffect, useState } from "react";
import { API } from "@/lib/api";

type Kind = "articles"|"categories"|"tags"|"comments"|"pages";

export default function TrashPage(){
    const [kind,setKind]=useState<Kind>("articles");
    const [rows,setRows]=useState<any[]>([]); const [meta,setMeta]=useState<any>({current_page:1});
    const load=async(p=1)=>{ const r = await API.get(`/admin/trash/${kind}?page=${p}`); setRows(r.data?.data?.data ?? []); setMeta(r.data?.data?.meta ?? {}); };
    useEffect(()=>{ load(1); },[kind]);

    const restore = async(id:number)=>{ await API.post(`/admin/trash/${kind}/${id}/restore`); load(meta.current_page||1); };
    const forceDel = async(id:number)=>{ await API.delete(`/admin/trash/${kind}/${id}/force`); load(meta.current_page||1); };

    return (
        <div>
            <h1 className="text-xl font-semibold mb-3">Çöp Kutusu</h1>
            <div className="flex gap-2 mb-3">
                {(["articles","categories","tags","comments","pages"] as Kind[]).map(k=>
                    <button key={k} className={`px-3 py-1 border rounded ${k===kind?'bg-black text-white':''}`} onClick={()=>setKind(k)}>{k}</button>
                )}
            </div>
            <table className="w-full bg-white border">
                <thead><tr className="bg-gray-50 text-left"><th className="p-2">ID</th><th>Başlık/Ad</th><th>Silinme</th><th></th></tr></thead>
                <tbody>{rows.map((r:any)=>(
                    <tr key={r.id} className="border-t">
                        <td className="p-2">{r.id}</td>
                        <td className="p-2">{r.title ?? r.name ?? r.slug}</td>
                        <td className="p-2">{r.deleted_at}</td>
                        <td className="p-2 text-right">
                            <button className="px-2 py-1 border rounded mr-2" onClick={()=>restore(r.id)}>Geri Yükle</button>
                            <button className="px-2 py-1 border rounded text-red-600" onClick={()=>forceDel(r.id)}>Kalıcı Sil</button>
                        </td>
                    </tr>
                ))}</tbody>
            </table>
            <div className="flex items-center gap-2 mt-3">
                <button className="px-3 py-1 border rounded" disabled={(meta.current_page||1)<=1} onClick={()=>load((meta.current_page||1)-1)}>Geri</button>
                <span>Sayfa {meta.current_page||1} / {meta.last_page||"?"}</span>
                <button className="px-3 py-1 border rounded" disabled={(meta.current_page||1)>=(meta.last_page||1)} onClick={()=>load((meta.current_page||1)+1)}>İleri</button>
            </div>
        </div>
    );
}
