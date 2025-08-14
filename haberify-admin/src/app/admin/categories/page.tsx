"use client";
import { useEffect, useState } from "react";
import { API, pickData } from "@/lib/api";

export default function CategoriesPage(){
    const [rows,setRows]=useState<any[]>([]);
    const [name,setName]=useState(""); const [desc,setDesc]=useState("");
    const load=async()=>{ const r = await API.get("/categories"); setRows(pickData(r)??[]); };
    useEffect(()=>{ load(); },[]);
    const create = async()=>{ await API.post("/categories",{ name, description: desc }); setName(""); setDesc(""); load(); };
    const update = async(id:number, patch:any)=>{ await API.put(`/categories/${id}`, patch); load(); };
    const del = async(id:number)=>{ await API.delete(`/categories/${id}`); load(); };
    return (
        <div className="space-y-4">
            <h1 className="text-xl font-semibold">Kategoriler</h1>
            <div className="flex gap-2">
                <input className="border p-2 rounded" placeholder="Ad" value={name} onChange={e=>setName(e.target.value)} />
                <input className="border p-2 rounded w-80" placeholder="Açıklama" value={desc} onChange={e=>setDesc(e.target.value)} />
                <button className="bg-black text-white px-3 rounded" onClick={create}>Ekle</button>
            </div>
            <table className="w-full bg-white border">
                <thead><tr className="bg-gray-50 text-left"><th className="p-2">ID</th><th>Ad</th><th>Açıklama</th><th>Aktif</th><th></th></tr></thead>
                <tbody>{rows.map(r=>(
                    <tr key={r.id} className="border-t">
                        <td className="p-2">{r.id}</td>
                        <td className="p-2"><input className="border p-1 rounded w-full" defaultValue={r.name} onBlur={e=>update(r.id,{name:e.target.value})}/></td>
                        <td className="p-2"><input className="border p-1 rounded w-full" defaultValue={r.description||""} onBlur={e=>update(r.id,{description:e.target.value})}/></td>
                        <td className="p-2"><input type="checkbox" defaultChecked={r.is_active} onChange={e=>update(r.id,{is_active:e.target.checked})}/></td>
                        <td className="p-2 text-right"><button className="text-red-600" onClick={()=>del(r.id)}>Sil</button></td>
                    </tr>
                ))}</tbody>
            </table>
        </div>
    );
}
