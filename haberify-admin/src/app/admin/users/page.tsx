"use client";
import { useEffect, useState } from "react";
import { API } from "@/lib/api";

export default function UsersPage(){
    const [rows,setRows]=useState<any[]>([]);
    const [role,setRole]=useState("editor");

    const load=async()=>{ const r = await API.get("/admin/users"); setRows(r.data?.data ?? []); };
    const assign=async(id:number)=>{ await API.post(`/admin/users/${id}/assign-role`, { role }); load(); };

    useEffect(()=>{ load(); },[]);

    return (
        <div className="space-y-3">
            <h1 className="text-xl font-semibold">Kullanıcılar</h1>
            <div className="flex items-center gap-2">
                <span>Rol:</span>
                <select className="border p-2 rounded" value={role} onChange={e=>setRole(e.target.value)}>
                    <option value="admin">admin</option>
                    <option value="editor">editor</option>
                    <option value="author">author</option>
                </select>
            </div>
            <table className="w-full bg-white border">
                <thead><tr className="bg-gray-50 text-left">
                    <th className="p-2">ID</th><th>Ad</th><th>E-posta</th><th>Roller</th><th></th>
                </tr></thead>
                <tbody>{rows.map(u=>(
                    <tr key={u.id} className="border-t">
                        <td className="p-2">{u.id}</td>
                        <td className="p-2">{u.name}</td>
                        <td className="p-2">{u.email}</td>
                        <td className="p-2 text-sm">{(u.roles||[]).map((r:any)=>r.name).join(", ")}</td>
                        <td className="p-2 text-right">
                            <button className="px-3 py-1 border rounded" onClick={()=>assign(u.id)}>Rol Ata</button>
                        </td>
                    </tr>
                ))}</tbody>
            </table>
        </div>
    );
}
