"use client";
import { useState } from "react";
import { API } from "@/lib/api";

const KEYS = ["site.name","site.description","seo.meta_title","seo.meta_description","ads.enabled","home.featured_limit"];

export default function SettingsPage(){
    const [rows,setRows]=useState<Record<string,string>>({});
    const load=async(key:string)=>{
        const r = await API.get(`/settings/${key}`); setRows(s=>({...s,[key]: String(r.data?.data?.value ?? "")}));
    };
    const save=async(key:string)=>{ await API.put(`/settings/${key}`, { value: rows[key] }); };

    return (
        <div className="space-y-4">
            <h1 className="text-xl font-semibold">Ayarlar</h1>
            {KEYS.map(k=>(
                <div key={k} className="flex gap-2 items-center">
                    <div className="w-64 text-sm">{k}</div>
                    <input className="border p-2 rounded w-96" value={rows[k] ?? ""} onChange={e=>setRows(s=>({...s,[k]:e.target.value}))}/>
                    <button className="px-2 py-1 border rounded" onClick={()=>load(k)}>Yükle</button>
                    <button className="px-2 py-1 bg-black text-white rounded" onClick={()=>save(k)}>Kaydet</button>
                </div>
            ))}
        </div>
    );
}
