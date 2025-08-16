"use client";
import { useEffect, useState } from "react";
import { API } from "@/lib/api";

export default function MenusPage(){
    const [name,setName]=useState("main");
    const [json,setJson]=useState<string>("");

    const load=async()=>{
        const r = await API.get(`/menus/${name}`);
        setJson(JSON.stringify(r.data?.data ?? {}, null, 2));
    };
    useEffect(()=>{ load(); },[]);

    const save=async()=>{
        await API.put(`/menus/${name}`, JSON.parse(json||"{}"));
        alert("Kaydedildi");
    };

    return (
        <div className="space-y-3">
            <h1 className="text-xl font-semibold">Menüler</h1>
            <div className="flex items-center gap-2">
                <input className="border p-2 rounded" value={name} onChange={e=>setName(e.target.value)} />
                <button className="px-3 py-2 border rounded" onClick={load}>Yükle</button>
            </div>
            <textarea className="w-full h-[420px] border p-3 rounded font-mono text-sm" value={json} onChange={e=>setJson(e.target.value)} />
            <div className="flex gap-2">
                <button className="px-3 py-2 bg-black text-white rounded" onClick={save}>Kaydet</button>
            </div>
        </div>
    );
}
